<!DOCTYPE html>
<?php
ini_set("display_errors", 1);
require_once('db.php');

	 $satisKanali = $ozy->query("select * from pazaryeri")->fetchAll(PDO::FETCH_ASSOC);
   $sql = $ozy->prepare("select siparis.pazar_id,siparis.tarihson,siparis.durum,siparis.id,siparis.siparisno,siparis.uye,siparis.kargoid,siparis.adsoyad,pazaryeri.isim,siparis.toplamtutar,siparis.tarih,siparis.telefon,siparis.adres,siparis.il,siparis.odemetipi,siparis.takipno from siparis LEFT JOIN pazaryeri ON siparis.pazar_id=pazaryeri.id order by tarih");
		$sql->execute();		
    $pr = $sql->fetchAll(PDO::FETCH_ASSOC);
   ?>
<html>
<head>
        <title>Pazaryeri Entegrasyonlar</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
      
        <style>
          body {
 background-image: url("https://www.uponte.com/upload/manset/slider2_1.jpg");
 background-color: #cccccc;
}
        </style>
</head>
<body>
   
<div class="container">
<div class="page-title-box">
                <div class="row align-items-center">
                    <div class="col-12">
                        <h1 class="page-title">PAZARYERİ APİ ENTEGRASYONU</h1>
                    </div>
                   
                </div>
                <!-- end row -->
            </div>
<div class="row">
                <div class="col-12">
                   <form id="order-table-form" name="order-table-form" action="yeni-siparisler" method="post" enctype="multipart/form-data" >
                    <div class="card m-b-30">
                        <div class="card-body">
                                  <select  name="satis-kanali" id="satis-kanali" class="form-control-sm">
                        <option value="0>">Pazaryeri</option>
                        <?php foreach ($satisKanali as $pazaryeri) { ?>
                         <option   value="<?php echo $pazaryeri["id"]; ?>"><?php echo $pazaryeri["isim"]; ?></option>
                         <?php } ?> </select> 
              
    <div class="table-responsive">          
    <table class="table table-hover">
      <thead>
        <tr>
          
            <th>ID</th>
            <th>Sipariş No</th>
            <th>Müşteri Adı</th>
            <th>Pazaryeri</th>
            <th>Ödenenen Tutar</th>
            <th>Durumu</th>
            <th>Sipariş Tarihi</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($pr as $tr) { ?>	
								
								
                                <tr>
								 <td><?php echo $tr['id']?></td>
                                    <td><?php echo $tr['siparisno']?></td>
								
									<td>      
									<?php if( strlen($tr['adsoyad'])>20){ $adsoyadd=(substr($tr['adsoyad'],0,25))."..."; echo $adsoyadd;  }else{ $adsoyadd=$tr['adsoyad']; echo $adsoyadd; }?></br>
									</td>
								
								
									<td><?php echo $tr['isim']?></td>
                                    <td><?php echo $tr['toplamtutar']?> TL</td>
									
									 <td><span style="font-size: 13px;font-size: 13px;background: white;color: black;border: 1px solid #6975b6;border-radius: 0px;padding: 5px;" class="badge badge-success"><?php echo $tr['durum']?></td>
									</span>
									<td><?php if($tr['pazar_id']==0){echo substr($tr['tarihson'],0,-3);}else{echo substr($tr['tarih'],0,-3);} ?></td>
                              
                                </tr>
                               
							
							<!-- SİPARİŞ YAZDIRMA ALANI -->
							   
		                    <?php }?>					   
							   
							   
							   
      </tbody>
    </table>

    </div>
    </div>
    </div>
</form>
</div>
    </div>
  </div>
  
  </body>
</html>