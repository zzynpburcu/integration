
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
//print_r($n11Params);
//$endDate=date("Y/m/d");
//$startDate=date("Y-m-d", strtotime("- 14 day"));
$n11 = new N11($n11Params); 
//print_r($_POST);
    $id = $_POST['id'];
  $durum   = "ANSWERED";
  $cevap   = $_POST['cevap'];
  $uyeadi   = $_POST['uyeadi'];
  $soruid = $_POST['soruid'];
  
   $stmt = $ozy->prepare("UPDATE pazaryeri_sorucevap SET durum = ?, cevap_metni = ? WHERE id = ?");
   $result2 = $stmt->execute(array($durum, $cevap, $id));

   if($result2){
       
        $questionList  = $n11->SaveProductAnswer($soruid,$cevap);
       // print_r($questionList); 
        
   }
   


?>