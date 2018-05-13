<?php

if (isset($_GET['autorefresh'])) {
    $autorefresh = $_GET['autorefresh'];
} else {
    $autorefresh = 0;
}

if ($autorefresh == 1) {
    echo("<meta http-equiv=\"refresh\" content=\"". $cfg['autorefresh'] ."; URL=" . $_SERVER["PHP_SELF"] . "?mod=foodcenter&action=kitchen&autorefresh=1\">\n");
}

$dsp->NewContent(t('Küche'), t('Auflistung derzeitiger unbearbeiter Produktionsauftraege'));
$ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2();

$ms2->query['from'] = "%prefix%food_ordering AS o 
                       INNER JOIN %prefix%food_product AS p ON p.id = o.productid
                       INNER JOIN %prefix%user AS u ON u.userid = o.userid";
$ms2->query['where'] = "p.supp_id= '{$cfg['foodcenter_kitchen']}' AND (o.status = '1' OR o.status = '2')";

$ms2->AddSelect('u.userid');
$ms2->AddResultField(t('Produkt'), 'p.caption');
$ms2->AddResultField(t('Anzahl'), 'o.pice');
$ms2->AddResultField(t('Bestelldatum'), 'o.ordertime', 'MS2GetDate');
$ms2->AddResultField(t('Besteller'), 'u.username', 'UserNameAndIcon');
$ms2->AddIconField('change', 'index.php?mod=foodcenter&action=kitchen&step=1&orderid=', t('Fertigstellen'));
$ms2->PrintSearch('index.php?mod=foodcenter&action=kitchen', 'o.id');

if ($_GET['step'] == 1) {
    $db->qry("UPDATE %prefix%food_ordering SET status = '4' WHERE id = %string%", $_GET["orderid"]);
}

// Display autorefresh status and control link
if ($autorefresh == 0) {
    $dsp->AddSingleRow("<img src=\"ext_inc/teamspeak2/refresh_off.gif\"> <b>Autorefresh:</b> <font color=red><b>". t('AUS') ."</b></font> (<a href=\"" . $_SERVER["PHP_SELF"] . "index.php?mod=foodcenter&action=kitchen&autorefresh=1\">". t('Aktivieren') ."</a>)<br>\n");
} elseif ($autorefresh == 1) {
    $dsp->AddSingleRow("<img src=\"ext_inc/teamspeak2/refresh_on.gif\"> <b>Autorefresh:</b> <font color=green><b>". t('AN') ."</b></font> (<a href=\"" . $_SERVER["PHP_SELF"] . "index.php?mod=foodcenter&action=kitchen&autorefresh=0\">". t('Deaktivieren') ."</a>)<br>\n");
}
