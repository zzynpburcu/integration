<?php
session_start();
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require $_SERVER['DOCUMENT_ROOT'] . '/ayarlar.php';

error_reporting(E_ALL);

include "trendyol/vendor/autoload.php";

use IS\PazarYeri\Trendyol\TrendyolClient;
use IS\PazarYeri\Trendyol\Helper\TrendyolException;

$urunId = $_POST["urunId"];
$barkod = $_POST["stok"];


$trendyolUrun = $db->query("SELECT * FROM trendyol_urun where id='$urunId'")->fetch(PDO::FETCH_ASSOC);

$kateID = $trendyolUrun["kategori_id"];
$magazaID = $trendyolUrun["magaza_id"];


$tParams = getApiInfo($magazaID);
if (!empty($tParams)) {
    $trendyol = new TrendyolClient();
    $trendyol->setSupplierId($tParams['supplierId']);
    $trendyol->setUsername($tParams['appKey']);
    $trendyol->setPassword($tParams['appSecret']);
}

function getApiInfo($id)
{
    global $db;
    $query = $db->prepare("SELECT * FROM pazaryeri_magaza WHERE mukellef_id = ? and id = ?");
    $query->execute(array($_SESSION["mukellef_no"], $id));
    $column = $query->fetch(PDO::FETCH_ASSOC);
    $apis = [
        'appKey' => $column['api_anahtar'],
        'appSecret' => $column['api_sifre'],
        'supplierId' => $column['api_id'],
        'cargoCompanyId' => $column["kargo"]
    ];
    return $apis;
}




$supplierID = $tParams["supplierId"];
$appKey = $tParams["appKey"];
$appSecret = $tParams["appSecret"];

$service_url = "https://api.trendyol.com/sapigw/suppliers/$supplierID/products?barcode=$barkod";

$curl = curl_init($service_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$header = array(
    'Authorization: Basic ' . base64_encode($appKey . ':' . $appSecret)
);
// $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
// curl_setopt($curl, CURLOPT_USERAGENT,  $agent);
$curl_response = curl_exec($curl);
$apiUrunler = json_decode($curl_response, true);



function kontrol($deger, $key)
{
    global $apiUrunler;

    foreach ($apiUrunler["content"][$key]["attributes"] as $key => $value) {
        $attributeId = $value["attributeId"];
        if ($attributeId == $deger) {
            return $key;
        }
    }
}


function apiKategoriUrun($kateID)
{
    global $trendyol;
    $categoriAttributes = $trendyol->category->getCategoryAttributes($kateID);
    return $categoriAttributes;
}


foreach ($apiUrunler["content"] as $key => $urun) {
    $kategoriIndex = "";
    $kategoriValue = "";
    $kategoriValueIsim = "";
    $a = apiKategoriUrun($urun["pimCategoryId"]);

    foreach ($a->categoryAttributes as $key2 => $value) {
        $deger = $value->attribute->id;
        $kontrol = kontrol($deger, $key);
        $kontrol = "$kontrol";
        if ($key2 > 0) {
            $kategoriIndex .= "/*";


            $kategoriValue .= "/*";
        }
        $kategoriIndex .= $deger;
        if ($kontrol != "") {
            if ($deger != "47") {
                $kategoriValueIsim .= $apiUrunler["content"][$key]["attributes"][$kontrol]["attributeValue"];
                $kategoriValue .= $apiUrunler["content"][$key]["attributes"][$kontrol]["attributeValueId"];
                $kategoriValueIsim .= "/*";
            } else {
                $kategoriValue .= $apiUrunler["content"][$key]["attributes"][$kontrol]["attributeValue"];
            }
        }
    }
}



$trendyolUpdate = $db->prepare("UPDATE trendyol_urun set kategori_index=?,kategori_value=?,kategori_value_isim=? where id=?");
$trendyolUpdate->execute(array($kategoriIndex, $kategoriValue, $kategoriValueIsim, $urunId));
