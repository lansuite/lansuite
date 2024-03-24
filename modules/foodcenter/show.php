<?php

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
} elseif ($configFoodcenterSTime2 < $time && $configFoodcenterETime2 > $time) {
    $open = true;
} elseif ($configFoodcenterSTime3 < $time && $configFoodcenterETime3 > $time) {
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

// Modul gesperrt
if ($open == false && $configFoodcenterFoodtime == 3) {
    $errormessage = t('Das Foodcenter ist geschlossen. Die Öffnungszeigen sind:') . HTML_NEWLINE;
    $errormessage .= $timemessage;
    
    $func->error($errormessage, "index.php?mod=home");
} else {
    $basket = new LanSuite\Module\Foodcenter\Basket();

    // Info message
    if ($open == false && $cfg['foodcenter_foodtime'] == 1) {
        $errormessage = t('Das Foodcenter ist geschlossen. Bestellungen sind möglich werden aber erst nach Öffnung abgearbeitet.Die Öffnungszeigen sind:'). HTML_NEWLINE;
        $errormessage .= $timemessage;
        $func->error($errormessage, "index.php?mod=home");
    }

    // Close ordering
    if ($open == false && $cfg['foodcenter_foodtime'] == 2) {
        $errormessage = t('Das Foodcenter ist geschlossen. Die Produkte werden nicht im Warenkorb abgelegt. Die Öffnungszeigen sind:'). HTML_NEWLINE;
        $errormessage .= $timemessage;
        $func->error($errormessage, "index.php?mod=home");
    } else {
        $basket->add_to_basket_from_global();
    }

    // Product groups
    $row = $db->qry("SELECT * FROM %prefix%food_cat");
    $i = 1;
    while ($data = $db->fetch_array($row)) {
        $menus[$i]    = $data['name'];
        $cat[$i]    = $data['cat_id'];
        $i++;
    }
    
    if (!isset($_GET['headermenuitem'])) {
        $_GET['headermenuitem'] = 1;
    }
    $dsp->NewContent(t('Speiseliste'));

    $product_list = new LanSuite\Module\Foodcenter\ProductList();
    
    if ($basket->count > 0) {
        $dsp->AddSingleRow("<b><a href='index.php?mod=foodcenter&action=basket'>" . $basket->count . t(' Produkt(e) im Warenkorb') . "</a></b>", " align=\"right\"");
    }

    $infoParameter = $_GET['info'] ?? '';
    if ($infoParameter) {
        $product_list->load_cat($cat[$_GET['headermenuitem']]);
        $product_list->get_info($$infoParameter, "index.php?mod=foodcenter&action=showfood&headermenuitem={$_GET['headermenuitem']}");
    } else {
        if (is_numeric($cat[$_GET['headermenuitem']])) {
            $dsp->AddHeaderMenu($menus, "index.php?mod=foodcenter", $_GET['headermenuitem']);
            $product_list->load_cat($cat[$_GET['headermenuitem']]);
            $product_list->get_list("index.php?mod=foodcenter&action=showfood&headermenuitem={$_GET['headermenuitem']}");
        } else {
            $dsp->AddSingleRow(t('In dieser Kategorie sind keine Produkte vorhanden'));
        }
    }
}
