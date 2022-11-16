
<?php
require_once('../../db.php');
include("n11.api.class.php");
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
print_r($n11Params);
//$endDate=date("Y/m/d");
//$startDate=date("Y-m-d", strtotime("- 14 day"));
$startDate="01/04/2022";
$endDate="28/04/2022";
$n11 = new N11($n11Params); 
print_r($n11);
    $questionList  = $n11->GetProductQuestionList(
        [
            "productId" => '',
            "buyerEmail" => '',
            "subject" => '',
            "status" => '',
            "questionDate" => '',
             "startDate" => "$startDate",
              "endDate" => "$endDate"
             
        ],0,8
    );
print("<pre>" . print_r($questionList, true) . "</pre>");
foreach ($questionList->productQuestions->productQuestion as $key => $questionDetail) {
      $question_id=$questionDetail->id;
      $product_name=$questionDetail->productTitle;
      $question_text=$questionDetail->question;
      $answer_text=$questionDetail->answer;
      $question_createDate=$questionDetail->questionDate;
  //    $questionListDetail  = $n11->GetProductQuestionDetail($question_id);
    print("<pre>" . print_r($questionDetail, true) . "</pre>");
  
 
       $n11SC = $ozy->prepare("select count(*) from pazaryeri_sorucevap where soru_id=?");
        $n11SC->execute(array($question_id));
        $number_of_rows = $n11SC->fetchColumn(); 
        if($number_of_rows==0){
         $siparis = $ozy->prepare("INSERT INTO pazaryeri_sorucevap (cevap_id, cevap_tarih, cevap_metni, yanit_sure, soru_tarih, soru_id,urun_resim_url,urun_adi,musteri_adi_goster,durum,soru_metni,musteri_adi,urun_url,pazar_id)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
         $siparisonuc = $siparis->execute(array("$answer_id", "$answer_date", "$answer_text", "$answeredDateMessage","$question_createDate" ,"$question_id","$img_url","$product_name","$show_username","$question_status","$question_text","$username","$web_url",1));
         if ($siparisonuc) { echo "BAŞARILI BAŞARILI BAŞARILI BAŞARILI BAŞARILI" . "<br>"; }
        }else{
           
     $stmt = $ozy->prepare("UPDATE pazaryeri_sorucevap SET durum = ?, cevap_metni = ? WHERE soru_id = ?");
     $result2 = $stmt->execute(array($question_status, $answer_text, $question_id));
    if($result2){
      echo "GÜNCELLENDİ GÜNCELLENDİ GÜNCELLENDİ GÜNCELLENDİ GÜNCELLENDİ" . "<br>"; 
      }else { continue; }
        }
}
?>