<?php

function FindCfgKeyForMod($name) {
  global $db, $config;

	$find_config = $db->query_first("SELECT cfg_key FROM {$config["tables"]["config"]} WHERE (cfg_module = '$name')");
	if ($find_config["cfg_key"] != '') return true; else return false;
} 

function WriteMenuEntries() {
	global $templ, $res, $db, $config, $dsp, $MenuCallbacks;

	if ($db->num_rows($res) == 0) $dsp->AddDoubleRow("", "<i>- keine -</i>");
	else while ($row = $db->fetch_array($res)) {
		$templ['ls']['row']['menuitem']['action'] = $row["action"];
		$templ['ls']['row']['menuitem']['file'] = $row["file"];
		$templ['ls']['row']['menuitem']['id'] = $row["id"];
		$templ['ls']['row']['menuitem']['caption'] = $row["caption"];
		$templ['ls']['row']['menuitem']['hint'] = $row["hint"];
		$templ['ls']['row']['menuitem']['link'] = $row["link"];
		$templ['ls']['row']['menuitem']['link'] = $row["link"];
		$templ['ls']['row']['menuitem']['pos'] = $row["pos"];
    if ($row['level'] == 0) $templ['ls']['row']['menuitem']['boxid'] = 'Boxid: <input type="text" name="boxid['.$row['id'].']" value="'. $row['boxid'] .'" size="2" />';
    else $templ['ls']['row']['menuitem']['boxid'] = '';

		$templ['ls']['row']['menuitem']['needed_config'] = "<option value=\"\">-".t('keine')."-</option>";

		$res2 = $db->query("SELECT cfg_key FROM {$config["tables"]["config"]} WHERE cfg_type = 'boolean' OR cfg_type = 'int' ORDER BY cfg_key");
		if ($MenuCallbacks) foreach ($MenuCallbacks as $MenuCallback) {
			($MenuCallback == $row["needed_config"])? $selected = " selected" : $selected = "";
			$templ['ls']['row']['menuitem']['needed_config'] .= "<option value=\"{$MenuCallback}\"$selected>{$MenuCallback}</option>";
		}
		$db->free_result($res2);


		$templ['ls']['row']['menuitem']['requirement'] = "";
		for ($i = 0; $i <= 5; $i++) {
			($i == $row["requirement"])? $selected = " selected" : $selected = "";
			switch ($i) {
				default: $out = t('Jeder'); break;
				case 1: $out = t('Nur Eingeloggte'); break;
				case 2: $out = t('Nur Admins'); break;
				case 3: $out = t('Nur Superadminen'); break;
				case 4: $out = t('Keine Admins'); break;
				case 5: $out = t('Nur Ausgeloggte'); break;
			}
			$templ['ls']['row']['menuitem']['requirement'] .= "<option value=\"$i\"$selected>$out</option>";
		}

		$dsp->AddModTpl("install", "menuitem");
		$dsp->AddHRuleRow();
	}
	$db->free_result($res);
}



switch($_GET["step"]) {
	// Update Modules
	case 2:
		$res = $db->query("SELECT name FROM {$config["tables"]["modules"]} WHERE changeable");
		while ($row = $db->fetch_array($res)){
			if ($_POST[$row["name"]]) $db->query_first("UPDATE {$config["tables"]["modules"]} SET active = 1 WHERE name = '{$row["name"]}'");
			elseif (count($_POST)) $db->query_first("UPDATE {$config["tables"]["modules"]} SET active = 0 WHERE name = '{$row["name"]}'");
		}
		$db->free_result($res);

		$db->query_first("UPDATE {$config["tables"]["modules"]} SET active = 1 WHERE name = 'settings'");
		$db->query_first("UPDATE {$config["tables"]["modules"]} SET active = 1 WHERE name = 'banner'");
		$db->query_first("UPDATE {$config["tables"]["modules"]} SET active = 1 WHERE name = 'about'");

		$func->confirmation(t('Änderungen erfolgreich gespeichert.'), "index.php?mod=install&action=modules");
	break;

	// Question: Reset all Modules
	case 3:
		$func->question(t('Sollen wirklich <b>\'alle Module\'</b> zurückgesetzt werden?HTML_NEWLINEDies wirkt sich <u>nicht</u> auf die Datenbankeinträge der Module aus, jedoch gehen alle Einstellungen und Menüänderungen verloren, die zu den Modulen getätigt worden sind. Außerdem sind danach nur noch die Standardmodule aktiviert.'), "index.php?mod=install&action=modules&rewrite=all", "index.php?mod=install&action=modules");
	break;

	// Question: Reset this Module
	case 4:
		$func->question(t('Soll das Modul <b>\'%1\'</b> wirklich zurückgesetzt werden?<br />Dies wirkt sich <u>nicht</u> auf die Datenbankeinträge des Modules aus, jedoch gehen alle Einstellungen und Menüänderungen verloren, die zu diesem Modul getätigt worden sind.', $_GET["module"]), "index.php?mod=install&action=modules&rewrite={$_GET["module"]}", "index.php?mod=install&action=modules");
	break;

	// Add Menuentry
	case 22:
		$db->query("INSERT INTO {$config["tables"]["menu"]}
				SET caption = 'Neuer Eintrag',
					requirement = '0',
					hint = '',
					link = 'index.php?mod=',
					needed_config = '',
					module='{$_GET["module"]}',
					level = 1");

	// Menuentries
	case 20:
		$db->query("DELETE FROM {$config["tables"]["menu"]} WHERE caption='' AND action='' AND file=''");

		$dsp->NewContent(t('Modul-Menüeinträge'), t('Hier können Sie die Navigationseinträge dieses Moduls ändern.'));
		$dsp->SetForm("index.php?mod=install&action=modules&step=21&module={$_GET["module"]}");

 		$dsp->AddFieldsetStart(t('Hauptmenüpunkt des Moduls / Modul-Startseite'));
		$res = $db->query("SELECT * FROM {$config["tables"]["menu"]} WHERE module='{$_GET["module"]}' AND level = 0 AND caption != '' ORDER BY requirement, pos");
		WriteMenuEntries();
 		$dsp->AddFieldsetEnd();

 		$dsp->AddFieldsetStart(t('Untermenüpunkte'));
		$res = $db->query("SELECT * FROM {$config["tables"]["menu"]} WHERE module='{$_GET["module"]}' AND level > 0 AND caption != '' ORDER BY requirement, pos");
		WriteMenuEntries();
 		$dsp->AddFieldsetEnd();

 		$dsp->AddFieldsetStart(t('Keine Menüpunkte - Interne Verweise'));
		$res = $db->query("SELECT * FROM {$config["tables"]["menu"]} WHERE module='{$_GET["module"]}' AND caption = '' ORDER BY requirement, pos");
		WriteMenuEntries();
 		$dsp->AddFieldsetEnd();

		$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=modules&module={$_GET["module"]}&step=22\">".t('Neuen Menüeintrag hinzufügen')."</a>");

		$dsp->AddFormSubmitRow("next");
		$dsp->AddContent();
	break;

	// Change Menuentries
	case 21:
		foreach ($_POST["caption"] as $key => $val) {
			$db->query("UPDATE {$config["tables"]["menu"]}
					SET caption = '{$_POST["caption"][$key]}',
					requirement = '{$_POST["requirement"][$key]}',
					action = '{$_POST["action"][$key]}',
					hint = '{$_POST["hint"][$key]}',
					link = '{$_POST["link"][$key]}',
					file = '{$_POST["file"][$key]}',
					pos = '{$_POST["pos"][$key]}',
					boxid = '{$_POST["boxid"][$key]}',
					needed_config = '{$_POST["needed_config"][$key]}'
					WHERE id = '$key'");
		}

		$func->confirmation(t('Änderungen erfolgreich gespeichert.'), "index.php?mod=install&action=modules&step=20&module={$_GET["module"]}");
	break;

	// Delete Menuentry
	case 23:
	  $row = $db->query_first("SELECT requirement FROM {$config["tables"]["menu"]} WHERE id='{$_GET["id"]}'");
	  if ($row['requirement'] > 0) $func->information(t('Mit diesem Eintrag ist eine Zugriffsberechtigung verknüpft. Sie sollten diesen Eintrag daher nicht löschen, da sonst jeder Zugriff auf die betreffende Datei hat.HTML_NEWLINEWenn Sie nur den Menülink entfernen möchten, löschen Sie die Felder Titel und Linkziel.HTML_NEWLINEWenn Sie wirklich jedem Zugriff auf die Datei geben möchten, setzen Sie den Zugriff auf Jeder und löschen Sie dann den Eintrag.'), "index.php?mod=install&action=modules&step=20&module={$_GET["module"]}");
	  
	  else {
  		$db->query("DELETE FROM {$config["tables"]["menu"]} WHERE id='{$_GET["id"]}'");
  		$func->confirmation(t('Der Menü-Eintrag wurde erfolgreich gelöscht'), "index.php?mod=install&action=modules&step=20&module={$_GET["module"]}");
  	}
	break;


	// Show Modulelist
	default:
		// If Rewrite, delete corresponding items
		$rewrite_all = 0;
		if ($_GET["rewrite"] == "all") {
			$db->query("TRUNCATE TABLE {$config["tables"]["config"]}");
			$db->query("TRUNCATE TABLE {$config["tables"]["modules"]}");
			$db->query("TRUNCATE TABLE {$config["tables"]["menu"]}");
			$rewrite_all = 1;
		} elseif ($_GET["rewrite"]) {
			$db->query("DELETE FROM {$config["tables"]["modules"]} WHERE name = '{$_GET["rewrite"]}'");
			$db->query("DELETE FROM {$config["tables"]["menu"]} WHERE module = '{$_GET["rewrite"]}'");

			$_GET["rewrite"] .= "_";
			if ($_GET["rewrite"] == "downloads_") $_GET["rewrite"] = "Download";
			if ($_GET["rewrite"] == "usrmgr_") $_GET["rewrite"] = "Userdetails";
			if ($_GET["rewrite"] == "tournament2_") $_GET["rewrite"] = "t";
			$find_config = $db->query_first("DELETE FROM {$config["tables"]["config"]}
					WHERE (cfg_group = '{$_GET["rewrite"]}') OR (cfg_key LIKE '{$_GET["rewrite"]}%')
					");
		}

		// Auto-Load Modules from XML-Files
		$install->InsertModules(0);
		$install->InsertMenus($rewrite_all);

		// Output Module-List
		$dsp->NewContent(t('Modulverwaltung'), t('Hier können Sie Module de-/aktivieren, sowie deren Einstellungen verändern.'));

		$dsp->AddDoubleRow("", "<a href=\"index.php?mod=install&action=modules&step=3\">".t('Alle Module zurücksetzen')."</a>");

		$dsp->AddHRuleRow();
		$dsp->SetForm("index.php?mod=install&action=modules&step=2");

		$res = $db->query("SELECT * FROM {$config["tables"]["modules"]} ORDER BY changeable DESC, caption");
		while ($row = $db->fetch_array($res)){

			$templ['ls']['row']['module']['name'] = $row["name"];
			$templ['ls']['row']['module']['caption'] = t($row["caption"]);
			$templ['ls']['row']['module']['description'] = t($row["description"]);

			if ($row["email"]) $templ['ls']['row']['module']['author'] = "<a href=\"mailto:{$row["email"]}\">{$row["author"]}</a>";
			else $templ['ls']['row']['module']['author'] = $row["author"];

			if ($row["active"]) $templ['ls']['row']['module']['active'] = " checked";
			else $templ['ls']['row']['module']['active'] = " ";

			if ($row["changeable"]) $templ['ls']['row']['module']['readonly'] = "";
			else $templ['ls']['row']['module']['readonly'] = " disabled";

			$templ['ls']['row']['module']['version'] = $row["version"];
			
			($row["state"] == "Stable")? $templ['ls']['row']['module']['state'] = $row["state"]
			: $templ['ls']['row']['module']['state'] = "<font color=\"red\">{$row["state"]}</font>";

			(file_exists("modules/{$row["name"]}/icon.gif"))? $templ['ls']['row']['module']['img'] = "modules/{$row["name"]}/icon.gif"
			: $templ['ls']['row']['module']['img'] = "modules/sample/icon.gif";

			if (FindCfgKeyForMod($row["name"])) $templ['ls']['row']['module']['settings_link'] = " | <a href=\"index.php?mod=install&action=mod_cfg&step=10&module={$row["name"]}\">". t('Konfig.') ."</a>";
			else $templ['ls']['row']['module']['settings_link'] = "";

			$find_mod = $db->query_first("SELECT module
					FROM {$config["tables"]["menu"]}
					WHERE module='{$row["name"]}'
					");
			if ($find_mod["module"]) $templ['ls']['row']['module']['menu_link'] = " | <a href=\"index.php?mod=install&action=mod_cfg&step=30&module={$row["name"]}\">". t('Menü') ."</a>";
			else $templ['ls']['row']['module']['menu_link'] = "";

			if (file_exists("modules/{$row["name"]}/mod_settings/db.xml")) $templ['ls']['row']['module']['db_link'] = " | <a href=\"index.php?mod=install&action=mod_cfg&step=40&module={$row["name"]}\">". t('DB') ."</a>";
			else $templ['ls']['row']['module']['db_link'] = "";

			if (file_exists("modules/{$row["name"]}/docu/{$language}_help.php")) {
        $templ['ls']['row']['helpletbutton']['helplet_id'] = $helplet_id;
        $templ['ls']['row']['helpletbutton']['help'] = 
        $templ['ls']['row']['module']['help_link'] = " | <a href=\"#\" onclick=\"javascript:var w=window.open('index.php?mod=helplet&action=helplet&design=base&module={$row["name"]}&helpletid=help','_blank','width=700,height=500,resizable=no,scrollbars=yes');\" class=\"Help\">?</a>";
      } else $templ['ls']['row']['module']['help_link'] = '';

			$dsp->AddModTpl("install", "module");
		}
		$db->free_result($res);

		$dsp->AddFormSubmitRow("next");
		$dsp->AddBackButton("index.php?mod=install", "install/modules");
		$dsp->AddContent();
	break;
} // Switch Action
?>
