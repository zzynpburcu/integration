<?php
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
$categories=$n11->GetTopLevelCategories();
$categoriessub=$n11->GetSubCategories(1001246);
//$productSellerCode=$n11->GetProductBySellerCode(KIMA000100001);
$product  = $n11->GetProductByProductId('524214942');
print("<pre>".print_r($product,true)."</pre>"); 
$shipmentTemplate=$n11->GetShipmentTemplateList();
//$stockGuncelle=$n11->UpdateProductPriceBySellerCode('KIMA000100001','799');
  //  print_r($stockGuncelle);
if(isset($_POST)){
    $sellerCode=$_POST['code'];
     $stock=$_POST['stock'];
    $stockGuncelle=$n11->UpdateProductPriceBySellerCode("$sellerCode","800");
    print_r($stockGuncelle);
}
//print("<pre>".print_r($shipmentTemplate,true)."</pre>");
//print("<pre>".print_r($productSellerCode,true)."</pre>");
  $image[] = [
        'url' => "https://www.google.com/imgres?imgurl=https%3A%2F%2Fwww.usetechs.com%2FUpload%2FSayfa%2Fmusteri-destek-hizmetleri-logo.jpg&imgrefurl=https%3A%2F%2Fwww.usetechs.com%2Fe-ticaret%2Furun-gorselleri&tbnid=0HD3eX86Nx5uLM&vet=12ahUKEwjlxZKvibL7AhUYLOwKHc1gBwkQMygGegUIARCjAQ..i&docid=7B7f1JkVXNv3BM&w=600&h=600&q=%C3%BCr%C3%BCn%20g%C3%B6rsel&ved=2ahUKEwjlxZKvibL7AhUYLOwKHc1gBwkQMygGegUIARCjAQ",
        'order' =>  1
    ];
    $productSellerCode = 'HOMETEST';
    $title = 'Deneme Ürünüdür Satın Almayın';
    $subtitle = 'TEST';
    $description="TEST DENEME";
    $kategoriID = '100124';
      $attribute[] = ['name' => 'Marka', 'value' => ' K102'];
      $price="10";
      $currencyType = '1';
      $productionDate = '20/03/2022';
      $expirationDate = '15/12/2023';
      $productCondition = '1';
      $preparingDay = '10';
        $discount = [
        'type' => 1, //1: İndirim Tutarı Cinsinden 2: İndirim Oranı Cinsinden 3: İndirimli Fiyat Cinsinden
        'value' => 1,
        'startDate' => '05/10/2021',
        'endDate' => '04/05/2022'
    ];
    $shipmentTemplate = 'default';
       $stockItem[] = [
        'quantity' => 10, //Ürün stok miktarı
        'optionPrice' => 10
    ];
/*$saveProduct = $n11->SaveProduct(
    [
        'productSellerCode' => $productSellerCode, //kendikayıt ederken oluşturduğumuz kod duzenlemede kullanılcak kod random  =ZORUNLU=
        'title' => $title, //urun başlık =ZORUNLU=
        'subtitle' => $subtitle, //urun alt başlık =ZORUNLU=
        'description' => $description, //urunn açıklama =ZORUNLU=
        'attributes' => //n11 kategorilerin attributeleri
        $attribute,
        'category' =>
        [
            'id' => $kategoriID //n11 in kategori idleri  =ZORUNLU=
        ],
        'price' => $price, //Ürün baz fiyatı =ZORUNLU=
        'currencyType' => $currencyType, //urun para birimi =ZORUNLU=
        'images' => //Ürün Resimleri
        $image,
        'saleStartDate' => '', //Ürün satış başlangıç tarihi (dd/MM/yyyy), boş gönderilirse ürün aynı gün satışa çıkar
        'saleEndDate' => '', //Ürün satış bitiş tarihi (dd/MM/yyyy), boş gönderilirse çok ileri bir tarihe atanır
        'productionDate' => $productionDate, //Ürün üretim tarihi (dd/MM/yyyy)
        'expirationDate' => $expirationDate, //Ürün son kullanma tarihi (dd/MM/yyyy)
        'productCondition' => $productCondition, //Ürün durumu:  1=Yeni 2=2.el  =ZORUNLU=
        'preparingDay' => $preparingDay, //Ürün kargoya verilme süresi (gün olarak) =ZORUNLU=
        'domestic' => '', //Ürünün yerli üretim olup olmadığını belirtir.Boolean olarak true/false değeri alır
        'discount' => $discount,
        'shipmentTemplate' => $shipmentTemplate, //Ürünün Teslimat Bilgileri Müşterinin Apisinden Gelicek =ZORUNLU=
        'stockItems' => //fullentegre urun varyantları
        $stockItem,
        'groupAttribute' => '',
        'groupItemCode' => '',
        'itemName' => '',
        'maxPurchaseQuantity' => '',
        'unitInfo' => ''
    ]
);


var_dump($n11->errorCode); */

?>
