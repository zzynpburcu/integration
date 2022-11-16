<?php 
require_once('../../db.php');
 include "trendyol/vendor/autoload.php";
use IS\PazarYeri\Trendyol\TrendyolClient;
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

$service_url = "https://api.trendyol.com/sapigw/suppliers/$supplierID/products?page=0&size=50&approved=True";

$curl = curl_init($service_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$header = array(
    'Authorization: Basic ' . base64_encode($appKey . ':' . $appSecret)
);
curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
$curl_response = curl_exec($curl);
$apiUrunler = json_decode($curl_response, true);

print("<pre>" . print_r($apiUrunler, true) . "</pre>");
?>