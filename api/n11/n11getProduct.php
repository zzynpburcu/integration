<?php
require_once('../../db.php');
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

$k=array();
$productList = $n11->GetProductList(100, 0);
foreach($productList->products->product as $key => $product){
$k[]=$productList->products->product[$key]->id;
$productDetails[]  = $n11->GetProductByProductId($productList->products->product[$key]->id);
}
$product  = $n11->GetProductByProductId('524214942');
print("<pre>".print_r($productList,true)."</pre>");

 foreach ($productDetails as $key => $productDetail) {
     $adi=$productDetail->product->id;
     $aciklama=$productDetail->product->description;
     $durum=$productDetail->product->saleStatus;
     
     print("<pre>".print_r($durum,true)."</pre>");
   /*   $stmt = $ozy->prepare("INSERT INTO urunler (adi, aciklama, seo, hit, durum, sira, seodurum, stitle, skey, sdesc, tarih, resim, urunkodu, fiyat, idurum, ifiyat, parabirimi, dolar, idolar, euro, ieuro, kisa, instagram, yildiz, stok, kategori, marka, kdv, agoster, yeni, populer, coksatan, firsat, firsatsaat, filtre, havaledurum, hfiyat, ucretsizkargo, alode, al, ode) 
   VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
   $result2 = $stmt->execute(array($adi, $aciklama, "", "", $durum, $sira, $seodurum, $stitle, $skey, $sdesc, $tarih, $resimadi, $urunkodu, $fiyat, $idurum, $ifiyat, $parabirimi, $dolar, $idolar, $euro, $ieuro, $kisa, $instagram, $yildiz, $stok, $kategori, $marka, $kdv, $agoster, $yeni, $populer, $coksatan, $firsat, $firsatsaat, $filtre, $havaledurum, $hfiyat, $ucretsizkargo, $alode, $al, $ode));
  */
     
 }
?>