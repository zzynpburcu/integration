<?php

session_start();

ob_start();

ini_set('display_errors', 1);

ini_set('display_startup_errors', 1);

error_reporting(E_ALL);

include "trendyol/vendor/autoload.php";

include('../../db.php');


use IS\PazarYeri\Trendyol\TrendyolClient;

use IS\PazarYeri\Trendyol\Helper\TrendyolException;



function check_api($user, $pass)

{

	if (empty($user)) {

		throw new InvalidArgumentException('API ANAHTARI YOK');
	}

	if (empty($pass)) {

		throw new InvalidArgumentException('API ŞİFRESİ YOK');
	}

	return true;
}





function getApiInfo()

{

	global $db;

	$query = $db->prepare("SELECT * FROM pazaryeri_magaza WHERE pazar_id = ?");



	$query->execute(array(4)); //sabit değişken 1 di

	$coloumns = $query->fetchAll(PDO::FETCH_ASSOC);

	if (!empty($coloumns)) {

		foreach ($coloumns as $column) {

			$result = check_api($column['api_anahtar'], $column['api_sifre']);

			if ($result) {

				$apis[] = [

					'appKey' => $column['api_anahtar'],

					'appSecret' => $column['api_sifre'],

					'supplierId' => $column['api_id']

				];
			}
		}
	} else {

		return false;
	}

	return $apis;
}



$tParams = getApiInfo();



if ($tParams == "") {

	exit;
}





if (!empty($tParams)) {

	foreach ($tParams as $tParam) {



		$trendyol = new TrendyolClient();

		for ($i = 0; $i < 5; $i++) {
			

			$trendyol->setSupplierId($tParam['supplierId']);

			$trendyol->setUsername($tParam['appKey']);

			$trendyol->setPassword($tParam['appSecret']);

			$orderDetails = getOrderList($trendyol,$i);

			if(!$orderDetails){
				break;
			}

			saveOrderDetails($orderDetails, $trendyol, $tParam);
		}
	}
}



function getOrderList($trendyol,$page)

{



	$orderList = $trendyol->order->orderList(

		array(

			// Belirli bir tarihten sonraki siparişleri getirir. Timestamp olarak gönderilmelidir.	

			'startDate'          => time() - (5 * 24 * 60 * 60),

			// Belirtilen tarihe kadar olan siparişleri getirir. Timestamp olarak gönderilmelidir ve startDate ve endDate aralığı en fazla 2 hafta olmalıdır

			'endDate'            => time(),

			// Sadece belirtilen sayfadaki bilgileri döndürür	

			'page'               => $page,

			// Bir sayfada listelenecek maksimum adeti belirtir. (Max 200)

			'size'               => 200,

			// Sadece belirli bir sipariş numarası verilerek o siparişin bilgilerini getirir	

			'orderNumber'        => '',

			// Siparişlerin statülerine göre bilgileri getirir.	(Created, Picking, Invoiced, Shipped, Cancelled, Delivered, UnDelivered, Returned, Repack, UnSupplied)

			'status'             => '',

			// Siparişler neye göre sıralanacak? (PackageLastModifiedDate, CreatedDate)

			'orderByField'       => 'CreatedDate',

			// Siparişleri sıralama türü? (ASC, DESC)

			'orderByDirection'   => 'DESC',

			// Paket numarasıyla sorgu atılır.	

			'shipmentPackagesId' => '',

		)

	);



	return $orderList;
}



function saveOrderDetails($orderList, $trendyol, $api)

{

	global $db;

	foreach ($orderList->content as $content) {

		print("Content" . "<pre>" . print_r($content, true) . "</pre>");



		if (!empty($content->orderNumber) && !empty($content->shipmentAddress->fullName) && !empty($content->id)) {



			$check_s_id = $db->prepare("SELECT * FROM pazaryeri_siparis WHERE siparis_no=? and mukellef_id=?");

			$check_s_id->execute(array($content->orderNumber, $_SESSION["mukellef_no"]));

			$row = $check_s_id->fetch(PDO::FETCH_ASSOC);

			if (!empty($row)) {



				$siparisNo = $content->orderNumber;
				if ($content->shipmentPackageStatus == "Cancelled") {


					$olusturmaYontemi = $db->query("SELECT * FROM fatura_olusturma_yontemi WHERE siparis_no='$siparisNo' and pazar_id=4")->fetch(PDO::FETCH_ASSOC);

					if ($olusturmaYontemi != " ") {
						$fatura_id = $olusturmaYontemi["fatura_id"];
						$faturaBilgi = $db->query("SELECT * FROM giden_fatura_taslak WHERE id='$fatura_id' and durum=0")->fetch(PDO::FETCH_ASSOC);
						if ($faturaBilgi != "") {
							siparisSil($siparisNo);
							faturaTaslakSil($fatura_id);
						}
					} else {

						siparisSil($siparisNo);
					}
				} else {
					$siparisID = $row["id"];
					$kargo_kodu  = $content->cargoTrackingNumber;
					$sirket_adi  = $content->cargoProviderName;
					echo $siparisID . " id'li kargo(" . $kargo_kodu . ") Güncellendi<br>";
					$kargoUpdate = $db->prepare("UPDATE pazaryeri_kargo set kargo_kodu=?,sirket_adi=? where siparis_id=?");
					$kargoUpdate->execute(array($kargo_kodu, $sirket_adi, $siparisID));
					// echo $content->shipmentPackageStatus . "<br>";
				}
				$olusturmaYontemi = $db->query("SELECT * FROM fatura_olusturma_yontemi WHERE siparis_no='$siparisNo'")->fetch(PDO::FETCH_ASSOC);

				continue;
			}


			if ($content->shipmentPackageStatus != "Cancelled") {
				//SİPARİSLER TABLOSU İÇİN GEREKEN BİLGİLER

				$siparis_no = $content->orderNumber;

				$musteri_adsoyad = $content->shipmentAddress->fullName;





				$siparis_tarih = date('Y-m-d', $content->orderDate / 1000);





				$siparis_tutar = NULL;

				if (!empty($content->totalPrice))

					$siparis_tutar = $content->totalPrice;



				$kullanici_no = 0;

				if (!empty($content->customerId))

					$kullanici_no = $content->customerId;





				$magaza = $db->prepare("SELECT id, pazar_id FROM pazaryeri_magaza WHERE mukellef_id=? and api_anahtar=?");

				$magaza->execute(array($_SESSION["mukellef_no"], $api['appKey']));

				$row = $magaza->fetch(PDO::FETCH_ASSOC);

				$magaza_id = $row['id'];

				$pazar_id = $row['pazar_id'];



				$siparis_no_query = $db->prepare("SELECT * FROM pazaryeri_siparis WHERE siparis_no = ? AND mukellef_id = ?");

				$siparis_no_query->execute(array($siparis_no, $_SESSION['mukellef_no']));

				$sorgu = $siparis_no_query->fetch();



				if (!empty($sorgu))

					continue;



				$query = $db->prepare("INSERT INTO pazaryeri_siparis SET mukellef_id=?, siparis_no=?, musteri_adsoyad=?, magaza_id=?, pazar_id=?, siparis_tarih=?, siparis_tutar=?, kullanici_no=?, onay_durum=1");



				$insert = $query->execute(array($_SESSION["mukellef_no"], $siparis_no, $musteri_adsoyad, $magaza_id, $pazar_id, $siparis_tarih, $siparis_tutar, $kullanici_no));



				if ($insert) {

					$s_id = $db->lastInsertId(); //siparis_id



					//kargo tablosu

					if (!empty($content->cargoTrackingNumber) && !empty($content->cargoProviderName)) {



						//KARGO TABLOSU İÇİN GEREKEN BİLGİLER

						$sirket_id  = NULL;

						$takip_no  = NULL;

						$kargo_kodu  = $content->cargoTrackingNumber;



						$sirket_adi  = $content->cargoProviderName;



						$sirket_adi_kisa = NULL;



						$sirket_numarasi = NULL;



						$kargo_tarihi = date('Y-m-d');



						echo "KARGO TABLOSU" . "<br>";



						try {

							$insert_into_siparis_urun = $db->prepare("INSERT INTO pazaryeri_kargo SET siparis_id=?, sirket_adi=?, kargo_kodu=?");

							$succ = $insert_into_siparis_urun->execute(array($s_id, $sirket_adi, $kargo_kodu));

							if ($succ)

								echo "BAŞARILI BAŞARILI BAŞARILI BAŞARILI BAŞARILI" . "<br>";

							else

								echo "BAŞARISIZ BAŞARISIZ BAŞARISIZ BAŞARISIZ BAŞARISIZ" . $insert_into_siparis_urun->errorCode() . "<br>";
						} catch (PDOException $e) {

							$e->getMessage();
						}
					}



					foreach ($content->lines as $line) {

						if (!empty($line->productCode) && !empty($line->productName) && !empty($line->barcode)) {

							//SİPARİS_URUN TABLOSU İÇİN GEREKEN BİLGİLER

							$stok_kodu = $line->productCode;

							$urun_adi = $line->productName;



							$adet = NULL;

							if (!empty($line->quantity))

								$adet = $line->quantity;



							$komisyon = NULL;

							$urunBilgisi = $line->barcode;

							$fiyat = NULL;

							if (!empty($line->price))

								$fiyat = $line->price;



							$barcode = $line->barcode;

							$product = $trendyol->product->filterProducts(

								array(

									// Ürün onaylı ya da onaysız kontrolü için kullanılır. Onaylı için true gönderilmelidir	

									'approved'      => false,

									// Tekil barkod sorgulamak için gönderilmelidir	

									'barcode'       => $barcode,

									//Sadece belirtilen sayfadaki bilgileri döndürür.

									'page'          => 0,

									// Tarih filtresinin çalışacağı tarih CREATED_DATE ya da LAST_MODIFIED_DATE gönderilebilir	

									'dateQueryType' => 'CREATED_DATE',

									// Bir sayfada listelenecek maksimum adeti belirtir.	

									'size'          => 50

								)

							);



							$resim_url = NULL;

							foreach ($product->content as $content) {

								if (!empty($content->images[0]->url))

									$resim_url = $content->images[0]->url;
							}







							try {

								$insert_into_pazaryeri_siparisler_urun = $db->prepare("INSERT INTO pazaryeri_siparis_urun SET siparis_id=?, stok_kodu=?, urun_adi=?, adet=?, komisyon=?, fiyat=?, resim=?,urun_bilgisi=?");

								$insert_into_pazaryeri_siparisler_urun->execute(array($s_id, $stok_kodu, $urun_adi, $adet, $komisyon, $fiyat, $resim_url, $urunBilgisi));
							} catch (PDOException $e) {

								$e->getMessage();
							}
						}
					}
				}
			}
		}
	}
}
function siparisSil($siparisNo)
{
	global $db;
	$siparisBilgisi = $db->query("SELECT * FROM pazaryeri_siparis WHERE siparis_no='$siparisNo'")->fetch(PDO::FETCH_ASSOC);
	$siparisID = $siparisBilgisi["id"];

	$deleteSiparis = $db->prepare("DELETE FROM pazaryeri_siparis where id=?");
	$deleteSiparis->execute(array($siparisID));

	$deleteSiparisUrun = $db->prepare("DELETE FROM pazaryeri_siparis_urun where siparis_id=?");
	$deleteSiparisUrun->execute(array($siparisID));
}

function faturaTaslakSil($fatura_id)
{
	global $db;
	$deleteFaturaKalem = $db->prepare("DELETE FROM giden_fatura_mal_hizmet where fatura_id=?");
	$deleteFaturaKalem->execute(array($fatura_id));

	$deleteFatura = $db->prepare("DELETE FROM giden_fatura_taslak where id=?");
	$deleteFatura->execute(array($fatura_id));
}
