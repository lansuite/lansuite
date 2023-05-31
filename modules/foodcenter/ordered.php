<?php

$product_list = new LanSuite\Module\Foodcenter\ProductList();
$dsp->NewContent(t('Bestellungen'), t('Auflistung deiner aktiven und abgeschlossenen Catering-Bestellungen'));

$ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2();
$ms2->query['from'] = "%prefix%food_ordering AS a
                       LEFT JOIN %prefix%food_status AS s ON a.status = s.id 
                       LEFT JOIN %prefix%food_product AS p ON a.productid = p.id 
                       LEFT JOIN %prefix%food_option AS o ON a.opts = o.id";
$ms2->query['where'] = 'userid='. (int)$auth['userid'];

$status_list = array('' => 'Alle');
$row = $db->qry("SELECT * FROM %prefix%food_status");
while ($res = $db->fetch_array($row)) {
    $status_list[$res['id']] = $res['statusname'];
}

$db->free_result($row);

$party_list = array('' => 'Alle');
$row = $db->qry("SELECT party_id, name FROM %prefix%partys");
while ($res = $db->fetch_array($row)) {
    $party_list[$res['party_id']] = $res['name'];
}

$db->free_result($row);

$ms2->AddTextSearchDropDown('Status', 'a.status', $status_list);
$ms2->AddTextSearchDropDown('Party', 'a.partyid', $party_list, $party->party_id);

$ms2->AddResultField('Titel', 'p.caption');
$ms2->AddResultField('Einheit', 'o.unit');
$ms2->AddResultField('Anzahl', 'a.pice');
$ms2->AddResultField('Preis', 'o.price', 'GetPriceFormat');
$ms2->AddResultField('Bestellt', 'a.ordertime', 'MS2GetDate');
$ms2->AddResultField('Letzte änderung', 'a.lastchange', 'MS2GetDate');
$ms2->AddResultField('Geliefert', 'a.supplytime', 'MS2GetDate');
$ms2->AddResultField('Status', 's.statusname');

$ms2->NoItemsText = match ($_POST['search_dd_input'][0]) {
    1 => t('Keine aktuellen Bestellungen vorhanden.'),
    2 => t('Es müssen keine Produkte bestellt werden.'),
    3 => t('Es wird auf keine Lieferung gewartet.'),
    4 => t('Derzeit gibt es keine fertiggestellten Gerichte aus der Küche.'),
    5 => t('Du hast alle Produkte abgeholt.'),
    default => t('Keine aktuellen Bestellungen vorhanden.'),
};

$ms2->PrintSearch('index.php?mod=foodcenter&action=ordered', 'a.id');
