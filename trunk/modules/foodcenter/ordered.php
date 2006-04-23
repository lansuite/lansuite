<?php
include_once("modules/foodcenter/class_product.php");
$product_list = new product_list();

include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2('news');

$ms2->query['from'] = "{$config['tables']['food_ordering']} AS a
  LEFT JOIN {$config['tables']['food_product']} AS p ON a.productid = p.id";
$ms2->query['where'] = 'userid='. (int)$auth['userid'];

$ms2->AddTextSearchField('Titel', array('p.caption' => 'like'));
$ms2->AddTextSearchDropDown('Status', 'a.status', array('1' => 'Wird bestellt', '2' => 'Lieferung erwartet', '3' => 'Abholbereit', '4' => 'abgeholt'), 3);

$ms2->AddResultField('Titel', 'p.caption');
$ms2->AddResultField('Beschreibung', 'a.opts');
$ms2->AddResultField('Bestellt', 'a.ordertime', 'MS2GetDate');
$ms2->AddResultField('Geliefert', 'a.supplytime', 'MS2GetDate');
$ms2->AddResultField('Anzahl', 'a.pice');

#$ms2->AddIconField('details', 'index.php?mod=foodcenter&action=ordered&step=2&id=', $lang['ms2']['details']);

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

$ms2->PrintSearch('index.php?mod=foodcenter&action=ordered', 'p.id');
?>