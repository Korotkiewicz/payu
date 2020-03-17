<?php

namespace Korotkiewicz\PayU;

use Illuminate\Support\Facades\Log;

class PayU {
	protected $continueUrl;
	protected $notifyUrl;
	protected $shopName;

	const RECURRING_FIRST_PAYMENT = 'FIRST'; //first payment
	const RECURRING_EVERY_SECOND_PAYMENT = 'STANDARD'; //not first payment


	public function __construct($productionMode, $merchantId, $signatureKey, $clientId, $clientSecret, $continueUrl, $notifyUrl, $shopName)
	{
        //set Production Environment
		\OpenPayU_Configuration::setEnvironment($productionMode);

		//set POS ID and Second MD5 Key (from merchant admin panel)
		\OpenPayU_Configuration::setMerchantPosId($merchantId);
		\OpenPayU_Configuration::setSignatureKey($signatureKey);

		//set Oauth Client Id and Oauth Client Secret (from merchant admin panel)
		\OpenPayU_Configuration::setOauthClientId($clientId);
		\OpenPayU_Configuration::setOauthClientSecret($clientSecret);   

		$this->continueUrl = $continueUrl;
		$this->notifyUrl = $notifyUrl;
		$this->shopName = $shopName;
	}

	public function getNotificationResult():? \OpenPayU_Result
	{
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		    $body = file_get_contents('php://input');
		    $data = trim($body);

		    try {
		        if (!empty($data)) {
		            return \OpenPayU_Order::consumeNotification($data);
		        }
		    } catch (\OpenPayU_Exception $e) {
		    	Log::error($e);
		    }
		}

		return null;
	}

	public function getShopName()
	{
		return $this->shopName;
	}

	public function getMerchantPosId()
	{
		return \OpenPayU_Configuration::getMerchantPosId();
	}


	/**
	 * Create new Order
	 * @param  string $desc        Cart description
	 * @param  string|int $orderID     must be unique!
	 * @param  int|float $totalAmount Total cart amount
	 * @param  array $products    [['name' => 'Product1', 'unitPrice' => 10, 'quantity' => 1], ...]
	 * @param  array $buyer    ['email' => 'test@gmail.com', 'phone' => '123123123, 'firstName' => 'Jan', 'lastName' => 'Kowalski']
	 * @param  string $currency    [description]
	 * @return \OpenPayU_Order              [description]
	 */
	public function createOrder($desc, $orderID, $totalAmount, $products, $buyer = null, $currency = 'PLN', $additionalParameters = null):? \OpenPayU_Result
	{
		$order = [];
		$order['continueUrl'] = $this->continueUrl; //customer will be redirected to this page after successfull payment
	    $order['notifyUrl'] = $this->notifyUrl;
	    $order['customerIp'] = $_SERVER['REMOTE_ADDR'];
	    $order['merchantPosId'] = self::getMerchantPosId();
	    $order['description'] = $desc;
	    $order['currencyCode'] = $currency;
	    $order['totalAmount'] = $totalAmount;
	    $order['extOrderId'] = $orderID; //must be unique!

	    $order['products'] = $products;

	    if ($currency !== 'HUF') {
	    	$order['totalAmount'] *= 100;

	    	foreach($order['products'] as $i => $product) {
	    		$order['products'][$i]['unitPrice'] *= 100;
	    	}
	    }

	    if (!empty($buyer)) {
		    //optional section buyer
		    $order['buyer'] = $buyer;
		}

		if (!is_null($additionalParameters) && is_array($additionalParameters)) {
			foreach($additionalParameters as $key => $param) {
				if (!is_null($param)) {
					$order[$key] = $param;
				} else {
					unset($order[$key]);
				}
			}
		}

		$response = null;
		try {
	    	$response = \OpenPayU_Order::create($order);
		} catch(\Exception $e) {
			Log::error($e);

			$response = null;
		}

	    return $response;
	}

	/**
	 * Create payu widget using credit cards
	 * @param  float $totalAmount    show mutch you want to charge customer (eg 1.99)
	 * @param  string $customerEmail  customer email
	 * @param  string $widgetMode     pay|use - this is PayU Mode
	 * @param  string $currency       (eg PLN)
	 * @param  string $language       widget language
	 * @param  string $buttonSelector after click this button it will open widget
	 * @return array                 array to set in <script ></script>
	 */
	public function createWidgetAttributes($totalAmount, $customerEmail, $widgetMode = 'use', $currency = 'PLN', $language = 'pl', $buttonSelector = '#pay-button')
	{
		if ($currency !== 'HUF') {
			$totalAmount *= 100;
		}

		$attributes = [
            'merchant-pos-id' => $this->getMerchantPosId(),
            'shop-name' => $this->getShopName(),
            'total-amount' => $totalAmount,
            'currency-code' => $currency,
            'customer-language' => $language,
            'store-card' => 'true',
            'recurring-payment' => 'true',
            'widget-mode' => $widgetMode,
            'customer-email' => $customerEmail,
        ];

        $attributes['sig'] = $this->generateSign($attributes);
        $attributes['pay-button'] = $buttonSelector;
        $attributes['src'] = 'https://secure.payu.com/front/widget/js/payu-bootstrap.js';

        return $attributes;
	}


	/**
	 * genereate sign for payu widget
	 * currency-code: PLN
	 * customer-email: test@test.com
	 * customer-language: pl
	 * merchant-pos-id: 145227
	 * shop-name: TEST
	 * total-amount: 12345
	 */
	protected function generateSign($attributes)
	{
		ksort($attributes);

		$plainText = implode('', array_values($attributes));
		$plainText .= \OpenPayU_Configuration::getSignatureKey();

		return hash("SHA256", $plainText);
	}
}