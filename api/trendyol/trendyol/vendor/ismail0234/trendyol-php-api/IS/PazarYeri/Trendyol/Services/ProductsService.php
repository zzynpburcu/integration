<?php

namespace IS\PazarYeri\Trendyol\Services;

use IS\PazarYeri\Trendyol\Helper\Request;

class ProductsService extends Request
{

	/**
	 *
	 * Default API Url Adresi
	 *
	 * @author Ismail Satilmis <ismaiil_0234@hotmail.com>
	 * @var string
	 *
	 */
	public $apiUrl = 'https://api.trendyol.com/sapigw/suppliers/{supplierId}/v2/products';

	/**
	 *
	 * Request sınıfı için gerekli ayarların yapılması
	 *
	 * @author Ismail Satilmis <ismaiil_0234@hotmail.com>
	 *
	 */
	public function __construct($supplierId, $username, $password)
	{
		parent::__construct($this->apiUrl, $supplierId, $username, $password, 'POST');
	}

	/**
	 *
	 * Trendyol üzerindeki ürünleri eklemek için kullanılır.
	 *
	 * @author Ismail Satilmis <ismaiil_0234@hotmail.com>
	 * @return array 
	 *
	 */
	public function addProducts($data = array())
	{

		// $query = array(

		// 	[
		// 		"items" => [
		// 			[
		// 				"barcode" => "barkod-1234",
		// 				"title" => "Bebek Takımı Pamuk",
		// 				"productMainId" => "1234BT",
		// 				"brandId" => 1791,
		// 				"categoryId" => 411,
		// 				"quantity" => 100,
		// 				"stockCode" => "STK-345",
		// 				"dimensionalWeight" => 2,
		// 				"description" => "Ürün açıklama bilgisi",
		// 				"currencyType" => "TRY",
		// 				"listPrice" => 250.99,
		// 				"salePrice" => 120.99,
		// 				"vatRate" => 18,
		// 				"cargoCompanyId" => 10,
		// 				"images" => [
		// 					[
		// 						"url" => "https://www.sampleadress/path/folder/image_1.jpg"
		// 					]
		// 				],
		// 				"attributes" => [
		// 					[
		// 						"attributeId" => 338,
		// 						"attributeValueId" => 6980
		// 					],
		// 					[
		// 						"attributeId" => 47,
		// 						"customAttributeValue" => "PUDRA"
		// 					],
		// 					[
		// 						"attributeId" => 346,
		// 						"attributeValueId" => 4290
		// 					]
		// 				]
		// 			]
		// 		]
		// 	]
		// );
		$query='{
  "items": [
    {
      "barcode": "URUN BARKOD",
      "title": "URUN ADI",
      "productMainId": "URUN KODU",
      "brandId": 647,
      "categoryId": 387,
      "quantity": 1,
      "stockCode": "URUN KODU",
      "dimensionalWeight": 1,
      "description": "Ürün açıklama bilgisi",
      "currencyType": "TRY",
      "listPrice": 220,
      "salePrice": 209,
      "vatRate": 18,
      "cargoCompanyId": 2,
      "shipmentAddressId": 0,
      "returningAddressId": 0,
      "color": "Lacivert",
      "attributes": [],
      "images": [
        {
          "url": "https://app.fullentegre.com/exec/temp/urunResim/27/0424426001613738008.jpeg"
        }
      ]
    }
  ]
}';

		return $this->getResponse($query, $query);
	}
}
