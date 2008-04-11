<?
ob_start();

if (!$cfg["equipment_shopid"]) $func->error($lang["equipment"]["err_noid"], "");
else {
	$post = "";
	reset ($_POST);
	while (list ($key, $val) = each ($_POST)) {
		if ($key == "equip") {
			reset ($val);
			while (list ($key2, $val2) = each ($val)) $post .= "&equip[$key2]=$val2";
		} else $post .= "&$key=$val";
	}

	include "http://www.orgapage.net/pages/equip/shops/order.php?param=mod&param_val=equipment&action={$_GET["action"]}&id={$cfg["equipment_shopid"]}$post";

	if (!strpos(ob_get_contents(), "<table ")) $func->error($lang["equipment"]["err_nodata"], "");
	else {
		$dsp->NewContent($lang["equipment"]["order_caption"], $lang["equipment"]["order_subcaption"]);
		$dsp->AddModTpl("equipment", "style");
		$dsp->AddSingleRow(ob_get_contents());
		$dsp->AddContent();
	}
}
ob_end_clean();
?>