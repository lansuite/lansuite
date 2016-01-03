<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class PayPal{
    
    private $AuthTokenCredential;
    private $config = array(
	"mode" => "sandbox"
            );
    
    function __construct() {
        //include Angell EYE PHP Class Library for PayPal on construction
        //include_once 'ext_scripts/paypal_php/config.php';
        //include_once 'ext_scripts/paypal_php/autoload.php';
        //include_once 'ext_scripts/paypal-php-sdk/autoload.php';
        require 'ext_scripts/paypal-php-sdk/vendor/autoload.php';     
    }
    
    function InitConfig(){
        //TODO: Read API keys and 
    }
    
    function GetAccessToken(){// Get OAuthToken to authenticate for future actions
        $this->AuthTokenCredential = new OAuthTokenCredential("AQkquBDf1zctJOWGKWUEtKXm6qVhueUEMvXO_-MCI4DQQ4-LWvkDLIN2fGsd","EL1tVxAjhT7cJimnz5-Nsx9k2reTKSVfErNQF-CmrwJgxRtylkGTKlU4RvrX", $this->config);  
    } 
    
    function CreatePaymentLink($amount, $description){
        global $cfg;
        $apiContext = new \PayPal\Rest\ApiContext($this->AuthTokenCredential, 'Request'.time());
        $apiContext->setConfig($this->config);
        
        $payer = new PayPal\Api\Payer();
        $payer->setPaymentMethod("paypal");
        
        $amount = new \PayPal\Api\Amount();
        $amount->setCurrency('EUR');
        $amount->setTotal($amount);
        
        $transaction = new \PayPal\Api\Transaction();
        $transaction->setDescription($description);
        $transaction->setAmount($amount);
        
        $baseUrl = getBaseUrl();
        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturn_url("https://devtools-paypal.com/guide/pay_paypal/php?success=true");
        $redirectUrls->setCancel_url("https://devtools-paypal.com/guide/pay_paypal/php?cancel=true");

        $payment = new Payment();
        $payment->setIntent("sale");
        $payment->setPayer($payer);
        $payment->setRedirect_urls($redirectUrls);
        $payment->setTransactions(array($transaction));

$payment->create($apiContext);
        
    }
    
    function CheckPaymentState($payment){
        
        
    }
}
?>
