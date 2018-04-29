<?php

/* 
 * Process all submitted items and get a payment link and ID from PayPal
 */
include 'modules/paypal/class_paypal.php';

function GetPriceDetails($priceID){
    global $db, $auth;
    $result = $db->qry_first('SELECT pu.party_id,pu.user_id,pu.price_id,p.name,price_text,price.price FROM %prefix%party_user AS pu LEFT JOIN %prefix%partys AS p USING(party_id) LEFT JOIN %prefix%party_prices AS price ON price.price_id=pu.price_id WHERE user_id=%int% and pu.price_id=%int%',$auth['userid'],$priceID);
    $result['price']=  SanitizeVal($result['price']); //make sure that we only get the value, not the currency items
    return $result;
}

function SanitizeVal($string){
    //convert comma to decimal point
    $string = str_replace(',','.',$string);
    //just return the float value
    return doubleval($string);
}



if($auth['userid'] == 0 && $cfg['paypal_donation'] == 0){
	$func->error(t('Du kannst nichts einzahlen wenn du nicht eingeloggt bist.'),"index.php?mod=home");
}else{
    $dsp->NewContent(t('Übersicht der Zahlung'), t('Folgend alle Artikel und Kosten'));
    
    //Get a payment link from PayPal

    $PayPalObj = new PayPal();
    $PayPalObj->GetAccessToken();
    //add items for all submitted party prices...
    if (!empty($_POST['price'])){
        foreach ($_POST['price'] as $priceID){
            $details = GetPriceDetails($priceID);
            $paypalitem = new PayPalItem($details['name']. ' '.$details['price_text'],  $details['price'], 'PARTY-'.$details['party_id'].'-'.$priceID, 1);
            $dsp->AddDoubleRow($details['name']. ' '.$details['price_text'],$details['price']. ' €');
            $PayPalObj->AddItem($paypalitem);
        }
    }
    //add foodorder item...
    if ($_POST['catering']>0) {
        $paypalitem = new PayPalItem(t('Guthaben Catering'), SanitizeVal($_POST['catering']),'CATERING',1);
        $dsp->AddDoubleRow(t('Guthaben Catering'),SanitizeVal($_POST['catering']). '€');
        $PayPalObj->AddItem($paypalitem);
    }
    //add donation item
    if ($_POST['donation']>0) {
        $paypalitem = new PayPalItem(t('Spende'), SanitizeVal($_POST['donation']),'DONATION',1);
        $dsp->AddDoubleRow(t('Spende'),SanitizeVal($_POST['donation']). '€');
        $PayPalObj->AddItem($paypalitem);
    }
    //Total
    if (!empty($_POST['price']) || $_POST['donation']>0 || $_POST['catering']>0){
        $dsp->AddDoubleRow(t('Gesamtsumme'),$PayPalObj->CalcItemsTotal(). '€');
        $PaymentLink = $PayPalObj->CreatePaymentLink();
        $dsp->AddSingleRow($dsp->FetchSpanButton(t('Mit PayPal Bezahlen'),$PaymentLink));
    }
    else {
        $func->error(t('Du hast keine Option zum Bezahlen ausgewählt'));
    }
}

?>