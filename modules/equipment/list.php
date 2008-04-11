<?
ob_start();

if (!$cfg["equipment_shopid"]) $func->error($lang["equipment"]["err_noid"], "");
else {
	include "http://www.orgapage.net/pages/equip/shops/liste.php?action={$_GET["action"]}&pack_id={$_GET["pack_id"]}&art_id={$_GET["art_id"]}&id={$cfg["equipment_shopid"]}";

	if (!strpos(ob_get_contents(), "<table ")) $func->error($lang["equipment"]["err_nodata"], "");
	else {
		$dsp->NewContent($lang["equipment"]["list_caption"], $lang["equipment"]["list_subcaption"]);
		$dsp->AddModTpl("equipment", "style");
		$dsp->AddSingleRow(ob_get_contents());
		$dsp->AddContent();
	}
}
ob_end_clean();
?>