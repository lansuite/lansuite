<?php


include_once("modules/foodcenter/class_product.php");
include_once("modules/foodcenter/class_basket.php");

	$basket = new basket();
	$basket->add_to_basket_from_global();
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
	
	
?>