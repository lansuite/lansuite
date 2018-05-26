<?php

namespace LanSuite\Module\PayPal;

use \PayPal\Rest\ApiContext;
use \PayPal\Auth\OAuthTokenCredential;
use \PayPal\Api\Payment;
use \PayPal\Api\Payer;

class PayPal
{

    /**
     * @var OAuthTokenCredential
     */
    private $authTokenCredential;

    /**
     * @var array
     */
    private $config = [];

    /**
     * @var array
     */
    private $items = [];

    /**
     * @var Payment
     */
    private $payment;

    /**
     * @var ApiContext
     */
    private $apiContext;
    
    public function __construct()
    {
        $this->items = array();

        $this->initConfig();
        $this->initAccessToken();

        $this->apiContext = new ApiContext($this->authTokenCredential, 'Request' . time());
        $this->apiContext-> setConfig($this->config);
    }

    /**
     * @return void
     */
    private function initConfig()
    {
        global $cfg;

        $this->config['client_ID']  = $cfg['paypal_client_ID'];
        $this->config['secret']     = $cfg['paypal_client_secret'];
        $this->config['mode']       = $cfg['paypal_mode'];
    }

    /**
     * @param PayPalItem $item
     * @return void
     */
    public function addItem(PayPalItem $item)
    {
        $this->items[] = $item;
    }

    /**
     * @return array
     */
    public function getItemsFromPayment()
    {
        $transactions= $this->payment->getTransactions();

        $items = [];
        foreach ($transactions as $transaction) {
            $PPitemlist = $transaction->getItemList();
            $PPitems = $PPitemlist->getItems();

            foreach ($PPitems as $PPitem) {
                $item = new PayPalItem($PPitem->getDescription(), $PPitem->getPrice(), $PPitem->getSku(), $PPitem->getQuantity());
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * @return float|int
     */
    public function calcItemsTotal()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->value * $item->quantity;
        }

        return $total;
    }

    /**
     * Get OAuthToken to authenticate for future actions
     *
     * @return void
     */
    public function initAccessToken()
    {
        $this->authTokenCredential = new OAuthTokenCredential($this->config['client_ID'], $this->config['secret'], $this->config);
    }

    /**
     * @param string $paymentID
     * @return Payment
     */
    public function getPayment($paymentID)
    {
        $this->payment = \PayPal\Api\Payment::get($paymentID, $this->apiContext);
        return $this->payment;
    }
    
    public function createPaymentLink()
    {
        global $func;
        
        if (count($this->items)) {
            $payer = new Payer();
            $payer->setPaymentMethod("paypal");
        
            // Set the URLS to return after payment authorisation
            // @TODO: Build dynamically from configuration
            $redirectUrls = new \PayPal\Api\RedirectUrls();

            // use the same link as the user currently has to avoid to return to a variant where the user is not logged in
            // e.g. HTTP vs. HTTPS or http://www.something vs. http://something.de
            if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) {
                $proto = 'https://';
            } else {
                $proto = 'http://';
            }
            $path = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], "index.php"));
            $verification_link = $proto . $_SERVER['SERVER_NAME']. ":" . $_SERVER['SERVER_PORT'].$path;

            $redirectUrls->setReturnUrl($verification_link. "index.php?mod=paypal&action=executepayment");
            $redirectUrls->setCancelUrl($verification_link. "index.php?mod=paypal&action=executepayment&failed=1");

            $this->payment = new Payment();
            $this->payment->setIntent("sale");
            $this->payment->setPayer($payer);
            $this->payment->setRedirectUrls($redirectUrls);
        
            // bundle transactions
            $PayPalItemList = new \PayPal\Api\ItemList();
            foreach ($this->items as $item) {
                $PayPalItem = new \PayPal\Api\Item();
                $PayPalItem->setDescription($item->description);
                $PayPalItem->setPrice($item->value);
                $PayPalItem->setQuantity($item->quantity);
                $PayPalItem->setSku($item->sku);
                $PayPalItem->setCurrency('EUR');
                $PayPalItemList->addItem($PayPalItem);
            }

            $amount = new \PayPal\Api\Amount();
            $amount->setCurrency('EUR');
            $amount->setTotal($this->calcItemsTotal());
            
            $transaction = new \PayPal\Api\Transaction();
            $transaction->setItemList($PayPalItemList);
            $transaction->setAmount($amount);
            $transaction->setDescription('LANsuite test');
        
            $this->payment->setTransactions(array($transaction));

            try {
                $this->payment->create($this->apiContext);
                // store essential information in session...
                $_SESSION['paypal_payment_id'] = $this->payment->getId();

                $approval_link = $this->payment->getApprovalLink();
                return $approval_link;
            } catch (\Exception $e) {
                $func->error(t('Fehler bei der Übermittlung an PayPal'));
            }
        } else {
            // Error handling if no items have been added
            $func->error(t('Du hast keine Option zum Bezahlen ausgewählt'));
        }
    }

    /**
     * @param string $payerID
     * @return string
     */
    public function executePayment($payerID)
    {
        $execution = new \PayPal\Api\PaymentExecution();
        $execution->setPayerId($payerID);
        $this->payment->execute($execution, $this->apiContext);
        $state = $this->payment->getState();
        return $state;
    }
}
