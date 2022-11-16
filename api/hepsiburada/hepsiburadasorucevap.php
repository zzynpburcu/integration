<?php
require_once('../../db.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);
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

$service_url =  "https://api-asktoseller-merchant-sit.hepsiburada.com/api/v1.0/issues?status=4&status=2&page=1&size=25&sortBy=1&desc=true";

$curl = curl_init($service_url);

curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$header = array(

    'Authorization: Basic ' . base64_encode($kullanici_adiniz . ':' . $sifreniz),

);

curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

$curl_response = curl_exec($curl);

$arr = json_decode($curl_response, true);

 print("sorular" . "<pre>" . print_r($curl_response, true) . "</pre>"); 
?>