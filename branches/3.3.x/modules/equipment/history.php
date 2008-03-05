<?
ob_start();

if (!$cfg["equipment_shopid"]) $func->error($lang["equipment"]["err_noid"], "");
else {
	include "http://www.orgapage.net/pages/equip/shops/termine.php?action=history&id={$cfg["equipment_shopid"]}";

	if (!strpos(ob_get_contents(), "<table ")) $func->error($lang["equipment"]["err_nodata"], "");
	else {
		$dsp->NewContent($lang["equipment"]["history_caption"], $lang["equipment"]["history_subcaption"]);
		$dsp->AddModTpl("equipment", "style");
		$dsp->AddSingleRow(ob_get_contents());
		$dsp->AddContent();
	}
}
ob_end_clean();
?>