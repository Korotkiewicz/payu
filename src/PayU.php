<?php

namespace Korotkiewicz\PayU;

use Illuminate\Support\Facades\Log;

class PayU {
	public function __construct($productionMode, $merchantId, $signatureKey, $clientId, $clientSecret)
	{
        //set Production Environment
		\OpenPayU_Configuration::setEnvironment($productionMode);

		//set POS ID and Second MD5 Key (from merchant admin panel)
		\OpenPayU_Configuration::setMerchantPosId($merchantId);
		\OpenPayU_Configuration::setSignatureKey($signatureKey);

		//set Oauth Client Id and Oauth Client Secret (from merchant admin panel)
		\OpenPayU_Configuration::setOauthClientId($clientId);
		\OpenPayU_Configuration::setOauthClientSecret($clientSecret);   
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

	/**
	 * Create new Order
	 * @param  string $continueUrl [description]
	 * @param  string $notifyUrl   [description]
	 * @param  string $desc        [description]
	 * @param  string|int $orderID     [description]
	 * @param  int|float $totalAmount [description]
	 * @param  array $products    [['name' => 'Product1', 'unitPrice' => 10, 'quantity' => 1], ...]
	 * @param  array $buyer    ['email' => 'test@gmail.com', 'phone' => '123123123, 'firstName' => 'Jan', 'lastName' => 'Kowalski']
	 * @param  string $currency    [description]
	 * @return \OpenPayU_Order              [description]
	 */
	public function createOrder($desc, $orderID, $totalAmount, $products, $continueUrl, $notifyUrl, $buyer = null, $currency = 'PLN'): \OpenPayU_Order
	{
		$order = [];
		$order['continueUrl'] = $continueUrl; //customer will be redirected to this page after successfull payment
	    $order['notifyUrl'] = $notifyUrl;
	    $order['customerIp'] = $_SERVER['REMOTE_ADDR'];
	    $order['merchantPosId'] = OpenPayU_Configuration::getMerchantPosId();
	    $order['description'] = $desc;
	    $order['currencyCode'] = $currency;
	    $order['totalAmount'] = $amount;
	    $order['extOrderId'] = $orderID; //must be unique!

	    $order['products'] = $products;

	    if (!empty($buyer)) {
		    //optional section buyer
		    $order['buyer'] = $buyer;
		}

	    $response = \OpenPayU_Order::create($order);

	    return $response;
	}
}