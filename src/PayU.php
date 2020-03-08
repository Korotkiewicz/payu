<?php

namespace Bolebor\PayU;

class PayU {
	public function __construct($productionMode, $merchantId, $signatureKey, $clientId, $clientSecret)
	{
        //set Production Environment
		\OpenPayU_Configuration::setEnvironment($productionMode);

		//set POS ID and Second MD5 Key (from merchant admin panel)
		\OpenPayU_Configuration::setMerchantPosId($merchantId);
		\OpenPayU_Configuration::setSignatureKey($privayeKey);

		//set Oauth Client Id and Oauth Client Secret (from merchant admin panel)
		\OpenPayU_Configuration::setOauthClientId($clientId);
		\OpenPayU_Configuration::setOauthClientSecret($clientSecret);   
	}
}