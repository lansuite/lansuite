<?php

if ($auth['userid'] == 0 && $cfg['paypal_donation'] == 0) {
    $func->error(t('Du kannst nichts einzahlen wenn du nicht eingeloggt bist.'), "index.php?mod=home");
} else {
    $paypalObj = new \LanSuite\Module\PayPal\PayPal();
    $paypalObj->initAccessToken();
    $payment = $paypalObj->getPayment($_SESSION['paypal_payment_id']);

    if ($payment->getState() == 'created' && !isset($_GET['failed'])) {
        $dsp->NewContent(t('Ergebnis der Zahlung'), t('So siehts aus!'));
        try {
            $status = $paypalObj->executePayment($_GET['PayerID']);
            if ($status='success') {
               // Get Items that were paid from the payment...
                $items = $paypalObj->getItemsFromPayment();
                $seat2 = new \LanSuite\Module\Seating\Seat2();
                $mail = new \LanSuite\Module\Mail\Mail();
                $UsrMgr = new\LanSuite\Module\UsrMgr\UserManager(new \LanSuite\Module\Mail\Mail());
                $GuestList = new \LanSuite\Module\GuestList\GuestList($seat2,$UsrMgr);
               // Show list of items. There has to be at least one because the payment succeeded
                $dsp->addSingleRow('Die folgenden Artikel wurden erfolgreich bezahlt:');
                foreach ($items as $item) {
                    $dsp->addDoubleRow($item->quantity.'x '.$item->description, $item->value);
                    switch ($item->sku) {
                        case 'DONATION':
                            $accounting = new \LanSuite\Module\CashMgr\Accounting(0, $auth['userid']);
                            $accounting->booking($item->value, t('Spende von').' '.$auth['username'], 0, true);
                            break;
                        case 'CATERING':
                            $accounting = new \LanSuite\Module\Foodcenter\Accounting($auth['userid']);
                            $accounting->change($item->value, t('Einzahlung via PayPal'), $auth['userid']); //this is misleading. Account value is not changed, but a transaction added
                            break;
                        default:
                            $data = SKUtoParty($item->sku);
                            $seating = new \LanSuite\Module\Seating\Seat2();
                            $mail = new \LanSuite\Module\Mail\Mail();
                            $userManager = new \LanSuite\Module\UsrMgr\UserManager($mail);
                            $GuestList = new \LanSuite\Module\GuestList\GuestList($seating, $userManager);
                            $GuestList->SetPaid($auth['userid'], $data['party_id']);
                            break;
                    }
                }
            } else {
                $dsp->NewContent(t('Zahlung fehlgeschlagen'), t('Leider war die Zahlung nicht erfolgreich!'));
                $func->error(t('Die PayPal-Zahlung wurde abgebrochen'));
            }
        } catch (PayPal\Exception\PayPalConnectionException) {
            $dsp->NewContent(t('Zahlung fehlgeschlagen'), t('Leider war die Zahlung nicht erfolgreich!'));
            $func->error(t('Die PayPal-Zahlung wurde abgebrochen'));
        }
    } else {
        $dsp->NewContent(t('Zahlung abgebrochen'), t('Die PayPal-Zahlung wurde abgebrochen'));
        $func->error(t('Die PayPal-Zahlung wurde abgebrochen'));
    }
}
