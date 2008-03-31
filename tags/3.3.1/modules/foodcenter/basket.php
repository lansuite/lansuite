<?php

include_once("modules/foodcenter/class_basket.php");
include_once("modules/foodcenter/class_product.php");
$basket = new basket();

//Pr�fe �ffungszeiten 
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
if($open == false && ($cfg['foodcenter_foodtime'] == 3 || $cfg['foodcenter_foodtime'] == 2)){
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
	if($_POST['calculate'] != ''){
		$basket->change_basket($auth['userid']);
	}

	if($_POST['imageField'] != ''){
		if($basket->change_basket($auth['userid'])){
			$basket->order_basket($auth['userid']);
			$func->information($lang['foodcenter']['basket_ordered'],"?mod=foodcenter");
		}else{
			$basket->show_basket();
		}
	}else{
		$basket->show_basket();
	}

}




?>