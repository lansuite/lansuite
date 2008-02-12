<?php
include_once("modules/foodcenter/class_product.php");
$product_list = new product_list();

if(!isset($vars['search_select1'])) $vars['search_select1'] = 3;
		
	$mastersearch = new MasterSearch($vars, "index.php?mod=foodcenter&action=ordered", "index.php?mod=foodcenter&action=ordered&step=2&id=", "AND userid=" . $auth['userid']);

	switch ($vars['search_select1']){
		case 1:
			$mastersearch->config['title']	= $lang['foodcenter']['list_order'];
			$mastersearch->config['no_items_caption'] = $lang['foodcenter']['ordered_no_stop'];
		break;
		
		case 2:
			$mastersearch->config['title']	= $lang['foodcenter']['list_ordered'];
			$mastersearch->config['no_items_caption'] = $lang['foodcenter']['ordered_no_supplied'];
		break;
			
		case 3:
			$mastersearch->config['title']	= $lang['foodcenter']['list_fetch'];
			$mastersearch->config['no_items_caption'] = $lang['foodcenter']['ordered_no_wait'];
		break;
			
		case 4:
			$mastersearch->config['title']	= $lang['foodcenter']['list_fetched'];
			$mastersearch->config['no_items_caption'] = $lang['foodcenter']['ordered_no_supply'];
		break;
		}

	$mastersearch->LoadConfig("food_ordered", $lang['usrmgr']['ms_search'], $lang['usrmgr']['ms_result']);
	$mastersearch->Search();
	$mastersearch->PrintResult();
	$mastersearch->PrintForm();		
	$templ['index']['info']['content'] .= $mastersearch->GetReturn();

?>