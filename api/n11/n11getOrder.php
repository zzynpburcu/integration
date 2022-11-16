
<?php
require_once('../../db.php');
include("n11.api.class.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);
function getApiInfo()
{
    global $ozy;
    $query = $ozy->prepare("SELECT * FROM pazaryeri_magaza WHERE  pazar_id = ?");
    $query->execute(array(1));
    $coloumns = $query->fetchAll(PDO::FETCH_ASSOC); 
        foreach ($coloumns as $column) { 
                $apis = [
                    'appKey' => $column['api_anahtar'],
                    'appSecret' => $column['api_sifre']
                ];
        }
      
  
    return $apis;
}
$n11Params = getApiInfo();


$n11 = new N11($n11Params); 

    $orderList  = $n11->OrderList(
        [
            "productId" => '',
            "status" => 'Approved',
            "buyerName" => '',
            "orderNumber" => '',
            "productSellerCode" => '',
            "recipient" => '',
            "period" => [
                "startDate" => '?',
                "endDate" => '?'
            ],
            "sortForUpdateDate" => '',
            
        ],100,10
      
    );
  //  $orderDetails = array();

 if (count($orderList->orderList->order) > 1) {
        $x = $orderList->orderList->order;
    } else {
        $x = $orderList->orderList;
    }
    foreach ($x as $key => $order) {
        if ($order->id) {
            $orderDetails[] = $n11->OrderDetail(['id' => $order->id]);
        }
    }
 print("<pre>" . print_r($orderDetails, true) . "</pre>");
      foreach ($orderDetails as $key => $orderDetail) {
      //   print("<pre>" . print_r($orderDetails, true) . "</pre>");  die();
        $siparisno=$orderDetail->orderDetail->orderNumber;
        $odemetipiKod=$orderDetail->orderDetail->paymentType;
        switch ($odemetipiKod) {
    case "1": $odemetipi="Kredi Kartı"; break;
       case "2": $odemetipi="BKMEXPRESS"; break;
        case "3": $odemetipi="AKBANKDIREKT"; break;
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
          $odemetipi="Other";}
          $vergibilgileri=($orderDetail->orderDetail->buyer->taxId.$orderDetail->orderDetail->buyer->taxOffice)??" ";
        $adsoyad=$orderDetail->orderDetail->shippingAddress->fullName;
        $telefon=$orderDetail->orderDetail->shippingAddress->gsm;
        $email=$orderDetail->orderDetail->buyer->email;
        $adres=$orderDetail->orderDetail->shippingAddress->address;
        $il=$orderDetail->orderDetail->shippingAddress->city;
        $ilce=$orderDetail->orderDetail->shippingAddress->district;
        $faturatip=$orderDetail->orderDetail->invoiceType;
        $faturaadres=$orderDetail->orderDetail->billingAddress->address;
        $faturail=$orderDetail->orderDetail->billingAddress->city;
        $faturailce=$orderDetail->orderDetail->billingAddress->district;
        $onay=$orderDetail->orderDetail->status;
        $uye=$orderDetail->orderDetail->buyer->id;
           if(!is_array($orderDetail->orderDetail->itemList->item)){
                $urunid=$orderDetail->orderDetail->itemList->item->productId;
                $adet=$orderDetail->orderDetail->itemList->item->quantity;
                $durumKod=$orderDetail->orderDetail->itemList->item->status;
                $toplamtutar=$orderDetail->orderDetail->itemList->item->sellerInvoiceAmount;
                if($orderDetail->orderDetail->itemList->item->deliveryFeeType=="3" || $orderDetail->orderDetail->itemList->item->deliveryFeeType=="1" || $orderDetail->orderDetail->itemList->item->deliveryFeeType=="3"){$kargotutari= "0";};
           $mesaj=$orderDetail->orderDetail->itemList->item->customTextOptionsValues;
        $kuponid=$orderDetail->orderDetail->itemList->item->shipmentInfo->campaignNumber;
        $kupontutari=$orderDetail->orderDetail->itemList->item->sellerCouponDiscount;
        $ekozellikid=$orderDetail->orderDetail->itemList->item->attributes->attribute->name;
        $ekozellikadet=$orderDetail->orderDetail->itemList->item->attributes->attribute->value;
         $kargoid=$orderDetail->orderDetail->itemList->item->shipmentInfo->shipmentCompany->id;
        $takipno=$orderDetail->orderDetail->itemList->item->shipmentInfo->trackingNumber;
        $urun_kodu=$orderDetail->orderDetail->itemList->item->productSellerCode;
        $birim_fiyat=$orderDetail->orderDetail->itemList->item->sellerInvoiceAmount;
        $urunadi=$orderDetail->orderDetail->itemList->item->productName;
               
           }
           else {
                     $urunid=$orderDetail->orderDetail->itemList->item[0]->productId;
                $adet=$orderDetail->orderDetail->itemList->item[0]->quantity;
                $durumKod=$orderDetail->orderDetail->itemList->item[0]->status;
                $toplamtutar=$orderDetail->orderDetail->itemList->item[0]->sellerInvoiceAmount;
                if($orderDetail->orderDetail->itemList->item[0]->deliveryFeeType=="3" || $orderDetail->orderDetail->itemList->item[0]->deliveryFeeType=="1" || $orderDetail->orderDetail->itemList->item[0]->deliveryFeeType=="3"){$kargotutari= "0";};
          $mesaj=$orderDetail->orderDetail->itemList->item[0]->customTextOptionsValues;
        $kuponid=$orderDetail->orderDetail->itemList->item[0]->shipmentInfo->campaignNumber;
        $kupontutari=$orderDetail->orderDetail->itemList->item[0]->sellerCouponDiscount;
        $ekozellikid=$orderDetail->orderDetail->itemList->item[0]->attributes->attribute->name;
        $ekozellikadet=$orderDetail->orderDetail->itemList->item[0]->attributes->attribute->value;
        $kargoid=$orderDetail->orderDetail->itemList->item[0]->shipmentInfo->shipmentCompany->id;
        $takipno=$orderDetail->orderDetail->itemList->item[0]->shipmentInfo->trackingNumber;
        $urun_kodu=$orderDetail->orderDetail->itemList->item[0]->productSellerCode;
        $birim_fiyat=$orderDetail->orderDetail->itemList->item[0]->sellerInvoiceAmount;
        $urunadi=$orderDetail->orderDetail->itemList->item[0]->productName;
           }
        
        switch ($durumKod) {
    case "1": $durum="İşlem Bekliyor"; break;
       case "2": $durum="Ödendi"; break;
        case "3": $durum="Geçersiz"; break;
         case "4": $durum="İptal Edilmiş"; break;
          case "5": $durum="Kabul Edilmiş"; break;
           case "6": $durum="Kargoda"; break;
            case "7": $durum="Teslim Edilmiş"; break;
             case "8": $durum="Reddedilmiş"; break;
              case "9": $durum="İade Edildi"; break;
               case "10": $durum="Tamamlandı"; break;
                case "11": $durum="İade İptal Değişim Talep Edildi"; break;
                 case "12": $durum="İade İptal Değişim Tamamlandı"; break;
                  case "13": $durum="Kargoda İade"; break;
                   case "14": $durum="Kargo Yapılması Gecikmiş"; break;
                    case "15": $durum="Kabul Edilmiş Ama Zamanında Kargoya Verilmemiş"; break;
                     case "16": $durum="Teslim Edilmiş İade"; break;
                      case "17": $durum="Tamamlandıktan Sonra İade"; break;
        default:
          $durum="";}
        $pazar_id=1;
        $kdv="";
        $tarih_=$orderDetail->orderDetail->createDate;
        $tarih_=str_replace('/', '-', $tarih_); 
        $tarih = date ('Y-m-d H:i:s', strtotime($tarih_));
        
        //  print("<pre>" . print_r($kargoid, true) . "</pre>");
        $siparisSorgula = $ozy->prepare("select count(*) from siparis where siparisno=?");
        $siparisSorgula->execute(array($siparisno));
        $number_of_rows = $siparisSorgula->fetchColumn(); 
        if($number_of_rows==0){
         $siparis = $ozy->prepare("INSERT INTO siparis (siparisno, odemetipi, urunid, adet, uyetip, vergibilgileri, adsoyad, telefon, email, adres, il, ilce, faturatip, faturaadres, faturail, faturailce, onay, aratutar, kdvtutari, kargotutari, havaleindirimtutari, kuponid, kupontutari, cekid, cektutari, kapitutar, toplamtutar, uye, kim, gelenkim, uruntablo, mesaj, durum, ekozellikid, ekozellikadet, tarih, mailtablo, tarihson,pazar_id,takipno) 
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
         $siparisonuc = $siparis->execute(array("$siparisno", "$odemetipi", "$urunid", "$adet", "0", "$vergibilgileri", "$adsoyad", "$telefon", "$email", "$adres", "$il", "$ilce", "$faturatip", "$faturaadres", "$faturail", "$faturailce", "$onay",0, 0, 0, 0, "$kuponid", "$kupontutari", 0, 0, 0, "$toplamtutar", "0", "", "", "$urun_kodu", "$mesaj", "$durum", "$ekozellikid", "$ekozellikadet", "$tarih", "", "$tarih","$pazar_id","$takipno"));
       
           if ($siparisonuc) {
                $s_id = $ozy->lastInsertId();
                
                 $insert_into_siparis_urun = $ozy->prepare("INSERT INTO siparis_urun (siparis_id, urun_id, urun_adi,birim_fiyat,kdv,adet,urun_kodu) VALUES (?,?,?,?,?,?,?)");
                     if(!is_array($orderDetail->orderDetail->itemList->item)){
                    $succ = $insert_into_siparis_urun->execute(array($s_id, $urunid, $urunadi, $birim_fiyat,$kdv,$adet,$urun_kodu));
                     }else{
                         foreach($orderDetail->orderDetail->itemList->item as $urun){
                         //    $orderDetail->orderDetail->itemList->item[0]->productId
                          $succ = $insert_into_siparis_urun->execute(array($s_id, $urun->productId, $urun->productName, $urun->sellerInvoiceAmount,$kdv,$urun->quantity,$urun->productSellerCode));
                         }
                     }
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