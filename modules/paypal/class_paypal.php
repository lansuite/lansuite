<?php
include_once 'modules/paypal/class_paypal_item.php';
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class PayPal{
    
    private $AuthTokenCredential;
    private $config;
    private $items;
    public $payment; //public as I'm too lazy to write a ton of accessor functions ;)
    private $apiContext;
    
    function __construct() {
        //include PayPal PHP SDK
        require_once 'ext_scripts/paypal-php-sdk/autoload.php';     
        //empty item basket
       $this->items = array();
       $this->InitConfig();
       $this->GetAccessToken();
       $this->apiContext = new PayPal\Rest\ApiContext($this->AuthTokenCredential, 'Request' . time());
       $this->apiContext-> setConfig($this->config);
    }
    
    function InitConfig(){
        global $cfg;
        //TODO: Read API keys and stuff from lansuite
        $this->config['client_ID']=$cfg['paypal_client_ID'];
        $this->config['secret']=$cfg['paypal_client_secret'];
        $this->config['mode']=$cfg['paypal_mode'];
    }
    
    function AddItem($paypalitem){
        $this->items[] = $paypalitem;
    }
    
    function GetItemsFromPayment(){
        $transactions= $this->payment->getTransactions();

        $items = array();
        foreach ($transactions as $transaction){
            $PPitemlist = $transaction->getItemList(); 
            $PPitems = $PPitemlist->getItems();
            foreach($PPitems as $PPitem){
                //convert to our own object to keep the PayPal SDK objects out of LANsuite
                $item = new PayPalItem($PPitem->getDescription(), $PPitem->getPrice(), $PPitem->getSku(), $PPitem->getQuantity());
                $items[]=$item;
            }    
        }
        return $items;
    }
    
    function CalcItemsTotal(){
        $total = 0;
        foreach($this->items as $item){
            $total += $item->value * $item->quantity;
        }
        return $total;
    }
    
    function GetAccessToken(){// Get OAuthToken to authenticate for future actions
        $this->AuthTokenCredential = new PayPal\Auth\OAuthTokenCredential($this->config['client_ID'],$this->config['secret'], $this->config);  
    } 
    
    function GetPayment($PaymentID){
        $this->payment =  \PayPal\Api\Payment::get($PaymentID,$this->apiContext);
    }
    
    function CreatePaymentLink(){
        global $cfg, $func;
        
        if (count($this->items)){
        
        $payer = new PayPal\Api\Payer();
        $payer->setPaymentMethod("paypal");
        
        //Set the URLS to return after payment authorisation
        //@TODO: Build dynamically from configuration
        $redirectUrls = new \PayPal\Api\RedirectUrls();
        $redirectUrls->setReturnUrl("http://localhost/berglan/index.php?mod=paypal&action=executepayment");
        $redirectUrls->setCancelUrl("http://localhost/berglan/index.php?mod=paypal&action=executepayment&failed=1");

        $this->payment = new \PayPal\Api\Payment();
        $this->payment->setIntent("sale");
        $this->payment->setPayer($payer);
        $this->payment->setRedirectUrls($redirectUrls);
        
        //bundle transactions
        $PayPalItemList = new PayPal\Api\ItemList();
        foreach ($this->items as $item){
            $PayPalItem = new \PayPal\Api\Item();
            $PayPalItem->setDescription($item->description);
            $PayPalItem->setPrice($item->value);
            $PayPalItem->setQuantity($item->quantity);
            $PayPalItem->setSku($item->sku);
            $PayPalItem->setCurrency('EUR');
            $PayPalItemList->addItem($PayPalItem);
        }
            $amount = new PayPal\Api\Amount();
            $amount->setCurrency('EUR');
            $amount->setTotal($this->CalcItemsTotal());
            
            $transaction = new \PayPal\Api\Transaction();
            $transaction->setItemList($PayPalItemList); 
            $transaction->setAmount($amount);
            $transaction->setDescription('LANsuite test');
        
        $this->payment->setTransactions(array($transaction));

        try{
            $this->payment->create($this->apiContext);
            //store essential information in session...
            $_SESSION['paypal_payment_id'] = $this->payment->getId();

            $approval_link = $this->payment->getApprovalLink();
            return $approval_link;
            }
            catch(PayPal\Exception\PayPalConnectionException $e){
                $func->error(t('Fehler bei der Übermittlung an PayPal'));
            }
        }
        else {
            //Error handling if no items have been added
            $func->error(t('Du hast keine Option zum Bezahlen ausgewählt'));
        }
    }
    
    function ExecutePayment($paymentID, $payerID){
        $execution = new \PayPal\Api\PaymentExecution();
        $execution->setPayerId($payerID);
        $this->payment->execute($execution, $this->apiContext);  
        $state = $this->payment->getState();
        return $state;
    }
}
?>
