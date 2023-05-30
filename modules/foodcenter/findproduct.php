<?php

$product_list = new LanSuite\Module\Foodcenter\ProductList();
$dsp->NewContent(t('Produktsuche'), t('Hier findest du alles was das Herz begehrt'));

$ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2();
$ms2->query['from'] = "%prefix%food_product AS p
                       LEFT JOIN %prefix%food_option AS o ON o.parentid = p.id";

$cat_list = array('' => 'Alle');
$row = $db->qry("SELECT * FROM %prefix%food_cat");
while ($res = $db->fetch_array($row)) {
    $cat_list[$res['cat_id']] = $res['name'];
}

$db->free_result($row);

$ms2->AddTextSearchDropDown('Produktkategorie', 'p.cat_id', $cat_list);
$ms2->AddTextSearchField('Produktsuche', array('p.caption' => 'like', 'p.p_desc' => 'like'));

$ms2->AddSelect('p.cat_id');
$ms2->AddResultField('Titel', 'p.id', 'GetTitelName');

$ms2->NoItemsText = match ($_POST['search_dd_input'][0]) {
    1 => t('Keine aktuellen Bestellungen vorhanden.'),
    2 => t('Es müssen keine Produkte bestellt werden.'),
    3 => t('Es wird auf keine Lieferung gewartet.'),
    4 => t('Derzeit gibt es keine fertiggestellten Gerichte aus der Küche.'),
    5 => t('Du hast alle Produkte abgeholt.'),
    default => t('Keine aktuellen Bestellungen vorhanden.'),
};

$ms2->PrintSearch('index.php?mod=foodcenter&action=findproduct', 'p.id');
