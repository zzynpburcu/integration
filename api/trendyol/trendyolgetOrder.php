<?php

require_once('../../db.php');
use IS\PazarYeri\Trendyol\TrendyolClient;
include "trendyol/vendor/autoload.php";

 function getApiInfo()
 {
 	global $ozy;
   $query = $ozy->prepare("SELECT * FROM pazaryeri_magaza WHERE  pazar_id = ?");
    $query->execute(array(4));
    $coloumns = $query->fetchAll(PDO::FETCH_ASSOC);
 	if (!empty($coloumns)) {
 		foreach ($coloumns as $column) {
 				$apis = [
 					'appKey' => $column['api_anahtar'],
 					'appSecret' => $column['api_sifre'],
 					'supplierId' => $column['api_id']
 				];
 			
 		}
 	} else {
		return false;
	}
return $apis;
}

$tParams = getApiInfo(); 

$supplierID = $tParams['supplierId'];
$appKey = $tParams['appKey'];
$appSecret = $tParams['appSecret'];
$service_url = "https://api.trendyol.com/sapigw/suppliers/$supplierID/orders";
$curl = curl_init($service_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$header = array(
    'Authorization: Basic ' . base64_encode($appKey . ':' . $appSecret)
);
curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
$curl_response = curl_exec($curl);
$orderDetails = json_decode($curl_response, true); 
 print("<pre>" . print_r($orderDetails, true) . "</pre>"); die();
 foreach ($orderDetails["content"] as $key => $orderDetail) {
   // print("<pre>" . print_r($orderDetail, true) . "</pre>"); 
      $siparisno=$orderDetail["orderNumber"];
      
       /*   $odemetipiKod=$orderDetail->orderDetail->paymentType;
        switch ($odemetipiKod) {
    case "1": $odemetipi="Kredi Kartı"; break;
       case "2": $odemetipi="BKMEXPRESS"; break;
/*      case "3": $odemetipi="AKBANKDIREKT"; break;
         case "4": $odemetipi="PAYPAL"; break;
          case "5": $odemetipi="MallPoint"; break;
           case "6": $odemetipi="GARANTIPAY"; break;
            case "7": $odemetipi="GarantiLoan"; break;
             case "8": $odemetipi="MasterPass"; break;
              case "9": $odemetipi="ISBANKPAY"; break;
               case "10": $odemetipi="PAYCELL"; break;
                case "11": $odemetipi="COMPAY"; break;
                 case "12": $odemetipi="YKBPAY"; break;
                 
        default:
          $odemetipi="Other";}*/
           $urunid=$orderDetail["lines"][0]["id"];
           $adet=$orderDetail["lines"][0]["quantity"];
            $vergibilgileri=$orderDetail["taxNumber"];
             $adsoyad=$orderDetail["shipmentAddress"]["fullName"];
             $telefon=$orderDetail["shipmentAddress"]["phone"];
             $email=$orderDetail["customerEmail"];
             $adres=$orderDetail["shipmentAddress"]["fullAddress"];
             $il=$orderDetail["shipmentAddress"]["city"];
        $ilce=$orderDetail["shipmentAddress"]["district"];
         $faturaadres=$orderDetail["invoiceAddress"]["fullAddress"];
          $faturail=$orderDetail["invoiceAddress"]["city"];
        $faturailce=$orderDetail["invoiceAddress"]["district"];
         $onayKod=$orderDetail["status"];
          switch ($onayKod) {
    case "Awaiting": $onay="İşlem Bekliyor"; break;
       case "Created": $onay="Gönderime Hazır"; break;
        case "Picking": $onay="Sipariş Hazırlanıyor"; break;
         case "Invoiced": $onay="Fatura Oluşturuldu"; break;
          case "Shipped": $onay="Kargoya Verildi"; break;
           case "AtCollectionPoint": $onay="Sipariş Teslimat Noktasında"; break;
            case "Cancelled": $onay="İptal Edildi"; break;
             case "UnPacked": $onay="Sipariş Paketi BÖündü"; break;
              case "Delivered": $onay="Tamamlandı"; break;
               case "UnDelivered": $onay="Teslim Edilemedi"; break;
                case "UnDeliveredAndReturned": $onay="Paket Tedarikçiye Geri Döndü"; break;
                  case "ReadyToShip": $onay="Sipariş Hazırlanıyor"; break;
        default:
          $onay="";}
           $toplamtutar=$orderDetail["totalPrice"];
            $uye=$orderDetail["customerId"];
               $durumKod=$orderDetail["lines"][0]["orderLineItemStatusName"];
        switch ($durumKod) {
    case "Awaiting": $durum="İşlem Bekliyor"; break;
       case "Created": $durum="Gönderime Hazır"; break;
        case "Picking": $durum="Sipariş Hazırlanıyor"; break;
         case "Invoiced": $durum="Fatura Oluşturuldu"; break;
          case "Shipped": $durum="Kargoya Verildi"; break;
           case "AtCollectionPoint": $durum="Sipariş Teslimat Noktasında"; break;
            case "Cancelled": $durum="İptal Edildi"; break;
             case "UnPacked": $durum="Sipariş Paketi BÖündü"; break;
              case "Delivered": $durum="Tamamlandı"; break;
               case "UnDelivered": $durum="Teslim Edilemedi"; break;
                case "UnDeliveredAndReturned": $durum="Paket Tedarikçiye Geri Döndü"; break;
                  case "ReadyToShip": $durum="Sipariş Hazırlanıyor"; break;
        default:
          $durum="";}
           $kupontutari=$orderDetail["totalDiscount"];
            $tarih=date('Y-m-d H:i:s',($orderDetail["orderDate"]/1000));
            print("<pre>" . print_r($tarih, true) . "</pre>"); 
        $takipno=$orderDetail["cargoTrackingNumber"];
         $pazar_id="4";
       //  print("<pre>" . print_r($takipno, true) . "</pre>");
      $urunadi=$orderDetail["lines"][0]["productName"];
      $urunkodu=$orderDetail["lines"][0]["productCode"];
      $birim_fiyat=$orderDetail["lines"][0]["amount"];
      $kdv=$orderDetail["lines"][0]["vatBaseAmount"];
        $siparisSorgula = $ozy->prepare("select count(*) from siparis where siparisno=?");
        $siparisSorgula->execute(array($siparisno));
        $number_of_rows = $siparisSorgula->fetchColumn(); 
        if($number_of_rows==0){
         $siparis = $ozy->prepare("INSERT INTO siparis (siparisno, odemetipi, urunid, adet, uyetip, vergibilgileri, adsoyad, telefon, email, adres, il, ilce, faturatip, faturaadres, faturail, faturailce, onay, aratutar, kdvtutari, kargotutari, havaleindirimtutari, kuponid, kupontutari, cekid, cektutari, kapitutar, toplamtutar, uye, kim, gelenkim, uruntablo, mesaj, durum, ekozellikid, ekozellikadet, tarih, mailtablo, tarihson,pazar_id,takipno) 
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
         $siparisonuc = $siparis->execute(array("$siparisno", "", "$urunid", "$adet", 0, "$vergibilgileri", "$adsoyad", "$telefon", "$email", "$adres", "$il", "$ilce", "", "$faturaadres", "$faturail", "$faturailce", "$onay",0, 0, 0, 0, "", "$kupontutari", 0, 0, 0, "$toplamtutar", "0", "", "", "$urunkodu", "", "$durum", "", "", "$tarih", "", "$tarih","$pazar_id","$takipno"));
         if ($siparisonuc) {
                $s_id = $ozy->lastInsertId();
                
                 $insert_into_siparis_urun = $ozy->prepare("INSERT INTO siparis_urun SET siparis_id=?, urun_id=?, urun_adi=?,birim_fiyat=?,kdv=?,adet=?,urun_kodu=?");
                    $succ = $insert_into_siparis_urun->execute(array($s_id, $urunid, "$urunadi", $birim_fiyat,$kdv,$adet,$urunkodu));
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












?>