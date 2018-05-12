<?php

$basket = new LanSuite\Module\Foodcenter\Basket();

// Check opening times
$time = time();
if ($cfg['foodcenter_foodtime'] == 4) {
    $open = true;
} elseif ($cfg['foodcenter_s_time_1'] < $time && $cfg['foodcenter_e_time_1'] > $time) {
    $open = true;
} elseif ($cfg['foodcenter_s_time_2'] < $time && $cfg['foodcenter_e_time_2'] > $time) {
    $open = true;
} elseif ($cfg['foodcenter_s_time_3'] < $time && $cfg['foodcenter_e_time_3'] > $time) {
    $open = true;
} else {
    $open = false;
    
    $timemessage = $func->unixstamp2date($cfg['foodcenter_s_time_1'], 'datetime') . " - ";
    $timemessage .= $func->unixstamp2date($cfg['foodcenter_e_time_1'], 'datetime') . HTML_NEWLINE;

    if ($cfg['foodcenter_s_time_2'] != $cfg['foodcenter_e_time_2']) {
        $timemessage .= $func->unixstamp2date($cfg['foodcenter_s_time_2'], 'datetime') . " - ";
        $timemessage .= $func->unixstamp2date($cfg['foodcenter_e_time_2'], 'datetime') . HTML_NEWLINE;
    }

    if ($cfg['foodcenter_s_time_3'] != $cfg['foodcenter_e_time_3']) {
        $timemessage .= $func->unixstamp2date($cfg['foodcenter_s_time_3'], 'datetime') . " - ";
        $timemessage .= $func->unixstamp2date($cfg['foodcenter_e_time_3'], 'datetime') . HTML_NEWLINE;
    }
}

// Module closed
if ($open == false && ($cfg['foodcenter_foodtime'] == 3 || $cfg['foodcenter_foodtime'] == 2)) {
    $errormessage = t('Das Foodcenter ist geschlossen. Die Öffnungszeigen sind:'). HTML_NEWLINE;
    $errormessage .= $timemessage;
    
    $func->error($errormessage, "index.php?mod=home");
} else {
    $basket = new LanSuite\Module\Foodcenter\Basket();

    if ($open == false && $cfg['foodcenter_foodtime'] == 1) {
        $errormessage = t('Das Foodcenter ist geschlossen Bestellungen sind möglich werden aber erst nach Öffnung abgearbeitet.Die Öffnungszeigen sind:'). HTML_NEWLINE;
        $errormessage .= $timemessage;
        $func->error($errormessage, "index.php?mod=home");
    }

    if ($_POST['calculate'] != '') {
        $basket->change_basket($auth['userid']);
    }

    if ($_POST['imageField'] != '') {
        if ($basket->change_basket($auth['userid'])) {
            $basket->order_basket($auth['userid']);
            $func->confirmation(t('Die Bestellung wurde aufgenommen'), "index.php?mod=foodcenter");
        } else {
            $basket->show_basket();
        }
    } else {
        $basket->show_basket();
    }
}
