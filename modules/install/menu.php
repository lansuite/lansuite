<?php
switch($_GET["step"]) {
	// Move Up
	case 2:
		$db->query("UPDATE {$config["tables"]["menu"]} SET pos = 0 WHERE pos = ". ($_GET["pos"] - 1) ."");
		$db->query("UPDATE {$config["tables"]["menu"]} SET pos = pos - 1 WHERE pos = {$_GET["pos"]}");
		$db->query("UPDATE {$config["tables"]["menu"]} SET pos = {$_GET["pos"]} WHERE pos = 0");
	break;

	// Move Down
	case 3:
		$db->query("UPDATE {$config["tables"]["menu"]} SET pos = 0 WHERE pos = ". ($_GET["pos"] + 1) ."");
		$db->query("UPDATE {$config["tables"]["menu"]} SET pos = pos + 1 WHERE pos = {$_GET["pos"]}");
		$db->query("UPDATE {$config["tables"]["menu"]} SET pos = {$_GET["pos"]} WHERE pos = 0");
	break;

	// Add HRule Row
	case 4:
		$db->query("UPDATE {$config["tables"]["menu"]} SET pos = pos + 1 WHERE pos > {$_GET["pos"]}");
		$db->query("INSERT INTO {$config["tables"]["menu"]} SET caption = '--hr--', pos = ". ($_GET["pos"] + 1));
	break;

	// Delete
	case 5:
		$db->query("DELETE FROM {$config["tables"]["menu"]} WHERE pos = {$_GET["pos"]}");
	break;

	// Rewrite Menu Question
	case 6:
		$func->question($lang["install"]["menu_reset_navi_quest"], "index.php?mod=install&action=menu&step=7&onlyactive={$_GET["onlyactive"]}", "index.php?mod=install&action=menu&onlyactive={$_GET["onlyactive"]}");
	break;

	// Rewrite Menu Action
	case 7:
		$db->query_first("DELETE FROM {$config["tables"]["menu"]}");
		$install->InsertMenus(1);
	break;

	// Change Group Action
	case 9:
		$menu = $db->query("UPDATE {$config["tables"]["menu"]} SET group_nr = ". (int)$_POST["group"] ." WHERE pos = {$_GET["pos"]}");
	break;

	// Set Possition
	case 10:
		if ($_POST['pos']) foreach ($_POST['pos'] as $key => $val) {
			$db->qry('UPDATE %prefix%menu SET pos = %int% WHERE id = %int%', $val, $key);
		}
		if ($_POST['group']) foreach ($_POST['group'] as $key => $val) {
			$db->qry('UPDATE %prefix%menu SET group_nr = %int% WHERE id = %int%', $val, $key);
		}
		if ($_POST['box']) foreach ($_POST['box'] as $key => $val) {
			$db->qry('UPDATE %prefix%menu SET boxid = %int% WHERE id = %int%', $val, $key);
		}
	break;

}


switch($_GET["step"]) {

	// Change Group Choice
	case 8:
		$dsp->NewContent($lang["install"]["menu_group_change"], $lang["install"]["menu_group_change2"]);
		$dsp->SetForm("index.php?mod=install&action=menu&step=9&pos={$_GET["pos"]}&onlyactive={$_GET["onlyactive"]}");

		$menu = $db->query_first("SELECT group_nr FROM {$config["tables"]["menu"]} WHERE pos = {$_GET["pos"]}");
		$dsp->AddTextFieldRow("group", "Gruppe", (int)$menu["group_nr"], "");

		$dsp->AddFormSubmitRow("next");
		$dsp->AddBackButton("index.php?mod=install&action=menu&onlyactive={$_GET["onlyactive"]}", "install/modules");
		$dsp->AddContent();
	break;


	default:
		$dsp->NewContent(t('Navigationsmenü verwalten'), '<font color="red">'. t('Hinweis: Verwenden Sie die MenüBox-Nr um neue Boxen zu bilden. Alle Einträge mit gleicher ID landen in der gleichen Box'). HTML_NEWLINE .t('Hinweis2: Verwenden sie die Gruppen um in der URL mit dem Parameter &menu_group=xx nur bestimmte Menü-Eintrage auszugeben. Das ist nützlich bei einer eigenen Hauptnavigation im eigenen Design') .'</font>');
		$dsp->SetForm("index.php?mod=install&action=menu&step=10&onlyactive={$_GET["onlyactive"]}");

		$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=menu&step=6&onlyactive={$_GET["onlyactive"]}\">{$lang["install"]["menu_navi_reset"]}</a>");
		if ($_GET["onlyactive"]) $dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=menu&onlyactive=0\">{$lang["install"]["menu_navi_showall"]}</a>");
		else  $dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=menu&onlyactive=1\">{$lang["install"]["menu_navi_showactive"]}</a>");

		$menus = $db->query("SELECT module.active, menu.* FROM {$config["tables"]["menu"]} AS menu
			LEFT JOIN {$config["tables"]["modules"]} AS module ON (menu.module = module.name)
			WHERE (menu.level = 0) and (menu.caption != '') ORDER BY menu.pos");
		$z = 0;
		while ($menu = $db->fetch_array($menus)){
			$z++;
			$db->query("UPDATE {$config["tables"]["menu"]} SET pos = $z WHERE id = {$menu["id"]}");

			if ($menu["active"] or (!$_GET["onlyactive"])) {
				$link = "";
				if ($menu["caption"] == "--hr--") {
					$menu["caption"] = "<i>Trennzeile</i>";
					$link .= "[<a href=\"index.php?mod=install&action=menu&step=5&pos=$z&onlyactive={$_GET["onlyactive"]}\">{$lang["install"]["del"]}</a>] ";
				} else {
					$link .= "[<a href=\"index.php?mod=install&action=modules&step=20&module={$menu["module"]}&onlyactive={$_GET["onlyactive"]}\">{$lang["install"]["edit"]}</a>] ";
					if ($z < $db->num_rows($menus)) $link .= "[<a href=\"index.php?mod=install&action=menu&step=4&pos=$z&onlyactive={$_GET["onlyactive"]}\">{$lang["install"]["hr"]}</a>] ";
				}
#				$link .= "[<a href=\"index.php?mod=install&action=menu&step=8&pos=$z&onlyactive={$_GET["onlyactive"]}\">{$lang["install"]["group"]} ({$menu["group_nr"]})</a>] ";
				if ($z > 1)  $link .= "[<a href=\"index.php?mod=install&action=menu&step=2&pos=$z&onlyactive={$_GET["onlyactive"]}\">^</a>] ";
				if ($z < $db->num_rows($menus)) $link .= "[<a href=\"index.php?mod=install&action=menu&step=3&pos=$z&onlyactive={$_GET["onlyactive"]}\">v</a>]";
				$link .= " {$lang["install"]["pos"]}: <input type=\"text\" name=\"pos[{$menu["id"]}]\" value=\"$z\" size=\"2\">";
				$link .= " Gruppe: <input type=\"text\" name=\"group[{$menu["id"]}]\" value=\"{$menu['group_nr']}\" size=\"2\">";
				$link .= " MenüBox-Nr: <input type=\"text\" name=\"box[{$menu["id"]}]\" value=\"{$menu['boxid']}\" size=\"2\">";

				$dsp->AddDoubleRow("$z) ". $menu["caption"], $link);
			}
		}
		$db->free_result($menus);

		$dsp->AddFormSubmitRow("next");
		$dsp->AddBackButton("index.php?mod=install", "install/modules");
		$dsp->AddContent();
	break;
}
?>
