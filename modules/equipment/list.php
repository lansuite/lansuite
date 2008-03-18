<?
ob_start();

if (!$cfg["equipment_shopid"]) $func->error(t('Es wurde noch keine Orgapage.Net-ShopID angegeben. Diese kann auf der Admin-Seite in den Moduleinstellungen unter \'Equipmentshop\' eingestellt werden'), "");
else {
	include "http://www.orgapage.net/pages/equip/shops/liste.php?action={$_GET["action"]}&pack_id={$_GET["pack_id"]}&art_id={$_GET["art_id"]}&id={$cfg["equipment_shopid"]}";

	if (!strpos(ob_get_contents(), "<table ")) $func->error(t('Es konnten keine Daten abgerufen werden. Evtl. ist der Orgapage.Net-Server momentan nicht erreichbar'), "");
	else {
		$dsp->NewContent(t('Equipmentliste'), t('Hier siehst du eine Liste des LanParty-Equipment, dass wir vermieten'));
		$dsp->AddModTpl("equipment", "style");
		$dsp->AddSingleRow(ob_get_contents());
		$dsp->AddContent();
	}
}
ob_end_clean();
?>