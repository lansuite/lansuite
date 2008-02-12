<?php


include_once("modules/foodcenter/class_product.php");
include_once("modules/foodcenter/class_basket.php");

//Prüfe öffungszeiten 
$time = time();
if ($cfg['foodcenter_foodtime'] == 4){
	$open = true;
}elseif($cfg['foodcenter_s_time_1'] < $time && $cfg['foodcenter_e_time_1'] > $time ){
	$open = true;
}elseif ($cfg['foodcenter_s_time_2'] < $time && $cfg['foodcenter_e_time_2'] > $time ){
	$open = true;
}elseif ($cfg['foodcenter_s_time_3'] < $time && $cfg['foodcenter_e_time_3'] > $time ){
	$open = true;
}else{
	$open = false;
	$timemessage = $func->unixstamp2date($cfg['foodcenter_s_time_1'],'datetime') . " - ";
	$timemessage .= $func->unixstamp2date($cfg['foodcenter_e_time_1'],'datetime') . HTML_NEWLINE;
	if($cfg['foodcenter_s_time_2'] != $cfg['foodcenter_e_time_2']){
		$timemessage .= $func->unixstamp2date($cfg['foodcenter_s_time_2'],'datetime') . " - ";
		$timemessage .= $func->unixstamp2date($cfg['foodcenter_e_time_2'],'datetime') . HTML_NEWLINE;
	}
	if($cfg['foodcenter_s_time_3'] != $cfg['foodcenter_e_time_3']){
		$timemessage .= $func->unixstamp2date($cfg['foodcenter_s_time_3'],'datetime') . " - ";
		$timemessage .= $func->unixstamp2date($cfg['foodcenter_e_time_3'],'datetime') . HTML_NEWLINE;
	}
}
// Modul gesperrt
if($open == false && $cfg['foodcenter_foodtime'] == 3){
	$errormessage = $lang['foodcenter']['time_closed_block']. HTML_NEWLINE; 
	$errormessage .= $timemessage;
	
	$func->error($errormessage,"index.php?mod=home");
	
}else{
	$basket = new basket();
	// InfoMeldung
	if($open == false && $cfg['foodcenter_foodtime'] == 1) {
		$errormessage = $lang['foodcenter']['time_closed_info']. HTML_NEWLINE;
		$errormessage .= $timemessage;
		$func->error($errormessage,"index.php?mod=home");
	}
	// Bestellungen sperren
	if($open == false && $cfg['foodcenter_foodtime'] == 2){
		$errormessage = $lang['foodcenter']['time_closed_onlyshow']. HTML_NEWLINE;
		$errormessage .= $timemessage;
		$func->error($errormessage,"index.php?mod=home");
	}else{
		$basket->add_to_basket_from_global();
	}
	// Productgroups
	$row = $db->query("SELECT * FROM {$config['tables']['food_cat']}");		
	$i = 1;
	while ($data = $db->fetch_array($row)){
			$menus[$i]	= $data['name'];
			$cat[$i] 	= $data['cat_id'];
			$i++;
	}
	
	if(!isset($_GET['headermenuitem'])) $_GET['headermenuitem'] = 1;
	$dsp->NewContent($lang['foodcenter']['show_product_catpion']);


	$product_list = new product_list();
	
	if($basket->count > 0){
		$dsp->AddSingleRow("<a href='index.php?mod=foodcenter&action=basket'>" . $basket->count . $lang['foodcenter']['basket_product_item'] . "</a>"," align=\"right\"");
	}
	if($_GET['info']){
		$product_list->load_cat($cat[$_GET['headermenuitem']]);
		$product_list->get_info($_GET['info'],"?mod=foodcenter&action=showfood&headermenuitem={$_GET['headermenuitem']}");
	}else{
		if(is_numeric($cat[$_GET['headermenuitem']])){
			$dsp->AddHeaderMenu($menus,"?mod=foodcenter",$_GET['headermenuitem']);
			$product_list->load_cat($cat[$_GET['headermenuitem']]);
			$product_list->get_list("?mod=foodcenter&action=showfood&headermenuitem={$_GET['headermenuitem']}");
		}else{
			$dsp->AddSingleRow($lang['foodcenter']['show_product_noproducts']);
		}
	}
	$dsp->AddContent();
}
	
?>