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


$service_url = "https://api.trendyol.com/sapigw/suppliers/$supplierID/questions/filter";
$curl = curl_init($service_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$header = array(
    'Authorization: Basic ' . base64_encode($appKey . ':' . $appSecret)
);
curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
$curl_response = curl_exec($curl);
$questionDetails = json_decode($curl_response, true);
foreach ($questionDetails["content"] as $key => $questionDetail) {
   print("<pre>" . print_r($questionDetail, true) . "</pre>");
    $answer_id=$questionDetail['answer']['id']; 
    $answer_date=$questionDetail['answer']['creationDate']; 
     $answer_text=$questionDetail['answer']['text']; 
     $answeredDateMessage=$questionDetail['answeredDateMessage']; 
     $question_createDate=$questionDetail['creationDate'];
     $customer_id=$questionDetail['customerId'];
     $question_id=$questionDetail['id'];
     $img_url=$questionDetail['imageUrl'];
     $product_name=$questionDetail['productName'];
     $show_username=$questionDetail['showUserName'];
     $question_status=$questionDetail['status'];
     $question_text=$questionDetail['text'];
     $username=$questionDetail['userName'];
     $web_url=$questionDetail['webUrl'];
   
      $trendyolSC = $ozy->prepare("select count(*) from pazaryeri_sorucevap where soru_id=?");
        $trendyolSC->execute(array($question_id));
        $number_of_rows = $trendyolSC->fetchColumn(); 
        if($number_of_rows==0){
         $siparis = $ozy->prepare("INSERT INTO pazaryeri_sorucevap (cevap_id, cevap_tarih, cevap_metni, yanit_sure, soru_tarih, soru_id,urun_resim_url,urun_adi,musteri_adi_goster,durum,soru_metni,musteri_adi,urun_url,pazar_id)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
         $siparisonuc = $siparis->execute(array("$answer_id", "$answer_date", "$answer_text", "$answeredDateMessage","$question_createDate" ,"$question_id","$img_url","$product_name","$show_username","$question_status","$question_text","$username","$web_url",4));
         if ($siparisonuc) { echo "BAŞARILI BAŞARILI BAŞARILI BAŞARILI BAŞARILI" . "<br>"; }
        }else{
            if($answer_text!=""){
     $stmt = $ozy->prepare("UPDATE pazaryeri_sorucevap SET durum = ?, cevap_metni = ? WHERE soru_id = ?");
     $result2 = $stmt->execute(array($question_status, $answer_text, $question_id));
    if($result2){
      echo "GÜNCELLENDİ GÜNCELLENDİ GÜNCELLENDİ GÜNCELLENDİ GÜNCELLENDİ" . "<br>"; 
   }
            }
            else { continue; }
        }
     
    
    
}


?>