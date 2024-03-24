<?php

$basket = new LanSuite\Module\Foodcenter\Basket();

// Check opening times
$time = time();
$configFoodcenterFoodtime = $cfg['foodcenter_foodtime'] ?? 4;
$configFoodcenterSTime1 = $cfg['foodcenter_s_time_1'] ?? 4;
$configFoodcenterSTime2 = $cfg['foodcenter_s_time_2'] ?? 4;
$configFoodcenterSTime3 = $cfg['foodcenter_s_time_3'] ?? 4;
$configFoodcenterETime1 = $cfg['foodcenter_e_time_1'] ?? 4;
$configFoodcenterETime2 = $cfg['foodcenter_e_time_2'] ?? 4;
$configFoodcenterETime3 = $cfg['foodcenter_e_time_3'] ?? 4;

if ($configFoodcenterFoodtime == 4) {
    $open = true;
} elseif ($configFoodcenterSTime1 < $time && $configFoodcenterETime1 > $time) {
    $open = true;
} elseif ($configFoodcenterSTime2 < $time && $cfg['foodcenter_e_time_2'] > $time) {
    $open = true;
} elseif ($configFoodcenterSTime3 < $time && $cfg['foodcenter_e_time_3'] > $time) {
    $open = true;
} else {
    $open = false;
    
    $timemessage = $func->unixstamp2date($configFoodcenterSTime1, 'datetime') . " - ";
    $timemessage .= $func->unixstamp2date($configFoodcenterETime1, 'datetime') . HTML_NEWLINE;

    if ($configFoodcenterSTime2 != $configFoodcenterETime2) {
        $timemessage .= $func->unixstamp2date($configFoodcenterSTime2, 'datetime') . " - ";
        $timemessage .= $func->unixstamp2date($configFoodcenterETime2, 'datetime') . HTML_NEWLINE;
    }

    if ($configFoodcenterSTime3 != $configFoodcenterETime3) {
        $timemessage .= $func->unixstamp2date($configFoodcenterSTime3, 'datetime') . " - ";
        $timemessage .= $func->unixstamp2date($configFoodcenterETime3, 'datetime') . HTML_NEWLINE;
    }
}

// Module closed
if ($open == false && ($configFoodcenterFoodtime == 3 || $configFoodcenterFoodtime == 2)) {
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

    $calculateParameter = $_POST['calculate'] ?? '';
    if ($calculateParameter != '') {
        $basket->change_basket($auth['userid']);
    }

    $imageFieldParameter = $_POST['imageField'] ?? '';
    if ($imageFieldParameter != '') {
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
