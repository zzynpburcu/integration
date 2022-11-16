<?php

session_start();

error_reporting(E_ALL);

ini_set('display_errors', 1);

include('../../db.php');



$magaza_sql = $db->prepare("SELECT * from pazaryeri_magaza where  pazar_id=2");

$magaza_sql->execute();

$magaza = $magaza_sql->fetch(PDO::FETCH_ASSOC);

$kullanici_adi = $magaza["api_anahtar"]; //kullanıcı adi api kısmında tutuluyor

$kullanici_sifre = $magaza["api_sifre"]; // kullanici şifres api şifre kısmında tutuluyor

$merchant_id = $magaza["api_id"];



if ($kullanici_adi == "") {

    exit;
}



$kullanici_adiniz = $kullanici_adi; 

$sifreniz = $kullanici_sifre; 


//echo $merchant_id;

$service_url = 'https://oms-external.hepsiburada.com/orders/merchantid/' . $merchant_id;

$curl = curl_init($service_url);

curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$header = array(

    'Authorization: Basic ' . base64_encode($kullanici_adiniz . ':' . $sifreniz),

);

curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

$curl_response = curl_exec($curl);

$arr = json_decode($curl_response, true);







//print_r($arr);

function baglan($url, $kAdi, $kSifre)

{

    $curl = curl_init($url);

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $header5 = array(

        'Authorization: Basic ' . base64_encode($kAdi . ':' . $kSifre),

    );



    curl_setopt($curl, CURLOPT_HTTPHEADER, $header5);

    $curl_response = curl_exec($curl);

    $arr2 = json_decode($curl_response, true);

    return $arr2;
}



function paketleme($url, $kAdi, $kSifre, $veri)

{

    $curl = curl_init($url);

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $header4 = array(

        'Authorization: Basic ' . base64_encode($kAdi . ':' . $kSifre),

        'Content-Type:application/json',

        'Content-Length: ' . strlen($veri)

    );



    curl_setopt($curl, CURLOPT_HTTPHEADER, $header4);

    curl_setopt($curl, CURLOPT_POSTFIELDS, $veri);

    $curl_response = curl_exec($curl);

    $arr3 = json_decode($curl_response, true);

    return $arr3;
}







//ÜRÜNÜ PAKETLEME YERİ

foreach ($arr["items"] as $key => $value) {

    $id = $value["id"];

    $sku = $value["sku"];
    $quantity = $value["quantity"];

    echo "aaa" . $id . "<br>";
}

$service_url2 = "https://oms-external.hepsiburada.com/lineitems/merchantid/" . $merchant_id . "/packageablewith/lineitemid/" . $id;

$baglanti = baglan($service_url2, $kullanici_adiniz, $sifreniz);

// print("paketleme" . "<pre>" . print_r($arr["items"], true) . "</pre>");




$lineItemIdler = [];

if ($baglanti) {



    $i = 0;

    foreach ($baglanti["lineItems"] as $key => $value2) {

        $lineItemId = $value2["lineItemId"];

        $orderNumber = $value2["orderNumber"];

        $quantity = $value2["quantity"];



        $lineItemIdler[$orderNumber][] = [$lineItemId, $quantity];



        // $service_url3 = "https://oms-external-sit.hepsiburada.com/packages/merchantid/" . $merchant_id;

        //$paketleme = paketleme($service_url3, $kullanici_adiniz, $sifreniz, $veri);
    }
} else {


    if ($_SESSION["kullanici_no"] == 203) {
        foreach ($baglanti["lineItems"] as $key => $value2) {

            $lineItemId = $value2["lineItemId"];
    
            $orderNumber = $value2["orderNumber"];
    
            $quantity = $value2["quantity"];

        }
        $veri = '{
            "parcelQuantity":2,
            "deci":10,
            "lineItemRequests":
         [
           {
             "id": "' . $id . '",
             "quantity":"' . $quantity . '"
           }
         ]
        }';
        $service_url3 = "https://oms-external.hepsiburada.com/packages/merchantid/" . $merchant_id;
        $paketleme = paketleme($service_url3, $kullanici_adiniz, $sifreniz, $veri);
        // print("paketleme" . "<pre>" . print_r($paketleme, true) . "</pre>");

        
    }
}



foreach ($lineItemIdler as $lineItemIds) {

    $i = 0;

    $veri2 = "";

    foreach ($lineItemIds as $aa) {

        if ($i >= 1) {

            $veri2 .= ",";
        }

        $veri2 .= '{

                    "id": "' . $aa[0] . '",

                    "quantity":"' . $aa[1] . '"

                }';

        $i++;
    }

    $veri = '{

        "parcelQuantity":1,

        "deci":10,

        "lineItemRequests": 

            [

               ' . $veri2 . '

            ]

        }';

    //echo "<pre>".$veri."</pre>";

    $service_url3 = "https://oms-external.hepsiburada.com/packages/merchantid/" . $merchant_id . "?timespan=96";

    $paketleme = paketleme($service_url3, $kullanici_adiniz, $sifreniz, $veri);
}





$service_url4 = "https://oms-external.hepsiburada.com/packages/merchantid/" . $merchant_id . "?timespan=96";

$siparisler = baglan($service_url4, $kullanici_adiniz, $sifreniz);



// print("siparisler" . "<pre>" . print_r($siparisler, true) . "</pre>");





foreach ($siparisler as $key => $value) {

    $items = $value["items"];

    $kullanici_no = $value["packageNumber"];



    $siparis_no_query = $db->prepare("SELECT * FROM pazaryeri_siparis WHERE kullanici_no = ? AND mukellef_id = ?");

    $siparis_no_query->execute(array($kullanici_no, $mukellef_id));

    $sorgu = $siparis_no_query->fetch();



    if (!empty($sorgu)) {
        $siparisID = $sorgu["id"];
        $kargoKodu = $value["barcode"];
        $sirketAdi = $value["cargoCompany"];

        $kargoUpdate = $db->prepare("UPDATE pazaryeri_kargo set kargo_kodu=?,sirket_adi=? where siparis_id=?");
        $kargoUpdate->execute(array($kargoKodu, $sirketAdi, $siparisID));

        continue;
    }




    $musteri_adsoyad = "aa";

    $magaza_id = $magaza["id"];

    $pazar_id = 2;

    $siparis_tarih = $value["orderDate"];

    $siparis_tutar = $value["totalPrice"]["amount"];

    $siparis_no = 1;



    $siparis_insert = $db->prepare("INSERT INTO pazaryeri_siparis SET mukellef_id=?,siparis_no=?, kullanici_no=?, musteri_adsoyad=?, magaza_id=?, pazar_id=?, siparis_tarih=?, siparis_tutar=?,durum=0,onay_durum=1");

    $insert = $siparis_insert->execute(array($mukellef_id, $siparis_no, $kullanici_no, $musteri_adsoyad, $magaza_id, $pazar_id, $siparis_tarih, $siparis_tutar));

    $s_id = $db->lastInsertId(); //siparis_id


    $sirketAdi = $value["cargoCompany"];
    $kargoKodu = $value["barcode"];
    if ($kargoKodu != "") {
        $kargoInsert = $db->prepare("INSERT INTO pazaryeri_kargo SET siparis_id=?,sirket_adi=?,kargo_kodu=?");
        $kargoInsert->execute(array($s_id, $sirketAdi, $kargoKodu));
    }

    foreach ($items as  $item) {

        $stok_kodu = $item["hbSku"];

        $urun_adi = $item["productName"];

        $adet = $item["quantity"];

        $komisyon = $item["commission"]["amount"];

        $fiyat = $item["price"]["amount"];

        $resim_url = 1;
        $urunKodu = $item["merchantSku"];




        //sipariş no su kalemlerde geldiği için update yapıldı ve faturalandırırken bu sipariş no ya göre bilgileri çekilcek

        $siparis_update = $db->prepare("UPDATE pazaryeri_siparis SET siparis_no=? where id=?");

        $siparis_update->execute(array($item["orderNumber"], $s_id));



        $service_url44 = "https://oms-external.hepsiburada.com/orders/merchantid/" . $merchant_id . "/ordernumber/" . $item["orderNumber"];

        $siparisİsim = baglan($service_url44, $kullanici_adiniz, $sifreniz);

        $isim = $siparisİsim["customer"]["name"];

        //müşterinin adı soyadı sipariş detaytında geldiği için sonradan update yapıldı order numarasına göre 

        $siparis_update = $db->prepare("UPDATE pazaryeri_siparis SET musteri_adsoyad=? where id=?");

        $siparis_update->execute(array($isim, $s_id));






        try {

            $insert_into_pazaryeri_siparisler_urun = $db->prepare("INSERT INTO pazaryeri_siparis_urun SET siparis_id=?, stok_kodu=?, urun_adi=?, adet=?, komisyon=?, fiyat=?, resim=?,urun_bilgisi=?");

            $insert_into_pazaryeri_siparisler_urun->execute(array($s_id, $stok_kodu, $urun_adi, $adet, $komisyon, $fiyat, $resim_url, $urunKodu));
        } catch (PDOException $e) {

            $e->getMessage();
        }
    }
}
