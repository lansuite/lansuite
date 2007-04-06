<?php
include_once("modules/mastersearch2/class_mastersearch2.php");

$dsp->NewContent(t('Küche'), t('Auflistung derzeitiger unbearbeiter Produktionsauftraege'));

$ms2 = new mastersearch2('kitchen');

$ms2->query['from'] = "{$config["tables"]["food_ordering"]} AS o 
					INNER JOIN {$config["tables"]["food_product"]} AS p ON p.id = o.productid
					INNER JOIN {$config["tables"]["user"]} AS u ON u.userid = o.userid";
$ms2->query['where'] = "p.supp_id= '{$cfg['foodcenter_kitchen']}' AND o.status = '1' OR o.status = '2'";

//$ms2->query['default_order_by'] = 'DATE DESC';
//$ms2->config['EntriesPerPage'] = 20;

//$ms2->AddTextSearchField(t('Status'), array('o.status' => 'exact'));

$ms2->AddSelect('u.userid');
$ms2->AddResultField(t('Produkt'), 'p.caption');
$ms2->AddResultField(t('Anzahl'), 'o.pice');
$ms2->AddResultField(t('Bestelldatum'), 'o.ordertime', 'MS2GetDate');
$ms2->AddResultField(t('Besteller'), 'u.username', 'UserNameAndIcon');

$ms2->AddIconField('change', 'index.php?mod=foodcenter&action=kitchen&step=1&orderid=', t('Fertigstellen'));

$ms2->PrintSearch('index.php?mod=foodcenter&action=kitchen', 'o.id');

if($_GET['step'] == 1)
	$db->query("UPDATE {$config["tables"]["food_ordering"]} SET status = '4' WHERE id = '{$_GET["orderid"]}'");

?>