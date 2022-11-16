<?php

class TrendyolCurl
{
    /**
     *
     * Trendyol Api Url
     * @var string
     *
     */
    public $apiUrl;

    /**
     *
     * Trendyol Api SupplierId
     * @var int
     *
     */
    protected $apiSupplierId;

    /**
     *
     * Trendyol Api Kullanıcı Adı
     * @var string
     *
     */
    protected $apiUsername;

    /**
     *
     * Trendyol Api Şifre
     * @var string
     *
     */
    protected $apiPassword;

    /**
     *
     * Trendyol Api Şifre
     * @var string
     *
     */
    protected $method;

    /**
     *
     * API SupplierId değerini değiştirir.
     *
     * @param string $apiUrl
     *
     */
    public function setApiSupplierId($supplierId)
    {
        $this->apiSupplierId = $supplierId;
        $this->setApiUrl();
    }

    /**
     *
     * API Kullanıcı adını değiştirir.
     *
     * @param string $apiUrl
     *
     */
    public function setApiUsername($username)
    {
        $this->apiUsername = $username;
    }

    /**
     *
     * API Şifresini değiştirir.
     *
     * @param string $apiUrl
     *
     */
    public function setApiPassword($password)
    {
        $this->apiPassword = $password;
    }

    /**
     *
     * Api Linkini ayarlar.
     *
     * @param string $apiUrl
     *
     */
    public function setApiUrl()
    {
        $this->apiUrl = 'https://api.trendyol.com/sapigw/';
    }

    /**
     *
     * Method türünü ayarlama POST|GET...
     *
     * @param string $method
     *
     */
    public function setMethod($method)
    {
        $this->method = strtoupper($method);
    }

    /**
     *
     * Trendyol için basic auth döndürür
     *
     * @author Ismail Satilmis <ismaiil_0234@hotmail.com>
     * @return string 
     *
     */
    protected function authorization()
    {
        return base64_encode($this->apiUsername . ':' . $this->apiPassword);
    }

    public function getResponse(array $data, $url, $authorization = true)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        if ($authorization) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $this->authorization()));
        }

        if (($this->method == 'POST' || $this->method == 'PUT') && !empty($data)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        $response = trim(curl_exec($ch));
        if (empty($response)) {
            print("Trendyol boş yanıt döndürdü.");
        }

        $response = json_decode($response);
        curl_close($ch);
        return $response;
    }

    /*** |||||||||||||||| SİPARİŞ |||||||||||||||| SİPARİŞ |||||||||||||||| SİPARİŞ |||||||||||||||| SİPARİŞ */

    /****KARGO TAKİP KODU BİLDİRME updateTrackingNumber*/
    /**Bu method herhangi bir paket için çağırıldığında, artık Trendyol’un anlaşması üzerinden olan paket değil, 
     * tedarikçinin kendi anlaşması üzerinden yaptığı gönderinin durumu sorgulanmaya başlar 
     * ve Yola Çıktı, Teslim Edildi, Teslim Edilemedi bilgileri entegrasyon üzerinden alınır ve takip edilir. */
    /**Bu servise tek seferlik gönderim işlemi yapılmaktadır. */
    /**Eğer bir sipariş iptal edilmiş ise Sipariş Paketlerini Çekme servisi kullanılıp güncel paket numarasına gönderim işlemi yapılması gerekmektedir. */
    /**NOT : "Created" statüsünde olan sipariş paketine "Picking" statüsü iletilmezse "Shipped" statüsüne kadar müşteri tarafında iptal edilebilir olacaktır. */
    function updateTrackingNumber($shipmentPackageId, $trackingNumber)
    {
        
        $url = 'suppliers/' . $this->apiSupplierId . '/' . $shipmentPackageId . '/update-tracking-number';
        $fields = array(
            "trackingNumber" =>  $trackingNumber // string
        );
        $this->method = 'PUT';
        return $this->getResponse($fields, $url);
    }

    /****FATURA LİNKİ GÖNDERME sendInvoiceLink*/
    /**Tedarikçi tarafından kendi sisteminde yaratılmış e-Arşiv ya da e-Fatura bilgisinin 
     * LİNK detayını Trendyol sistemine transfer etmek için bu method kullanılacaktır. */
    /**ÖNEMLİ NOT: Bu servisin tetiklenmesi ile birlikte ilgili sipariş paketi için 
     * Trendyol müşterilerine e-Fatura linki kontrol edilerek gönderilir. */
    function sendInvoiceLink($shipmentPackageId, $invoiceLink)
    {
        $url = 'suppliers/' . $this->apiSupplierId . '/' . 'supplier-invoice-links';
        $fields = array(
            "invoiceLink" => $invoiceLink,
            "shipmentPackageId" => $shipmentPackageId
        );
        $this->method = 'POST';
        return $this->getResponse($fields, $url);
    }

    /****TEDARİK EDEMEME BİLDİRİMİ updatePackage*/
    /**Tedarikçinin paket içerisindeki ürünlerden bir ya da birkaçını Tedarik Edememe kaynaklı iptal etmesi için kullanılır. 
     * Bu method yardımıyla yapılan bir iptal sonrası, iptal edilen paket bozularak yeni ID’li bir paket oluşturulacaktır.*/
    /**NOT: Tedarik edememe bildirimi yapıldıktan sonra Trendyol Order Management System tarafından 
     * aynı orderNumber üzerinde yeni bir ShipmentPackageID oluşturulmakta ve daha önceki shipmentpackage iptal edilmektedir. 
     * Bu durumda Tedarik Edememe kaydı yapıldıktan sonra tekrar Sipariş Paketlerini Çekme işlemi yapılması gerekmektedir. */

    /****FATURA KESME BİLDİRİMİ updatePackage*/
    /**Oluşturularan sipariş paketinin faturasının kesilmesi işleminin Trendyol’a bildirilebilmesi için kullanılır.
     * Fatura kesme işleminin bildirilmesi, Trendyol Müşteri Hizmetlerine ulaşan, müşteri kaynaklı iptallerin 
     * önlenmesi için bir referanstır. */
    /**Siparişe ait paketi sadece 2 paket statüsü ile güncelleyebilirsiniz. 
     * Bu statüler haricindekiler sistem tarafından otomatik olarak pakete aktarılmaktadır.  */
    /**NOT: Statü beslemelerini yaparken önce "Picking" sonra "Invoiced" statü beslemesi yapmanız gerekmektedir. */
    /*****Picking statüsü beslediğiniz an Trendyol panelinde "Sipariş İşleme Alınmıştır" ifadesi gözükecektir. 
     * Bu statü ile kendi tarafınızda siparişlerinize ait durumu kontrol edebilirsiniz. */
    /*****Invoiced statüsü beslediğiniz an Trendyol panelinde "Sipariş İşleme Alınmıştır" ifadesi gözükecektir. 
     * Bu statü ile kendi tarafınızda siparişlerinize ait durumu kontrol edebilirsiniz. */
    function updatePackage($id, array $lines, array $params, string $status)
    {
        $url = 'suppliers/' . $this->apiSupplierId . '/' . 'shipment-packages/' . $id;
        $fields = array(
            "lines" => $lines,
            "params" => $params,
            "status" => $status
        );
        $this->method = 'PUT';
        return $this->getResponse($fields, $url);
    }
    /**Picking
     {
        "lines": [{
            "lineId": {lineId},
            "quantity": 3
        }],
        "params": 
        {
            "boxQuantity": 2,
            "deci": 2.8
        },
        "status": "Picking"
    }*/
    /**Invoiced
     {
        "lines": [{
            "lineId": {lineId},
            "quantity": 3
        }],
        "params": {
            "invoiceNumber": "EME2018000025208"
        },
        "status": "Invoiced"
    }*/
    /**UnSuplied
 {
    "lines": [
        {
        "lineId": {id},
        "quantity": 1
        }
    ],
    "params": {},
    "status": "UnSupplied"
    }*/

    /*** |||||||||||||||| İADE |||||||||||||||| İADE |||||||||||||||| İADE |||||||||||||||| İADE */

    /****İADESİ OLUŞTURULAN SİPARİŞLERİ ÇEKME getClaims*/
    /**Trendyol sisteminde iadesi oluşan siparişleri bu metod yardımıyla çekebilirsiniz. */
    /**Kullanılabilinecek statüler: Created, WaitingInAction, Accepted, Rejected, Cancelled, Unresolved. */
    public function getClaims($status)
    {
        $url = 'claims?claimItemStatus=' . $status; //Created, WaitingInAction, Accepted, Rejected, Cancelled, Unresolved
        $this->method = 'GET';
        return $this->getResponse([], $url);
    }

    /****İADE TALEBİ OLUŞTURMA createClaim*/
    /**Deponuza iade kodu alınmadan gelen sipariş paketlerin iade talep paketlerini oluşturmak için kullanabilirsiniz. 
     * Bu servis ile paket oluşturduktan sonra iade paketlerini çekme servisi ile iade paketlerini tekrardan çekebilirsiniz. */
    /**NOT: Oluşturacağınız iade talebi "Created" statüsünde oluşacaktır. 
     * createClaim servisi, sadece onaylayacağınız iade talepleri için kullanabilirsiniz. */
    public function createClaim(array $fields)
    {
        $url = 'claims/create';
        $this->method = 'POST';
        return $this->getResponse($fields, $url);
    }
    /**Örnek İstek
     * {
        "claimItems": [
            {
            "barcode": "string",
            "customerNote": "string", 
            "quantity": 0,
            "reasonId": 401
            }
        ],
        "customerId": 0,
        "excludeListing": true,
        "forcePackageCreation": true,
        "orderNumber": "string",
        "shipmentCompanyId": 0
        }*/

    /****İADE SİPARİŞLERİ ONAYLAMA approveClaimLineItems*/
    /**Trendyol sisteminde iadesi oluşarak deponuza ulaşan iade siparişleri bu method yardımıyla onaylayabilirsiniz.  */
    /**NOT 1: "claimId" ve "claimLineItemIdList" değerine İadesi Oluşturulan Siparişleri Çekme servisimizi kullanarak bu değere ulaşabilirsiniz. */
    /**NOT 2: Sadece "WaitingInAction" statüsündeki siparişleri onaylayabilirsiniz. */
    public function approveClaimLineItems($claimId, array $fields)
    {
        $url = 'claims' . $claimId . '/items/approve';
        $this->method = 'PUT';
        return $this->getResponse($fields, $url);
    }
    /**Örnek İstek
     * {
        "claimLineItemIdList": [
        "f9da2317-876b-4b86-b8f7-0535c3b65731"
        ],
        "params": {}
        }*/

    /****İADE SİPARİŞLERİNDE RET TALEBİ OLUŞTURMA createClaim*/
    /**Trendyol sisteminde iadesi oluşarak deponuza ulaşan iade siparişleri bu method yardımıyla ret talebi oluşturabilirsiniz.  */
    /**NOT 1: "claimId" ve "claimLineItemIdList" değerine İadesi Oluşturulan Siparişleri Çekme servisimizi kullanarak bu değerlere ulaşabilirsiniz 
     * ve sadece "WaitingInAction" statüsündeki iade siparişlerine ret talebi oluşturabilirsiniz. */
    /**NOT 2: İadeye ait ekleri (pdf, jpeg vb.) "Binary (file)" olarak eklemeniz gerekmektedir. */
    /**NOT 3: "claimIssueReasonId" değerine İade Sebeplerini Çekme servisimizi kullanarak ilgili ID değerlerine ulaşabilirsiniz. */
    /**NOT 4: "description" değerini freetext olarak maksimum 500 karakter olarak yazabilirsiniz. */
    /**NOT 5: Sadece "WaitingInAction" statüsündeki siparişleri için Ret Talebi oluşturabilirsiniz. */
    public function createClaimIssue($claimId, $claimIssueReasonId, $claimItemIdList, $test, array $fields)
    {
        $url = 'claims' . $claimId . '/issue?claimIssueReasonId=' . $claimIssueReasonId . '&claimItemIdList=' . $claimItemIdList . '&description=' . $test;
        $this->method = 'POST';
        return $this->getResponse($fields, $url);
    }
    /**Örnek cUrl İsteği
        curl -X POST \
        'https://api.trendyol.com/sapigw/claims/{claimId}/issue?claimIssueReasonId={claimIssueReasonId}&claimItemIdList={claimItemIdList}&description={test}' \
        -header 'Content-Type: application/json' \
        -form 'files=@/Users/TRENDYOL/Downloads/image.png'
     */

    /****İADESİ OLUŞTURULAN SİPARİŞLERİ ÇEKME getClaims*/
    /**createClaimIssue servisine yapılacak olan isteklerde gönderilecek claimIssueReasonId değerine bu servisi kullanarak ulaşabilirsiniz. */
    public function getClaimsIssueReasons($status)
    {
        $url = 'claim-issue-reasons';
        $this->method = 'GET';
        return $this->getResponse([], $url);
    }

    /*** |||||||||||||||| BARKOD |||||||||||||||| BARKOD |||||||||||||||| BARKOD |||||||||||||||| BARKOD */

    /****BARKOD TALEBİ createCommonLabel*/
    /**Bu servis ile ortak etiket sürecindeki siparişler için ilgili kargo numarasına ait barkod talebi yapabilirsiniz.
     * Barkod oluştuktan sonra sizlere ZPL formatında servisinden dönecektir. */
    /**Bu servis için sadece post isteği yeterlidir. Herhangi bir JSON body göndermenize gerek bulunmamaktadır.*/
    function createCommonLabel($cargoTrackingNumber)
    {
        $url = 'common-label/' . $cargoTrackingNumber . '?format=ZPL';
        $this->method = 'POST';
        return $this->getResponse([], $url);
    }

    /****OLUŞAN BARKODUN ALINMASI getCommonLabel*/
    /**Bu servis ile barkod talebi oluşturduğunuz formattaki değeri alabilirsiniz. */
    function getCommonLabel($cargoTrackingNumber)
    {
        $url = 'common-label/' . $cargoTrackingNumber;
        return $this->getResponse([], $url);
    }
}
