<?php

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

// Modul gesperrt
if ($open == false && $cfg['foodcenter_foodtime'] == 3) {
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

    if ($_GET['info']) {
        $product_list->load_cat($cat[$_GET['headermenuitem']]);
        $product_list->get_info($_GET['info'], "index.php?mod=foodcenter&action=showfood&headermenuitem={$_GET['headermenuitem']}");
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
