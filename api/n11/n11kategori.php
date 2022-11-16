<?php
require_once('../../db.php');

 ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
 error_reporting(E_ALL);

include("n11.api.class.php");

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
$topLevelCategories = $n11->GetTopLevelCategories();
$altkategori= $n11->GetSubCategories(1003120);   
$alt_= $n11->GetProductBySellerCode('TESTURUN');
print("<pre>".print_r($altkategori->category->name,true)."</pre>");
die();
$kategoriSira = $db->query("SELECT * FROM n11_kategori_temp_sira ")->fetch(PDO::FETCH_ASSOC);

$deger = $kategoriSira['sira'] + 10;


$topLevelCategories = $n11->GetTopLevelCategories(); // ana kategoriler

echo "<pre>";
$a = altDongu(1001374, 'Süpermarket');
print_r($a);
exit;
$anaKategoriler = $topLevelCategories->categoryList->category;
for ($i = $kategoriSira['sira']; $i < $deger; $i++) {
    // echo $i.".) ".$anaKategoriler[$i]->name."=".$anaKategoriler[$i]->id."<br>";
    altDongu($anaKategoriler[$i]->id, $anaKategoriler[$i]->name);
}
if ($i >= count($anaKategoriler)) {
    $i = 0;
}
tempKategoriSira($i);



function altKategori($id)
{
    global $n11;
    $subCategories2 = $n11->GetSubCategories($id);
    $altKategori2 = $subCategories2->category->subCategoryList->subCategory;
    return $altKategori2;
}
function altKategori2($id)
{
    global $n11;
    $subCategories2 = $n11->GetSubCategories($id);
    $altKategori2 = $subCategories2->category->subCategoryList->subCategory;
    return $altKategori2;
}
function altDongu($id, $kategoriAdi)
{
    global $n11, $i;
    $anaİsim = $kategoriAdi;
    $subCategories = $n11->GetSubCategories($id); //alt kategoriler
    $altKategoriler = $subCategories->category->subCategoryList->subCategory;
    foreach ($altKategoriler as $ak) {
        $kategoriAdi = $anaİsim;
        $kategoriAdi .= " > " . $ak->name;
        $altKategori = altKategori($ak->id);
        if ($altKategori) {
            foreach ($altKategori as $alt) {
                $kategoriAdi = $anaİsim . " > " . $ak->name;
                $kategoriAdi .= " > " . $alt->name;
                $altKategori2 = altKategori($ak->id);
                if ($altKategori2) {
                    foreach ($altKategori2 as $alt2) {
                        $kategoriAdi = $anaİsim . " > " . $ak->name . " > " . $alt->name;
                        $kategoriAdi .= " > " . $alt2->name;
                        echo $kategoriAdi . "=" . $alt2->id . "<br>";
                    }
                } else {

                    echo $kategoriAdi . "=" . $ak->id . "<br>";
                }

               
            }
        } else {
            echo $kategoriAdi . "=" . $ak->id . "<br>";
           
        }
    }
}

