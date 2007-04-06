<?php
include_once("modules/foodcenter/class_product.php");
include_once('modules/mastersearch2/class_mastersearch2.php');
$product_list = new product_list();

$dsp->NewContent(t('Bestellungen'), t('Auflistung deiner aktiven und abgeschlossenen Catering-Bestellungen'));

$ms2 = new mastersearch2('news');

$ms2->query['from'] = "{$config['tables']['food_ordering']} AS a
			LEFT JOIN {$config['tables']['food_status']} AS s ON a.status = s.id 
			LEFT JOIN {$config['tables']['food_product']} AS p ON a.productid = p.id 
			LEFT JOIN {$config['tables']['food_option']} AS o ON a.opts = o.id";
$ms2->query['where'] = 'userid='. (int)$auth['userid'];

$ms2->AddTextSearchField('Titel', array('p.caption' => 'like'));

  	$status = $db->query("SELECT * FROM lansuite_food_status");
  	$status_array[''] = $lang['ms']['select_all'];
  	while ($statusrows = $db->fetch_array($status)) {
  		$status_array[$statusrows['id']] = $statusrows['statusname'];
  	}
    $ms2->AddTextSearchDropDown('Status', 'a.status', $status_array);

$ms2->AddResultField('Titel', 'p.caption');
$ms2->AddResultField('Einheit', 'o.unit');
$ms2->AddResultField('Anzahl', 'a.pice');
$ms2->AddResultField('Preis', 'o.price');
$ms2->AddResultField('Bestellt', 'a.ordertime', 'MS2GetDate');
$ms2->AddResultField('Letzte änderung', 'a.lastchange', 'MS2GetDate');
$ms2->AddResultField('Geliefert', 'a.supplytime', 'MS2GetDate');
$ms2->AddResultField('Status', 's.statusname');


//$ms2->AddIconField('details', 'index.php?mod=foodcenter&action=ordered&step=2&id=', $lang['ms2']['details']);

switch ($_POST['search_dd_input'][0]){
	case 1:
    $dsp->NewContent($lang['foodcenter']['list_order'], '');
		$ms2->NoItemsText = $lang['foodcenter']['ordered_no_stop'];
	break;
	
	case 2:
    $dsp->NewContent($lang['foodcenter']['list_ordered'], '');
		$ms2->NoItemsText = $lang['foodcenter']['ordered_no_supplied'];
	break;
		
	case 3:
    $dsp->NewContent($lang['foodcenter']['list_fetch'], '');
		$ms2->NoItemsText = $lang['foodcenter']['ordered_no_wait'];
	break;
		
	case 4:
    $dsp->NewContent($lang['foodcenter']['list_fetched'], '');
		$ms2->NoItemsText = $lang['foodcenter']['ordered_no_supply'];
	break;
}

$ms2->PrintSearch('index.php?mod=foodcenter&action=ordered', 'a.id');
?>