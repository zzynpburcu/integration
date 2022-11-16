<?php

use IS\PazarYeri\Trendyol\TrendyolClient;
include "trendyol/vendor/autoload.php";
require_once('../../db.php');
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

    $id = $_POST['id'];
  $durum   = "ANSWERED";
  $cevap   = $_POST['cevap'];
  $uyeadi   = $_POST['uyeadi'];
  $soruid = $_POST['soruid'];
 // print_r($_POST);
   $stmt = $ozy->prepare("UPDATE pazaryeri_sorucevap SET durum = ?, cevap_metni = ? WHERE id = ?");
   $result2 = $stmt->execute(array($durum, $cevap, $id));

   if($result2){
  $json = [ 'text' =>  $cevap  ];
$service_url = "https://api.trendyol.com/sapigw/suppliers/$supplierID/questions/$soruid/answers";
$curl = curl_init($service_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$header = array(
    'Authorization: Basic ' . base64_encode($appKey . ':' . $appSecret)
);
   curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);


$curl_response = curl_exec($curl);
$questionDetails = json_decode($curl_response, true);
print_r("Soru Cevaplandı");
       
   }
   


?>