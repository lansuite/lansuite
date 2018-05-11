<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function GetPriceDetails($priceid){
    global $db;
    $result = $db->qry_first("SELECT name, p.party_id, user_id, price.price_id, paid
            FROM %prefix%party_user AS pu 
            LEFT JOIN %prefix%partys AS p USING(party_id) 
            LEFT JOIN %prefix%party_prices AS price 
            ON price.price_id=pu.price_id 
            WHERE user_id=%int% AND price.price_id=%int% AND paid = '0'",$priceid,$auth['userid']);
    return $result;
}
/*
 * We stored the party_id and the price_id inside the PayPal SKU, so we need to extract it from the string again
 */
function SKUtoParty($SKU){
    $SKUarray=explode('-',$SKU);
    if ($SKUarray[0]=='PARTY'){
        return array(
            'party_id' => $SKUarray[1],
            'price_id' => $SKUarray[2] 
            );
    } else return false;
}


if($auth['userid'] == 0 && $cfg['paypal_donation'] == 0){
	$func->error(t('Du kannst nichts einzahlen wenn du nicht eingeloggt bist.'),"index.php?mod=home");
}else{
    include 'modules/paypal/class_paypal.php';
    $PayPalObj = new PayPal();
    $PayPalObj->GetAccessToken();
    $PayPalObj->GetPayment($_SESSION['paypal_payment_id']);
        
if ($PayPalObj->payment->getState()=='created' && !isset($_GET['failed'])){
        $dsp->NewContent(t('Ergebnis der Zahlung'), t('So siehts aus!')); 
        try{
            $status = $PayPalObj->ExecutePayment($_SESSION['paypal_payment_id'],$_GET['PayerID']);
            if ($status='success'){

               //Get Items that were paid from the payment...
               $items = $PayPalObj->GetItemsFromPayment();

               //Show list of items. There has to be at least one because the payment succeeded
               $dsp->addSingleRow('Die folgenden Artikel wurden erfolgreich bezahlt:');
               foreach ($items as $item){
                   $dsp->addDoubleRow($item->quantity.'x '.$item->description,$item->value);
                   switch($item->sku){
                       case 'DONATION':
                           ////add a transaction for this...
                           //require 'modules/cashmgr/class_accounting.php';
                           //$accounting = new accounting(0,$auth['userid']);
                           //$accounting->booking($item->value, t('Spende von').' '.$auth['username'], 0, true);
                           break;
                       case 'CATERING':
                           require 'modules/foodcenter/class_accounting.php';
                           $accounting = new accounting($auth['userid']);
                           $accounting->change($item->value,t('Einzahlung via PayPal'),$auth['userid']); //this is misleading. Account value is not changed, but a transaction added
                           break;
                       default:
                           require 'modules/guestlist/class_guestlist.php';
                           $data = SKUtoParty($item->sku);
                           $GuestList = new guestlist();
                           $GuestList->SetPaid($auth['userid'], $data['party_id']);
                           break;
                   }
               }
            }
            else{
             $dsp->NewContent(t('Zahlung fehlgeschlagen'), t('Leider war die Zahlung nicht erfolgreich!'));    
             $func->error(t('Die PayPal-Zahlung wurde abgebrochen'));
            } 
        }
        catch(PayPal\Exception\PayPalConnectionException $e){
            $dsp->NewContent(t('Zahlung fehlgeschlagen'), t('Leider war die Zahlung nicht erfolgreich!'));  
            $func->error(t('Die PayPal-Zahlung wurde abgebrochen'));
        }
    }
    else {
        $dsp->NewContent(t('Zahlung abgebrochen'), t('Die PayPal-Zahlung wurde abgebrochen')); 
        $func->error(t('Die PayPal-Zahlung wurde abgebrochen'));
    }
}
 ?>