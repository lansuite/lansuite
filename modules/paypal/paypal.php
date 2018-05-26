<?php

/**
 * Get form to see what you must pay
 */
if ($auth['userid'] == 0 && $cfg['paypal_donation'] == 0) {
    $func->error(t('Du kannst nichts einzahlen wenn du nicht eingeloggt bist.'), "index.php?mod=home");
} else {
    $dsp->NewContent(t('Einzahlen'), t('Hier sehen sie was f&uuml;r Betr&auml;ge noch ausstehend sind. W&auml;hlen sie was sie bezahlen m&ouml;chten.'));
    $dsp->AddSmartyTpl('javascript', 'paypal');
    $dsp->SetForm("index.php?mod=paypal&action=createpayment", "paypal");

    // List all partys
    if ($auth['userid'] != 0) {
        $pay_partys = $db->qry("
          SELECT
            *
          FROM %prefix%party_user AS pu
          LEFT JOIN %prefix%partys AS p USING(party_id)
          LEFT JOIN %prefix%party_prices AS price ON price.price_id=pu.price_id
          WHERE
            user_id=%int%
            AND p.senddate > now()
            AND paid = '0'", $auth['userid']);

        if ($db->num_rows($pay_partys) > 0) {
            while ($pay = $db->fetch_array($pay_partys)) {
                $dsp->AddCheckBoxRow("price[]", $pay['name'], $pay['price_text'] . " " . $pay['price'] . " " . $cfg['paypal_currency_code'], "", null, true, null, $pay['price_id']);
                if ($cfg['paypal_depot'] && $pay['depot_price'] > 0) {
                    $dsp->AddCheckBoxRow("depot[]", $pay['name'], $pay['depot_text'] . " " . $pay['depot_price'] . " " . $cfg['paypal_currency_code'], "", null, null, null, $pay['price_id']);
                }
            }
        } else {
            $dsp->AddSingleRow(t('Alle Eintrittspreise bezahlt.'));
        }

        if ($cfg['paypal_catering']) {
            $dsp->AddTextFieldRow("catering", t('Einzahlung f&uuml;r Catering'), 0, "");
        }
    }

    if ($cfg['paypal_donation']) {
        $dsp->AddTextFieldRow("donation", t('Spende f&uuml;r die Organisatoren'), 0, "");
    }

    $dsp->AddFormSubmitRow(t('Weiter'));
    $db->free_result($pay_partys);
}
