<?php
include("n11.api.class.php");
require_once('../../db.php');

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

     $saveProduct = $n11->SaveProduct(
                 [
        'productSellerCode' => 'TESTURUN2', //kendikayıt ederken oluşturduğumuz kod duzenlemede kullanılcak kod random  =ZORUNLU=
        'title' => "Deneme ürünüdür, satın almayınız", //urun başlık =ZORUNLU=
        'subtitle' => "test,deneme", //urun alt başlık =ZORUNLU=
        'description' => "deneme,test ürün", //urunn açıklama =ZORUNLU=
        'attributes' => //n11 kategorilerin attributeleri
       [     'attribute' => [
           'id' => '354080548',
           'name' => 'Marka',
           'value' => 'TESTM']
           ],
        'category' =>
        [
            'id' => '1003120' //n11 in kategori idleri  =ZORUNLU=
        ],
        'price' => "0.99", //Ürün baz fiyatı =ZORUNLU=
        'currencyType' => 1, //urun para birimi =ZORUNLU=
        'images' => //Ürün Resimleri
        [ 'image'=> [
            'url'=>"https://www.altinbas.com/resim/urun/ozel/ozel-urun/altinbas-ozel-urun-TEST-URUNU-01-1543648181.jpg",
            'order' => 1]
           
        ],
        'saleStartDate' => '', //Ürün satış başlangıç tarihi (dd/MM/yyyy), boş gönderilirse ürün aynı gün satışa çıkar
        'saleEndDate' => '', //Ürün satış bitiş tarihi (dd/MM/yyyy), boş gönderilirse çok ileri bir tarihe atanır
        'productionDate' => '', //Ürün üretim tarihi (dd/MM/yyyy)
        'expirationDate' => '', //Ürün son kullanma tarihi (dd/MM/yyyy)
        'productCondition' => 1, //Ürün durumu:  1=Yeni 2=2.el  =ZORUNLU=
        'preparingDay' => 3, //Ürün kargoya verilme süresi (gün olarak) =ZORUNLU=
        'domestic' => '', //Ürünün yerli üretim olup olmadığını belirtir.Boolean olarak true/false değeri alır
        'discount' => 0,
        'shipmentTemplate' => 'Alıcıoder', //Ürünün Teslimat Bilgileri Müşterinin Apisinden Gelicek =ZORUNLU=
        'stockItems' => //fullentegre urun varyantları
        [
            'stockItem'=> [
                'quantity'=>2,
                'n11CatalogId' => '',
                'sellerStockCode' => '',
                'attributes' =>'',
                'optionPrice' => '',
                ]
            ],
        'groupAttribute' => '',
        'groupItemCode' => '',
        'itemName' => '',
        'maxPurchaseQuantity' => '',
        'unitInfo' => '',
        'sellerNote' => ''
    ]
                
); 
print("<pre>".print_r($saveProduct,true)."</pre>");


?>