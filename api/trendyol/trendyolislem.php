<?php
session_start();
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 include "trendyol/vendor/autoload.php";
//include "trendyol/vendor/ismail0234/trendyol-php-api/IS/PazarYeri/Trendyol/TrendyolClient.php";

include('../../db.php');

use IS\PazarYeri\Trendyol\TrendyolClient;

$tParams = getApiInfo();






function getApiInfo()
{
  global $db;
  $query = $db->prepare("SELECT * FROM pazaryeri_magaza WHERE pazar_id = 4");
  $query->execute();
  $column = $query->fetch(PDO::FETCH_ASSOC);
  $apis = [
    'appKey' => $column['api_anahtar'],
    'appSecret' => $column['api_sifre'],
    'supplierId' => $column['api_id']
  ];
  return $apis;
}

if (!empty($tParams)) {
  //foreach ($tParams as $tParam) {
  $trendyol = new TrendyolClient();

  $trendyol->setSupplierId($tParams['supplierId']);
  $trendyol->setUsername($tParams['appKey']);
  $trendyol->setPassword($tParams['appSecret']);
}


$supplierId = $tParams["supplierId"];

if ($_POST) {
  $classTip = $_POST["tip"];
}
switch ($classTip) {
  case 'trendyol':
    $categoriAttributes = $trendyol->category->getCategoryAttributes($id);
    break;
  case 'marka':
    $brands = $trendyol->brand->getBrands();
    foreach ($brands->brands as $marka) {
      $onClick = "trendyolMarka($marka->id,'$marka->name')";
      $veri .= '<li class="ml-2 li-click" onclick="' . $onClick . '" style="cursor: pointer;">' . $marka->name . '</li>';
    }
    echo $veri;
    break;
  case 'marka_arama':
    $markalar=markaArama($_POST["marka"]);
    foreach ($markalar as $marka) {
      $id=$marka['id'];
      $name=$marka['name'];
      $onClick = "trendyolMarka($id,'$name')";
      $veri .= '<li class="ml-2 li-click" onclick="' . $onClick . '" style="cursor: pointer;">' . $marka['name'] . '</li>';
    }
    echo $veri;
  break;
  case 'sevk_adresi':

    $url = "https://api.trendyol.com/sapigw/suppliers/$supplierId/addresses";
    $sevk = adres($url);
    // print_r($sevk["supplierAddresses"][2]);
    $id = $sevk["supplierAddresses"][2]["id"];
    $name = $sevk["supplierAddresses"][2]["address"];
    $onClick = "adres('sevk_adresi',$id,'TURGUT ÖZAL MAH. 2553 SK. DALGIÇ SIT 7. BLOK NO: 1 G IÇ KAPI NO: 13 ')";
    $veri = '<li class="ml-2 li-click" onclick="' . $onClick . '" style="cursor: pointer;">' . $name . '</li>';
    echo $veri;
    break;
  case 'iade_adresi':
    $url = "https://api.trendyol.com/sapigw/suppliers/$supplierId/addresses";
    $sevk = adres($url);
    // print_r($sevk["supplierAddresses"][2]);
    $id = $sevk["supplierAddresses"][1]["id"];
    $name = $sevk["supplierAddresses"][1]["address"];
    $onClick = "adres('iade_adresi',$id,'TURGUT ÖZAL MAH. 2553 SK. DALGIÇ SIT 7. BLOK NO: 1 G IÇ KAPI NO: 13 ')";
    $veri = '<li class="ml-2 li-click" onclick="' . $onClick . '" style="cursor: pointer;">' . $name . '</li>';
    echo $veri;
    break;

  default:
    # code...
    break;
}

// echo "<pre>";
// print_r($categoriAttributes->categoryAttributes);



function adres($url)
{
  global $tParams;
  $appKey = $tParams["appKey"];
  $appSecret = $tParams["appSecret"];
  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  $header = array(
    'Authorization: Basic ' . base64_encode($appKey . ':' . $appSecret)
  );
  curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
  $curl_response = curl_exec($curl);
  $arr = json_decode($curl_response, true);
  return $arr;
}

function markaArama($marka){
$url="https://api.trendyol.com/sapigw//brands/by-name?name=$marka";
  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  $curl_response = curl_exec($curl);
  $arr = json_decode($curl_response, true);
  return $arr;
}
