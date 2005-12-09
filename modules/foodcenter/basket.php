<?php

include_once("modules/foodcenter/class_basket.php");
include_once("modules/foodcenter/class_product.php");
$basket = new basket();


if($_POST['calculate_x']){
	$basket->change_basket($auth['userid']);
}



if($_POST['imageField_x']){
	if($basket->change_basket($auth['userid'])){
		$basket->order_basket($auth['userid']);
		$func->information($lang['foodcenter']['basket_ordered'],"?mod=foodcenter");
	}else{
		$basket->show_basket();
	}
}else{
	$basket->show_basket();
}






?>