<?php
class N11
{
    protected static $_appKey, $_appSecret, $_parameters, $_sclient;
    public $_debug = true;

    public function __construct(array $attributes = array())
    {
        self::$_appKey = $attributes['appKey'];
        self::$_appSecret = $attributes['appSecret'];
        self::$_parameters = ['auth' => ['appKey' => self::$_appKey, 'appSecret' => self::$_appSecret]];
    }

    public function setUrl($url)
    {
        @self::$_sclient = new \SoapClient($url);
    }

    public function GetParentCategory(array $categoryId = array())
    {
        $this->setUrl('https://api.n11.com/ws/CategoryService.wsdl');
        self::$_parameters['categoryId'] = 1000314;
        return self::$_sclient->GetParentCategory(self::$_parameters);
    }

    public function GetSubCategories($categoryId)
    {
        $this->setUrl('https://api.n11.com/ws/CategoryService.wsdl');
        self::$_parameters['categoryId'] = $categoryId;
        return self::$_sclient->GetSubCategories(self::$_parameters);
    }

    public function GetTopLevelCategories()
    {
        $this->setUrl('https://api.n11.com/ws/CategoryService.wsdl');
        return self::$_sclient->GetTopLevelCategories(self::$_parameters);       
    }

    public function GetCategoryAttributesId($categoryId)
    {
        $this->setUrl('https://api.n11.com/ws/CategoryService.wsdl');
        self::$_parameters['categoryId'] = $categoryId;
        return self::$_sclient->GetCategoryAttributesId(self::$_parameters);     
    }

    public function GetCategoryAttributeValue($categoryId,$pagingData)
    {
        $this->setUrl('https://api.n11.com/ws/CategoryService.wsdl');
        self::$_parameters['categoryProductAttributeId'] = $categoryId;
        self::$_parameters['pagingData'] =['currentPage' => $pagingData,'pageSize'=>200];
        return self::$_sclient->GetCategoryAttributeValue(self::$_parameters);     
    }

    public function GetShipmentTemplateList()
    {
        $this->setUrl('https://api.n11.com/ws/ShipmentService.wsdl');
        return self::$_sclient->GetShipmentTemplateList(self::$_parameters);       
    }
 
  public function GetProductQuestionList(array $searchData = array(),$count,$page)
    {
        $this->setUrl('https://api.n11.com/ws/ProductService.wsdl');
        self::$_parameters['productQuestionSearch'] = $searchData;
        self::$_parameters['pagingData'] = ['currentPage'=>$count,'pageSize'=>$page];
        return self::$_sclient->GetProductQuestionList(self::$_parameters);
    }

    public function GetCities()
    {
        $this->setUrl('https://api.n11.com/ws/CityService.wsdl');
        return self::$_sclient->GetCities(self::$_parameters);
    }

    public function GetProductList($itemsPerPage, $currentPage)
    {
        $this->setUrl('https://api.n11.com/ws/ProductService.wsdl');
        self::$_parameters['pagingData'] = ['itemsPerPage' => $itemsPerPage, 'currentPage' => $currentPage];
        return self::$_sclient->GetProductList(self::$_parameters);
    }

    public function GetProductBySellerCode($sellerCode)
    {
        $this->setUrl('https://api.n11.com/ws/ProductService.wsdl');
        self::$_parameters['sellerCode'] = $sellerCode;
        return self::$_sclient->GetProductBySellerCode(self::$_parameters);
    }
    public function GetProductByProductId($productId)
    {
        $this->setUrl('https://api.n11.com/ws/ProductService.wsdl');
        self::$_parameters['productId'] = $productId;
        return self::$_sclient->GetProductByProductId(self::$_parameters);
    }

    public function DeleteProductBySellerCode($sellerCode)
    {
        $this->setUrl('https://api.n11.com/ws/ProductService.wsdl');
        self::$_parameters['productSellerCode'] = $sellerCode;
        return self::$_sclient->DeleteProductBySellerCode(self::$_parameters);
    }

    public function OrderList(array $searchData = array(),$count,$page)
    {
        $this->setUrl('https://api.n11.com/ws/OrderService.wsdl');
        self::$_parameters['searchData'] = $searchData;
        self::$_parameters['pagingData'] = ['totalCount'=>$count,'pageCount'=>$page];
        return self::$_sclient->OrderList(self::$_parameters);
    }

    public function OrderDetail(array $orderRequest = array())
    {
        $this->setUrl('https://api.n11.com/ws/OrderService.wsdl');
        self::$_parameters['orderRequest'] = $orderRequest;
        return self::$_sclient->OrderDetail(self::$_parameters);
    }

    public function OrderItemAccept(array $orderItemList = array(), int $numberOfPackages)//
    {
        $this->setUrl('https://api.n11.com/ws/OrderService.wsdl');
        self::$_parameters['orderItemList'] = $orderItemList;
        self::$_parameters['numberOfPackages'] = $numberOfPackages;
        return self::$_sclient->OrderItemAccept(self::$_parameters);
    }

    public function OrderItemReject(array $orderItemList = array(), string $rejectReason, string $rejectReasonType)
    {
        $this->setUrl('https://api.n11.com/ws/OrderService.wsdl');
        self::$_parameters['orderItemList'] = $orderItemList;
        self::$_parameters['rejectReason'] = $rejectReason;
        self::$_parameters['rejectReasonType'] = $rejectReasonType;
        return self::$_sclient->OrderItemAccept(self::$_parameters);
    }

    public function MakeOrderItemShipment(array $orderItemList = array())
    {
        $this->setUrl('https://api.n11.com/ws/OrderService.wsdl');
        self::$_parameters['orderItemList'] = $orderItemList;
        return self::$_sclient->MakeOrderItemShipment(self::$_parameters);
    }

    public function GetShipmentCompanies()
    {
        $this->setUrl('https://api.n11.com/ws/ShipmentCompanyService.wsdl');
        return self::$_sclient->GetShipmentCompanies(self::$_parameters);
    }

    public function SaveProduct(array $product = Array()) {
        $this->setUrl('https://api.n11.com/ws/ProductService.wsdl');
        self::$_parameters['product'] = $product;
        return self::$_sclient->SaveProduct(self::$_parameters);
    }
   public function SaveProductAnswer($productQId,$answer) {
        $this->setUrl('https://api.n11.com/ws/ProductService.wsdl');
        self::$_parameters['productQuestionId'] = $productQId;
        self::$_parameters['answer'] = $answer;
        return self::$_sclient->SaveProductAnswer(self::$_parameters);
    }
    
    public function UpdateDiscountValueBySellerCode($productSellerCode,$price) {
        $this->setUrl('https://api.n11.com/ws/ProductService.wsdl');
        self::$_parameters['productSellerCode'] = $productSellerCode;
        self::$_parameters['productDiscount'] = ['discountType'=>'1','discountValue'=>0,'discountStartDate'=>'','discountEndDate'=>''];

        return self::$_sclient->UpdateDiscountValueBySellerCode(self::$_parameters);
    }
    public function UpdateProductPriceBySellerCode($productSellerCode,$price) {
        $this->setUrl('https://api.n11.com/ws/ProductService.wsdl');
        self::$_parameters['productSellerCode'] = $productSellerCode;
        self::$_parameters['price'] = $price;
        self::$_parameters['currencyType'] = "1";
        self::$_parameters['stockItems'] = ['stockItem'=>['sellerStockCode'=>"",'optionPrice'=>""]];

        return self::$_sclient->UpdateProductPriceBySellerCode(self::$_parameters);
    }

    public function UpdateStockByStockSellerCode($sellerStockCode,$quantity) {
        $this->setUrl('https://api.n11.com/ws/ProductStockService.wsdl');
        self::$_parameters['stockItems'] = ['stockItem'=>['sellerStockCode'=>$sellerStockCode,'quantity'=>$quantity,'version'=>""]];

        return self::$_sclient->UpdateStockByStockSellerCode(self::$_parameters);
    }
 

    public function __destruct()
    {
        if ($this->_debug) {
        //   print_r(self::$_parameters);
        }
    }
}
