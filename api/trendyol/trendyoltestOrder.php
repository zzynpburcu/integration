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


$json = [ 'customer' =>  [
    "customerFirstName" =>"string",
    "customerLastName" => "string"
],
 "invoiceAddress"=> [
    "addressText"=> "string",
    "city"=> "string",
    "company"=> "string",
    "district"=> "string",
    "email"=> "string",
    "invoiceFirstName"=> "string",
    "invoiceLastName"=> "string",
    "latitude"=> "string",
    "longitude"=> "string",
    "neighborhood"=> "string",
    "phone"=> "string", ## "5301234567" ( 10 hane olacak şekilde ve yalnızca rakamdan oluşmalıdır )
    "postalCode"=> "string"
  ],
  "lines"=> [
    [
      "barcode"=> "string",
      "quantity"=> 0
    ],
    [
      "barcode"=> "string",
      "quantity"=> 0
    ]
  ],
  "seller"=> [
    "sellerId"=> 0
  ],
  "shippingAddress"=> [
    "addressText"=> "string",
    "city"=> "string",
    "company"=> "string",
    "district"=> "string",
    "email"=> "string",
    "latitude"=> "string",
    "longitude"=> "string",
    "neighborhood"=> "string",
    "phone"=> "string",
    "postalCode"=> "string",
    "shippingFirstName"=> "string",
    "shippingLastName"=> "string"
  ]
 
 
 ];
$service_url = "https://api.trendyol.com/sapigw/suppliers/$supplierID/products?page=0&size=50&approved=True";
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
print_r("Test Siparişi Oluştu");
       
   
   


?>