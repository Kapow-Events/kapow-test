<?php
/**
 * Merchant e-Solutions Magento Plugin.
 * v1.1.0 - March 10th, 2011
 * 
 * Copyright (c) 2010 Merchant e-Solutions
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *    * Redistributions of source code must retain the above copyright
 *      notice, this list of conditions and the following disclaimer.
 *    * Redistributions in binary form must reproduce the above copyright
 *      notice, this list of conditions and the following disclaimer in the
 *      documentation and/or other materials provided with the distribution.
 *    * Neither the name of Merchant e-Solutions nor the
 *      names of its contributors may be used to endorse or promote products
 *      derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

class Mes_Gateway_Model_Paymentmodel extends Mage_Payment_Model_Method_Cc {
    protected $_code  					= 'gateway';
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;	## Auth Only
    protected $_canCapture              = true;	## Sale, Capture
    protected $_canCapturePartial       = true;
    protected $_canRefund               = true;	
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true; ## Creation of a transaction from the admin panel
    protected $_canUseCheckout          = true;
	//protected $_formBlockType 			= 'gateway/form';
	
	public function __construct() {
	}
	
	
    public function authorize(Varien_Object $payment, $amount) {
		//$session = Mage::getSingleton('gateway/session');
		
		if($this->getConfigData('logging'))
			Mage::log("[MeS Gateway Module] ".$this->getConfigData('payment_action')." attempt");
		
		$order = $payment->getOrder();
		$billing = $order->getShippingAddress();
		$orderid = $order->getIncrementId();
		list($avsstreet) = $order->getBillingAddress()->getStreet();
		$shipping = $order->getShippingAddress();
		
		$requestValues = array(	"profile_id"				=> $this->getConfigData('profile_id'),
								"profile_key"				=> $this->getConfigData('profile_key'),
								"card_number" 				=> $payment->getCcNumber(),
								"card_exp_date" 			=> str_pad($payment->getCcExpMonth(), 2, "0", STR_PAD_LEFT) . substr($payment->getCcExpYear(), 2, 2),
								"cvv2"						=> $payment->getCcCid(),
								"transaction_amount" 		=> number_format($amount, 2, '.', ''),
								"cardholder_street_address"	=> urlencode($avsstreet),
								"cardholder_zip"			=> $order->getBillingAddress()->getPostcode(),
								"ship_to_zip"				=> $shipping->getPostcode(),
								"transaction_type"			=> $this->convertTransactionType($this->getConfigData('payment_action')),
								"client_reference_number"	=> urlencode($this->getClientReferenceNumber($order, $orderid, $this->getConfigData('client_reference_number'))),
								"tax_amount" 				=> number_format($order->getTaxAmount(), 2, '.', ''),
								"currency_code" 			=> $order->getBaseCurrencyCode(),
								"invoice_number"			=> $orderid
							  );
		
		$response = $this->execute($requestValues);
		
		$payment->setStatus(self::STATUS_APPROVED);
	    $payment->setTransactionId($response['transaction_id']);
		
		$payment->setIsTransactionClosed(0);
		
        return $this;
    }
	
	
    public function capture(Varien_Object $payment, $amount) {
        if($payment->getParentTransactionId()) {
			if($this->getConfigData('logging'))
				Mage::log("[MeS Gateway Module] Capture attempt");
			
			$orderid = $payment->getOrder()->getIncrementId();
			$requestValues = array(	"profile_id"				=> $this->getConfigData('profile_id'),
									"profile_key"				=> $this->getConfigData('profile_key'),
									"transaction_amount" 		=> number_format($amount, 2, '.', ''),
									"transaction_type"			=> 'S',
									"transaction_id" 			=> $payment->getParentTransactionId(),
									"invoice_number"			=> $orderid
								  );
			
			## process the Capture
			$response = $this->execute($requestValues);
			
			$payment->setStatus(self::STATUS_APPROVED);
			$payment->setTransactionId($response['transaction_id']);
			$payment->setIsTransactionPending(false);
			
			return $this;	
		}
		else if($this->getConfigData('payment_action') == self::ACTION_AUTHORIZE_CAPTURE)
			return $this->authorize($payment, $amount);
		else
			Mage::throwException('Unable to perform action: Invalid State');
    }
	
	
    public function refund(Varien_Object $payment, $amount) {
		if($this->getConfigData('logging'))
			Mage::log("[MeS Gateway Module] Refund/Void attempt");
		
        $orderid = $payment->getOrder()->getIncrementId();
		if( empty($orderid) )
			Mage::throwException('Unable to get order ID for this refund.');
		
		if( !$payment->getParentTransactionId() )
			Mage::throwException('Unable to get transaction ID for this refund.');
        
		$requestValues = array(	"profile_id"				=> $this->getConfigData('profile_id'),
								"profile_key"				=> $this->getConfigData('profile_key'),
								"transaction_id" 			=> $payment->getParentTransactionId(),
								"invoice_number"			=> $orderid
							  );
		
		## Set to void or refund. Void will always have an empty amount(Guaranteed by void()).
		if( empty($amount) )
			$requestValues['transaction_type'] = 'V';
		else {
			$requestValues['transaction_type'] = 'U';
			## Set amount for partial refund support
			$requestValues['transaction_amount'] = $amount;
		}
		
		## process the Refund/Void
		$response = $this->execute($requestValues);
		
		$payment->setStatus(self::STATUS_APPROVED);
		return $this;
    }


    public function void(Varien_Object $payment) {
		## Void/Auth Reversal or credit(if settled).
		return $this->refund($payment, null);
    }
	
	
	protected function execute($requestValues) {
		if($this->getConfigData('logging'))
			Mage::log("[MeS Gateway Module] Starting cURL");
		
		$requestString = "";
		foreach($requestValues as $key => $value)
			$requestString .= $key."=".$value."&";
		$requestString = substr($requestString, 0, -1);
		
		## Check to use the sandbox
		if($this->getConfigData('simulator'))
			$url = "https://cert.merchante-solutions.com/mes-api/tridentApi";
		else
			$url = "https://api.merchante-solutions.com/mes-api/tridentApi";
		
		$ch = curl_init($url);
		
		## Method is Post
		curl_setopt($ch, CURLOPT_POST, true); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $requestString);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT,TRUE);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 45); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
		
		## Check for a proxy in the config
		if ( $this->getConfigData('use_proxy') ) {
			if($this->getConfigData('logging'))
				Mage::log("[MeS Gateway Module] Using cURL Proxy: ".$this->getConfigData('proxy_url'));
			curl_setopt ($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
			curl_setopt ($ch, CURLOPT_PROXY, $this->getConfigData('proxy_url'));
		}
		
		## Check to bypass SSL 
		if( $this->getConfigData('bypassssl') ) {
			if($this->getConfigData('logging'))
				Mage::log("[MeS Gateway Module] Bypassing SSL Security");
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		}
		else {
			if($this->getConfigData('logging'))
				Mage::log("[MeS Gateway Module] Using SSL Security");
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
		}
		
		## Run the request
		$rawResponse = curl_exec($ch);
		if($this->getConfigData('logging'))
			Mage::log("[MeS Gateway Module] cURL Response: ".$rawResponse);
		
		## Check for cURL error
		if($rawResponse === false) {
			$errText = curl_error($ch) . ' (error '.curl_errno($ch).')';
			if($this->getConfigData('logging'))
				Mage::log("[MeS Gateway Module] ".$errText);
			Mage::throwException('Error during payment transmission:  ' . $errText );
		}
		else {
			## Parse response into an array
			$response = array();
			$fields = explode("&", $rawResponse);
			foreach($fields as $value) { 
				$field = explode("=", $value); 
				$response[$field[0]] = $field[1]; 
			}
			
			## Custom filters based on the error code
			switch($response['error_code']) {
				case '000':
					return $response;
					break;
				## CVV Mismatch
				case '0N7':
					Mage::throwException('CVV Mismatch - The transaction was not approved because the CVV code did not match. Please verify the CVV is correct and try again, or try a different form of payment.');
					break;
				## AVS filter set by the merchant
				case '210':
					Mage::throwException('AVS Mismatch - The transaction was not approved because the address and/or zip code did not match what the issuer has on file. Please verify the address information is correct and try again, or try a different form of payment.');
					break;
				## Any code under 100 is a negative response from the issuer
				case $response['error_code'] < 100:
					Mage::throwException('The transaction was declined by the issuer. You may contact the issuing bank, or try another form of payment.');
					break;
				## System error
				case '999';
					Mage::throwException('A Gateway error occurred while processing.');
					break;
				## Most codes over 100 are configuration errors
				default:
					Mage::throwException('An error occurred while processing: '.$response['auth_response_text'].' (error code '.$response['error_code'].') ');
					break;
			}
		}
	}
	
	
	private function getClientReferenceNumber($order, $orderid, $crn) {
		
		$billing = $order->getShippingAddress();
		
		## Default
		if(empty($crn)) {
			return "Order #".$orderid;
		}
		else {
			## Add additional macros as necessary here
			$crn = str_replace("[ip]", $order->getRemoteIp(), $crn);
			$crn = str_replace("[name]", $billing->getFirstname() . " " . $billing->getLastname(), $crn);
			$crn = str_replace("[email]", $order->getCustomerEmail(), $crn);
			$crn = str_replace("[orderid]", $orderid, $crn);
			$crn = str_replace("[phone]", $billing->getTelephone(), $crn);
			$crn = str_replace("[company]", $billing->getCompany(), $crn);
			$crn = str_replace("[customerid]", $billing->getCustomerId(), $crn);
			return $crn;
		}
	}
	
	
	private function convertTransactionType($str) {
		return $str == "authorize" ? "P" : "D";
	}
	
	
	private function secureEligible($str) {
		## Visa and MC only for 3D Secure
		if( ($str == "VI") || ($str == "MC") )
			return true;
		else
			return false;
	}
	
}