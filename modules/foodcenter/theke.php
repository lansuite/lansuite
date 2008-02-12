<?php


include_once("modules/foodcenter/class_basket.php");
include_once("modules/foodcenter/class_product.php");

$basket = new basket();
$basket->add_to_basket_from_global();


if(isset($_GET['userid'])) $_SESSION['foodcenter']['theke_userid'] = $_GET['userid'];

if($_GET['step'] == "del"){
	unset($_SESSION['foodcenter']['theke_userid']);
	unset($_SESSION['basket_item']['product']);
}

if(!isset($_SESSION['foodcenter']['theke_userid'])){

	if($cfg['sys_barcode_on']){
		$dsp->AddBarcodeForm("<strong>" . $lang['barcode']['barcode'] . "</strong>","","index.php?mod=foodcenter&action=theke&userid=");
	}
	$mastersearch = new MasterSearch($vars, "index.php?mod=foodcenter&action=theke", "index.php?mod=foodcenter&action=theke&userid=", "GROUP BY email");
	$mastersearch->LoadConfig("users", $lang['usrmgr']['ms_search'], $lang['usrmgr']['ms_result']);
	$mastersearch->PrintForm();
	$mastersearch->Search();
	$mastersearch->PrintResult();

	$templ['index']['info']['content'] .= $mastersearch->GetReturn();

	
}else{
	
	
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
	$user_theke = $db->query_first("SELECT username FROM {$config["tables"]["user"]} WHERE userid = {$_SESSION['foodcenter']['theke_userid']}");
	$dsp->AddDoubleRow(HTML_FONT_ERROR . $lang['foodcenter']['theke_user'] . HTML_FONT_END,"<table border=\"0\" width=\"100%\"><tr><td>{$user_theke['username']}</td><td align=\"right\"><a href=\"index.php?mod=foodcenter&action=theke&step=del\">{$lang['foodcenter']['theke_exit']}</a></td></tr></table>");


	$product_list = new product_list();

	if($_GET['info']){
		$product_list->load_cat($cat[$_GET['headermenuitem']]);
		$product_list->get_info($_GET['info'],"?mod=foodcenter&action=theke&headermenuitem={$_GET['headermenuitem']}");
	}else{
		if(is_numeric($cat[$_GET['headermenuitem']])){
			$dsp->AddHeaderMenu($menus,"?mod=foodcenter&action=theke",$_GET['headermenuitem']);
			$product_list->load_cat($cat[$_GET['headermenuitem']]);
			$product_list->get_list("?mod=foodcenter&action=theke&headermenuitem={$_GET['headermenuitem']}");
		}else{
			$dsp->AddSingleRow($lang['foodcenter']['show_product_noproducts']);
		}
	}
	$dsp->AddContent();



	if($_POST['calculate_x']){
		$basket->change_basket($_SESSION['foodcenter']['theke_userid']);
	}



	if($_POST['imageField_x'] && !isset($_GET['add'])){
		if($basket->change_basket($_SESSION['foodcenter']['theke_userid'])){
			$basket->order_basket($_SESSION['foodcenter']['theke_userid'],$_POST['delivered']);
			$func->information($lang['foodcenter']['basket_ordered'],"?mod=foodcenter&action=theke");
		}else{
			$basket->show_basket();
		}
	}else{
		$basket->show_basket();
	}


}


?>