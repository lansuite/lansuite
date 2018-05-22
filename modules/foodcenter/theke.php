<?php

$basket = new LanSuite\Module\Foodcenter\Basket();
$basket->add_to_basket_from_global();

// Get Barcode if exists and translate to userid
if ($_POST['barcodefield']) {
    $row = $db->qry_first('SELECT userid FROM %prefix%user WHERE barcode = %string%', $_POST["barcodefield"]);
    $_GET['userid']=$row['userid'];
}

if (isset($_GET['userid'])) {
    $_SESSION['foodcenter']['theke_userid'] = $_GET['userid'];
}

if ($_GET['step'] == "del") {
    unset($_SESSION['foodcenter']['theke_userid']);
    unset($_SESSION['basket_item']['product']);
}

if (!isset($_SESSION['foodcenter']['theke_userid'])) {
    if ($cfg['sys_barcode_on']) {
        $dsp->AddBarcodeForm("<strong>" . t('Strichcode') . "</strong>", "", "index.php?mod=foodcenter&action=theke&userid=");
    }
    
    if (!isset($_POST['search_dd_input'][2])) {
        $_POST['search_dd_input'][2] = ">1";
    }

    if (!isset($_POST['search_dd_input'][3])) {
        $_POST['search_dd_input'][3] = "0";
    }

    $current_url = 'index.php?mod=foodcenter&action=theke';
    $target_url = 'index.php?mod=foodcenter&action=theke&userid=';
    include_once('modules/foodcenter/search.inc.php');
} else {
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
    $user_theke = $db->qry_first("SELECT username FROM %prefix%user WHERE userid = %int%", $_SESSION['foodcenter']['theke_userid']);
    $dsp->AddDoubleRow(HTML_FONT_ERROR . t('Ausgewählter Benutzer:') . HTML_FONT_END, "<table border=\"0\" width=\"100%\"><tr><td>{$user_theke['username']}</td><td align=\"right\"><a href=\"index.php?mod=foodcenter&action=theke&step=del\">".t('Exit')."</a></td></tr></table>");

    $product_list = new LanSuite\Module\Foodcenter\ProductList();

    if ($_GET['info']) {
        $product_list->load_cat($cat[$_GET['headermenuitem']]);
        $product_list->get_info($_GET['info'], "index.php?mod=foodcenter&action=theke&headermenuitem={$_GET['headermenuitem']}");
    } else {
        if (is_numeric($cat[$_GET['headermenuitem']])) {
            $dsp->AddHeaderMenu($menus, "index.php?mod=foodcenter&action=theke", $_GET['headermenuitem']);
            $product_list->load_cat($cat[$_GET['headermenuitem']]);
            $product_list->get_list("index.php?mod=foodcenter&action=theke&headermenuitem={$_GET['headermenuitem']}");
        } else {
            $dsp->AddSingleRow(t('In dieser Kategorie sind keine Produkte vorhanden'));
        }
    }

    if ($_POST['calculate']) {
        $basket->change_basket($_SESSION['foodcenter']['theke_userid']);
    }

    if ($_POST['imageField'] && !isset($_GET['add'])) {
        if ($basket->change_basket($_SESSION['foodcenter']['theke_userid'])) {
            $basket->order_basket($_SESSION['foodcenter']['theke_userid'], $_POST['delivered']);
            $func->information(t('Die Bestellung wurde aufgenommen'), "index.php?mod=foodcenter&action=theke");
        } else {
            $basket->show_basket();
        }
    } else {
        $basket->show_basket();
    }
}
