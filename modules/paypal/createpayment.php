<?php

/**
 * Process all submitted items and get a payment link and ID from PayPal
 */

use LanSuite\Module\PayPal\PayPalItem;

if ($auth['userid'] == 0 && $cfg['paypal_donation'] == 0) {
    $func->error(t('Du kannst nichts einzahlen wenn du nicht eingeloggt bist.'), "index.php?mod=home");
} else {
    $dsp->NewContent(t('Übersicht der Zahlung'), t('Folgend alle Artikel und Kosten'));

    // Get a payment link from PayPal
    $paypalObj = new \LanSuite\Module\PayPal\PayPal();
    $paypalObj->initAccessToken();

    // Add items for all submitted party prices...
    if (!empty($_POST['price'])) {
        foreach ($_POST['price'] as $priceID) {
            $details = GetPriceDetails($priceID);
            $paypalitem = new PayPalItem($details['name']. ' '.$details['price_text'], $details['price'], 'PARTY-'.$details['party_id'].'-'.$priceID, 1);
            $dsp->AddDoubleRow($details['name']. ' '.$details['price_text'], $details['price']. ' €');
            $paypalObj->addItem($paypalitem);
        }
    }
    // Add foodorder item...

    $cateringValueParameter = $_POST['catering'] ?? 0;
    if ($cateringValueParameter > 0) {
        $paypalitem = new PayPalItem(t('Guthaben Catering'), SanitizeVal($cateringValueParameter), 'CATERING', 1);
        $dsp->AddDoubleRow(t('Guthaben Catering'), SanitizeVal($cateringValueParameter). '€');
        $paypalObj->addItem($paypalitem);
    }

    // Add donation item
    $donationValueParameter = $_POST['donation'] ?? 0;
    if ($donationValueParameter > 0) {
        $paypalitem = new PayPalItem(t('Spende'), SanitizeVal($donationValueParameter), 'DONATION', 1);
        $dsp->AddDoubleRow(t('Spende'), SanitizeVal($donationValueParameter). '€');
        $paypalObj->addItem($paypalitem);
    }

    // Total
    if (!empty($_POST['price']) || $donationValueParameter > 0 || $cateringValueParameter > 0) {
        $dsp->AddDoubleRow(t('Gesamtsumme'), $paypalObj->calcItemsTotal(). '€');
        $PaymentLink = $paypalObj->createPaymentLink();
        $dsp->AddSingleRow($dsp->FetchSpanButton(t('Mit PayPal Bezahlen'), $PaymentLink));
    } else {
        $func->error(t('Du hast keine Option zum Bezahlen ausgewählt'));
    }
}
