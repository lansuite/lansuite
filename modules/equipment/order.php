<?php
ob_start();

if (!$cfg["equipment_shopid"]) {
    $func->error(t('Es wurde noch keine Orgapage.Net-ShopID angegeben. Diese kann auf der Admin-Seite in den Moduleinstellungen unter \'Equipmentshop\' eingestellt werden'));
} elseif (!ini_get('allow_url_fopen') or !ini_get('allow_url_include')) {
    $func->error(t('In der php.ini muss sowohl allow_url_fopen, als auch allow_url_include auf On gesetzt sein'));
} else {
    $post = "";
    reset($_POST);
    while (list($key, $val) = each($_POST)) {
        if ($key == "equip") {
            reset($val);
            while (list($key2, $val2) = each($val)) {
                $post .= "&equip[$key2]=$val2";
            }
        } else {
            $post .= "&$key=$val";
        }
    }

    include "http://www.orgapage.net/pages/equip/shops/order.php?param=mod&param_val=equipment&action={$_GET["action"]}&id={$cfg["equipment_shopid"]}$post";

    if (!strpos(ob_get_contents(), "<table ")) {
        $func->error(t('Es konnten keine Daten abgerufen werden. Evtl. ist der Orgapage.Net-Server momentan nicht erreichbar'));
    } else {
        $dsp->NewContent(t('Bestellformular'), t('Hier kannst du Equipment für deine Party mieten.'));
        $dsp->AddSmartyTpl('style', 'equipment');
        $dsp->AddSingleRow(ob_get_contents());
        $dsp->AddContent();
    }
}
ob_end_clean();
