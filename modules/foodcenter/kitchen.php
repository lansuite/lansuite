<?php

$pathInfo = $request->getPathInfo();
$autoRefreshSwitch = $_GET['autorefresh'] ?? 0;
$autoRefreshInterval = $cfg['autorefresh'] ?? 10;
if ($autoRefreshSwitch) {
    // TODO Set the refresh interval via framework class and not via echo
    echo('<meta http-equiv="refresh" content=" ' . intval($autoRefreshInterval) . '"; URL="' . $pathInfo . '"index.php?mod=foodcenter&action=kitchen&autorefresh=1">');
}

$dsp->NewContent(t('Küche'), t('Auflistung derzeitiger unbearbeiter Produktionsauftraege'));
$ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2();

$configFoodcenterKitchen = $cfg['foodcenter_kitchen'] ?? 0;
$ms2->query['from'] = "%prefix%food_ordering AS o 
                       INNER JOIN %prefix%food_product AS p ON p.id = o.productid
                       INNER JOIN %prefix%user AS u ON u.userid = o.userid";
$ms2->query['where'] = "p.supp_id= '{$configFoodcenterKitchen}' AND (o.status = '1' OR o.status = '2')";

$ms2->AddSelect('u.userid');
$ms2->AddResultField(t('Produkt'), 'p.caption');
$ms2->AddResultField(t('Anzahl'), 'o.pice');
$ms2->AddResultField(t('Bestelldatum'), 'o.ordertime', 'MS2GetDate');
$ms2->AddResultField(t('Besteller'), 'u.username', 'UserNameAndIcon');
$ms2->AddIconField('change', 'index.php?mod=foodcenter&action=kitchen&step=1&orderid=', t('Fertigstellen'));
$ms2->PrintSearch('index.php?mod=foodcenter&action=kitchen', 'o.id');

$stepParameter = $_GET['step'] ?? 0;
if ($stepParameter == 1) {
    $database->query("UPDATE %prefix%food_ordering SET status = '4' WHERE id = ?", [$_GET["orderid"]]);
}

// Display autorefresh status and control link
if (!$autoRefreshSwitch) {
    $dsp->AddSingleRow("<img src=\"ext_inc/foodcenter/refresh_off.gif\"> <b>Autorefresh:</b> <font color=red><b>". t('AUS') ."</b></font> (<a href=\"" . $pathInfo . "index.php?mod=foodcenter&action=kitchen&autorefresh=1\">". t('Aktivieren') ."</a>)<br>\n");
} elseif ($autoRefreshSwitch) {
    $dsp->AddSingleRow("<img src=\"ext_inc/foodcenter/refresh_on.gif\"> <b>Autorefresh:</b> <font color=green><b>". t('AN') ."</b></font> (<a href=\"" . $pathInfo . "index.php?mod=foodcenter&action=kitchen&autorefresh=0\">". t('Deaktivieren') ."</a>)<br>\n");
}
