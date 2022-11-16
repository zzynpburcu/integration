<?php
require_once('../../db.php');
error_reporting(E_ALL);
ini_set("display_errors",1);

function getApiInfo()
{
  global $ozy;
  $query = $ozy->prepare("SELECT * FROM pazaryeri_magaza WHERE pazar_id = ?");
  $query->execute(array(2));
  $column = $query->fetch(PDO::FETCH_ASSOC);
  $apis = [
    'appKey' => $column['api_anahtar'],
    'appSecret' => $column['api_sifre'],
    'supplierId' => $column['api_id']
  ];
  return $apis;
}

$tParams = getApiInfo(); print_r($tParams);
$kullanici_adiniz=$tParams['appKey'];
$sifreniz=$tParams['appSecret'];
$merchant_id=$tParams['supplierId'];


function baglan($url, $kAdi, $kSifre)
{
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $header5 = array(
        'Authorization: Basic ' . base64_encode($kAdi . ':' . $kSifre),
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header5);
    $curl_response = curl_exec($curl);
    $arr2 = json_decode($curl_response, true);
    return $arr2; print_r($arr2);
}
$service_url4 = "https://oms-external.hepsiburada.com/packages/merchantid/" . $merchant_id . "?timespan=96";
$siparisler = baglan($service_url4, $kullanici_adiniz, $sifreniz);
 print("siparisler" . "<pre>" . print_r($siparisler, true) . "</pre>");
foreach ($siparisler as $key => $value) { 
     print("siparisler" . "<pre>" . print_r($value["items"][0]["orderNumber"], true) . "</pre>"); 
     $siparisno=$value["items"][0]["orderNumber"];
    $urunid=$value["items"][0]["lineItemId"]; 
    $adet=$value["items"][0]["quantity"];
    $vergibilgileri=$value["taxNumber"].$value["taxOffice"];
    $adsoyad=$value["recipientName"];
    $telefon=$value["phoneNumber"];
             $email=$value["email"];
             $adres=$value["shippingAddressDetail"];
             $il=$value["shippingCity"];
            $ilce=$value["shippingDistrict"];
         $faturaadres=$value["billingAddress"];
          $faturail=$value["billingCity"];
        $faturailce=$value["billingDistrict"];
         $onayD=$value["status"];
           switch ($onayD) {
    case "Open": $onay="Açık sipariş"; break;
       case "Packaged": $onay="Paketlenmiş sipariş"; break;
        case "CancelledByMerchant": $onay="Merchant tarafından iptal edilmiş sipariş"; break;
         case "Delivered": $onay="Teslim edilmiş sipariş"; break;
          case "InTransit": $onay="Kargoda olan sipariş"; break;
           case "ClaimCreated": $onay="Talep açılmış kalem"; break;
            case "CancelledByCustomer": $onay="Müşteri tarafından iptal edilmiş sipariş"; break;
            case "CancelledBySap": $onay="SAP tarafından iptal edilen sipariş Fraud vb durumlarda oluşur"; break;
        default:
          $onay="";}
           $toplamtutar=$value["totalPrice"]["amount"];
            $uye=$value["customerId"];

          $durum=$onay;
           $kupontutari=$value["items"][0]["totalHBDiscount"];
            $tarih=$value["orderDate"];
         $pazar_id="2";
        $urunkodu=$value["items"][0]["hbSku"];
        $takipno=$value["barcode"];
    $birim_fiyat=$value["items"][0]["merchantTotalPrice"]["amount"];
    $urunadi=$value["items"][0]["productName"];
    $kdvoran=$value["items"][0]["vatRate"];
    $kdv=$value["items"][0]["vat"];
     $service_url = "https://oms-external.hepsiburada.com/orders/merchantid/" . $merchant_id . "/ordernumber/" . $siparisno;
        $siparisİsim = baglan($service_url, $kullanici_adiniz, $sifreniz);
        $musteri_gsm=$siparisİsim["deliveryAddress"]["phoneNumber"]; 
   $siparisSorgula = $ozy->prepare("select count(*) from siparis where siparisno=?");
        $siparisSorgula->execute(array($siparisno));
        $number_of_rows = $siparisSorgula->fetchColumn(); 
        if($number_of_rows==0){
         $siparis = $ozy->prepare("INSERT INTO siparis (siparisno, odemetipi, urunid, adet, uyetip, vergibilgileri, adsoyad, telefon, email, adres, il, ilce, faturatip, faturaadres, faturail, faturailce, onay, aratutar, kdvtutari, kargotutari, havaleindirimtutari, kuponid, kupontutari, cekid, cektutari, kapitutar, toplamtutar, uye, kim, gelenkim, uruntablo, mesaj, durum, ekozellikid, ekozellikadet, tarih, mailtablo, tarihson,pazar_id,takipno) 
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
         $siparisonuc = $siparis->execute(array($siparisno, "", "$urunid", "$adet", 0, "$vergibilgileri", "$adsoyad", "$musteri_gsm", "$email", "$adres", "$il", "$ilce", "", "$faturaadres", "$faturail", "$faturailce", "$durum",0, "$kdv", 0, 0, "", "$kupontutari", 0, 0, 0, "$toplamtutar", "", "", "", "$urunid", "", "$onay", "", "", "$tarih", "", "$tarih","$pazar_id","$takipno"));
         if ($siparisonuc) {
              $s_id = $ozy->lastInsertId();
              $insert_into_siparis_urun = $ozy->prepare("INSERT INTO siparis_urun SET siparis_id=?, urun_id=?, urun_adi=?,birim_fiyat=?,kdv=?,adet=?,urun_kodu=?");
                    $succ = $insert_into_siparis_urun->execute(array($s_id, $urunid, "$urunadi", $toplamtutar,$kdvoran,$adet,$urunkodu));
                     if ($succ)
                        echo "BAŞARILI BAŞARILI BAŞARILI BAŞARILI BAŞARILI" . "<br>";
                    else
                        echo "BAŞARISIZ BAŞARISIZ BAŞARISIZ BAŞARISIZ BAŞARISIZ" . $insert_into_siparis_urun->errorCode() . "<br>";
          }
            
            
        }else{ 
                 $siparis = $ozy->prepare('UPDATE siparis SET durum=? WHERE siparisno=? and pazar_id=?');
         $siparise = $siparis->execute(array( $durum,$siparisno,$pazar_id));
           if ($siparise) {
       print_r("güncelleme başarılı");
           }else{
               print_r("güncelleme başarısız");
           } 
        }
}
