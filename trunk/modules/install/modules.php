<?php
function WriteMenuEntries() {
	global $templ, $res, $db, $config, $dsp, $lang;

	if ($db->num_rows($res) == 0) $dsp->AddDoubleRow("", "<i>- keine -</i>");
	else while ($row = $db->fetch_array($res)) {
		$templ['ls']['row']['menuitem']['action'] = $row["action"];
		$templ['ls']['row']['menuitem']['file'] = $row["file"];
		$templ['ls']['row']['menuitem']['id'] = $row["id"];
		$templ['ls']['row']['menuitem']['caption'] = $row["caption"];
		$templ['ls']['row']['menuitem']['hint'] = $row["hint"];
		$templ['ls']['row']['menuitem']['link'] = $row["link"];
		$templ['ls']['row']['menuitem']['link'] = $row["link"];

		$templ['ls']['row']['menuitem']['needed_config'] = "<option value=\"\">-{$lang["install"]["none"]}-</option>";
		$res2 = $db->query("SELECT cfg_key FROM {$config["tables"]["config"]} WHERE cfg_type = 'boolean' ORDER BY cfg_key");
		while ($cfg_row = $db->fetch_array($res2)){
			($cfg_row["cfg_key"] == $row["needed_config"])? $selected = " selected" : $selected = "";
			$templ['ls']['row']['menuitem']['needed_config'] .= "<option value=\"{$cfg_row["cfg_key"]}\"$selected>{$cfg_row["cfg_key"]}</option>";
		}
		$db->free_result($res2);

		$templ['ls']['row']['menuitem']['requirement'] = "";
		for ($i = 0; $i <= 5; $i++) {
			($i == $row["requirement"])? $selected = " selected" : $selected = "";
			switch ($i) {
				default: $out = $lang["install"]["everyone"]; break;
				case 1: $out = $lang["install"]["only_login"]; break;
				case 2: $out = $lang["install"]["only_admin"]; break;
				case 3: $out = $lang["install"]["only_op"]; break;
				case 4: $out = $lang["install"]["no_admin"]; break;
				case 5: $out = $lang["install"]["only_logout"]; break;
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
			else $db->query_first("UPDATE {$config["tables"]["modules"]} SET active = 0 WHERE name = '{$row["name"]}'");
		}
		$db->free_result($res);

		$db->query_first("UPDATE {$config["tables"]["modules"]} SET active = 1 WHERE name = 'settings'");
		$db->query_first("UPDATE {$config["tables"]["modules"]} SET active = 1 WHERE name = 'banner'");
		$db->query_first("UPDATE {$config["tables"]["modules"]} SET active = 1 WHERE name = 'about'");

		$func->confirmation("Die Änderungen wurden übernommen.", "install.php?mod=install&action=modules");
	break;

	// Question: Reset all Modules
	case 3:
		$func->question($lang["install"]["mod_reset_quest"], "install.php?mod=install&action=modules&rewrite=all", "install.php?mod=install&action=modules");
	break;

	// Question: Reset this Module
	case 4:
		$func->question(str_replace("%NAME%", $_GET["module"], $lang["install"]["mod_reset_mod_quest"]), "install.php?mod=install&action=modules&rewrite={$_GET["module"]}", "install.php?mod=install&action=modules");
	break;

	// Settings
	case 10:
		$_GET["module"] .= "_";
		if ($_GET["module"] == "downloads_") $_GET["module"] = "Download";
		if ($_GET["module"] == "usrmgr_") $_GET["module"] = "Userdetails";
		if ($_GET["module"] == "tournament2_") $_GET["module"] = "Turnier";

		$like = ereg_replace('_','\_', $_GET["module"]);		//Erestze alle _ durch \_ , da mysql _ für ein beliebiges Zeichen interpretiert.

		$res = $db->query("SELECT cfg_key, cfg_value, cfg_desc, cfg_type
				FROM {$config["tables"]["config"]}
				WHERE (cfg_group = '{$_GET["module"]}')
				OR (cfg_key LIKE '{$like}%')");

		if ($db->num_rows($res) == 0) $func->error($lang["install"]["mod_set_err_nosettings"], "install.php?mod=install&action=modules");
		else {
			$dsp->NewContent($lang["install"]["mod_set_caption"], $lang["install"]["mod_set_subcaption"]);
			$dsp->SetForm("install.php?mod=install&action=modules&step=11&module={$_GET["module"]}");

			while ($row = $db->fetch_array($res)){

				$row["cfg_desc"] = $func->translate($row["cfg_desc"]);
				$row["cfg_value"] = $func->translate($row["cfg_value"]);

				// Get Selections
				$get_cfg_selection = $db->query("SELECT cfg_display, cfg_value
					FROM {$config["tables"]["config_selections"]}
					WHERE cfg_key = '{$row["cfg_type"]}'
					");
				if ($db->num_rows($get_cfg_selection) > 0){
					$t_array = array();
					while ($selection = $db->fetch_array($get_cfg_selection)){
						($row["cfg_value"] == $selection["cfg_value"]) ? $selected = "selected" : $selected = "";
						array_push ($t_array, "<option $selected value=\"{$selection["cfg_value"]}\">". $func->translate($selection["cfg_display"]) ."</option>");
					}
					$dsp->AddDropDownFieldRow($row["cfg_key"], $row["cfg_desc"], $t_array, "", 1);

				// Show Edit-Fields for Settings
				} else switch ($row["cfg_type"]){
					case "password":
						$dsp->AddPasswordRow($row["cfg_key"], $row["cfg_desc"], $row["cfg_value"], "", "", 1);
					break;

					case "datetime":
						$dsp->AddDateTimeRow($row["cfg_key"], $row["cfg_desc"], $row["cfg_value"], "", "");
					break;

					case "date":
						$dsp->AddDateTimeRow($row["cfg_key"], $row["cfg_desc"], $row["cfg_value"], "", "", "", "", "", 1);
					break;

					case "time":
						$dsp->AddDateTimeRow($row["cfg_key"], $row["cfg_desc"], $row["cfg_value"], "", "", "", "", "", 2);
					break;

					default:
						$row["cfg_value"] = str_replace("<", "&lt;", $row["cfg_value"]);
						$row["cfg_value"] = str_replace(">", "&gt;", $row["cfg_value"]);
						$row["cfg_value"] = str_replace("\"", "'", $row["cfg_value"]);
						$dsp->AddTextFieldRow($row["cfg_key"], $row["cfg_desc"], $row["cfg_value"], "");
					break;
				}
			}
			$db->free_result($res);

			$dsp->AddFormSubmitRow("next");
			if ($_GET["module"] == "sys_") $dsp->AddBackButton("install.php?mod=install", "install/modules"); 
			elseif ($script_filename == "install.php")  $dsp->AddBackButton("install.php?mod=install&action=modules", "install/modules"); 
			$dsp->AddContent();
		}
	break;

	// Change Settings
	case 11:
		foreach ($_POST as $key => $val) {

			// Date + Time Values
			if (strpos($key, "_value_") > 0) {
				if (strpos($key, "_value_minutes") > 0) {
					$key = substr($key, 0, strpos($key, "_value_minutes"));

					$cfg_value = mktime($_POST[$key."_value_hours"], $_POST[$key."_value_minutes"], $_POST[$key."_value_seconds"], $_POST[$key."_value_month"], $_POST[$key."_value_day"], $_POST[$key."_value_year"]);

					$db->query("UPDATE {$config['tables']['config']} SET cfg_value = '$cfg_value' WHERE cfg_key = '$key'");
				}

			// Other Values
			} else $db->query("UPDATE {$config['tables']['config']} SET cfg_value = '$val' WHERE cfg_key = '$key'");
		}

		$func->confirmation($lang["install"]["modules_settings_success"], "install.php?mod=install&action=modules&step=10&module={$_GET["module"]}");
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

		$dsp->NewContent($lang["install"]["mod_menu_caption"], $lang["install"]["mod_menu_subcaption"]);
		$dsp->SetForm("install.php?mod=install&action=modules&step=21&module={$_GET["module"]}");

		$dsp->AddSingleRow("<b>{$lang["install"]["modules_menu_start"]}</b>");
		$res = $db->query("SELECT * FROM {$config["tables"]["menu"]} WHERE module='{$_GET["module"]}' AND level = 0 AND caption != '' ORDER BY pos");
		WriteMenuEntries();

		$dsp->AddSingleRow("<b>{$lang["install"]["modules_menu_sub"]}</b>");
		$res = $db->query("SELECT * FROM {$config["tables"]["menu"]} WHERE module='{$_GET["module"]}' AND level > 0 AND caption != '' ORDER BY pos");
		WriteMenuEntries();

		$dsp->AddSingleRow("<b>{$lang["install"]["modules_menu_internal"]}</b>");
		$res = $db->query("SELECT * FROM {$config["tables"]["menu"]} WHERE module='{$_GET["module"]}' AND caption = '' ORDER BY pos");
		WriteMenuEntries();

		$dsp->AddDoubleRow("", "<a href=\"install.php?mod=install&action=modules&module={$_GET["module"]}&step=22\">{$lang["install"]["modules_menu_new"]}</a>");

		$dsp->AddFormSubmitRow("next");
		if ($script_filename == "install.php") $dsp->AddBackButton("install.php?mod=install&action=modules", "install/modules"); 
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
					needed_config = '{$_POST["needed_config"][$key]}'
					WHERE id = '$key'");
		}

		$func->confirmation($lang["install"]["modules_settings_success"], "install.php?mod=install&action=modules&step=20&module={$_GET["module"]}");
	break;

	// Delete Menuentry
	case 23:
		$db->query("DELETE FROM {$config["tables"]["menu"]} WHERE id='{$_GET["id"]}'");
		$func->confirmation($lang["install"]["modules_del_success"], "install.php?mod=install&action=modules&step=20&module={$_GET["module"]}");
	break;

	// Show Module-DB
	case 30:
		if (!is_dir("modules/{$_GET["module"]}/mod_settings")) $func->error("Modul '{$_GET["module"]}' wurde nicht gefunden", "");
		else {
			$dsp->NewContent($lang["install"]["modules_db_caption"] .": ". $_GET["module"], $lang["install"]["modules_db_subcaption"]);

			$dsp->AddSingleRow("<b>{$lang["install"]["modules_db_belong"]}</b>");
			if (is_dir("modules/{$_GET["module"]}/mod_settings")) {
				$file = "modules/{$_GET["module"]}/mod_settings/db.xml";
				if (file_exists($file)) {
					$xml_file = fopen($file, "r");
					$xml_content = fread($xml_file, filesize($file));
					fclose($xml_file);

					$lansuite = $xml->get_tag_content("lansuite", $xml_content);
					$tables = $xml->get_tag_content_array("table", $lansuite);
					foreach ($tables as $table) {
						$table_head = $xml->get_tag_content("table_head", $table);
						$table_name = $xml->get_tag_content("name", $table_head);
						$dsp->AddDoubleRow("", $config["database"]["prefix"] . $table_name);
					}
				}
			}

			$dsp->AddHRuleRow();

			$dsp->AddSingleRow("<b>{$lang["install"]["modules_actions"]}</b>");
			$dsp->AddDoubleRow("", "<a href=\"install.php?mod=install&action=modules&step=31&module={$_GET["module"]}\">{$lang["install"]["modules_reset_moddb"]}</a>");
			$dsp->AddHRuleRow();

			$dsp->AddSingleRow("<b>{$lang["install"]["modules_export_moddb"]}</b>");
			$dsp->SetForm("base.php?mod=modules&step=33&module={$_GET["module"]}", "", "", "");
			$dsp->AddCheckBoxRow("e_struct", $lang["install"]["export_structure"], "", "", 1, 1);
			$dsp->AddCheckBoxRow("e_cont", $lang["install"]["export_content"], "", "", 1, 1);
			$dsp->AddFormSubmitRow("download");

			if ($script_filename == "install.php") $dsp->AddBackButton("install.php?mod=install&action=modules", "install/modules"); 
			$dsp->AddContent();
		}
	break;

	// Rewrite specific Module-DB - Question
	case 31:
		$func->question(str_replace("%NAME%", $_GET["module"], $lang["install"]["db_rewrite_quest"]), "install.php?mod=install&action=modules&step=32&module={$_GET["module"]}", "install.php?mod=install&action=modules&step=30&module={$_GET["module"]}");
	break;

	// Rewrite specific Module-DB
	case 32:
		$install->WriteTableFromXMLFile($_GET["module"], 1);
		$func->confirmation($lang["install"]["modules_rewritedb_success"], "install.php?mod=install&action=modules&step=30&module={$_GET["module"]}");
	break;

	// Export Module-DB
	case 33:
		include_once("modules/install/class_export.php");
		$export = New Export();

		if ($_GET["module"]) {
			$export->LSTableHead("lansuite_". $_GET["module"] ."_". date("ymd") .".xml");
			$export->ExportMod($_GET["module"], $_POST["e_struct"], $_POST["e_cont"]);
			$export->LSTableFoot();
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
		$dsp->NewContent($lang["install"]["mod_caption"], $lang["install"]["mod_subcaption"]);

		$dsp->AddDoubleRow("", "<a href=\"install.php?mod=install&action=modules&step=3\">{$lang["install"]["modules_reset_modules"]}</a>");

		$dsp->AddHRuleRow();
		$dsp->SetForm("install.php?mod=install&action=modules&step=2");

		$res = $db->query("SELECT * FROM {$config["tables"]["modules"]} ORDER BY changeable DESC, caption");
		while ($row = $db->fetch_array($res)){

			$templ['ls']['row']['module']['name'] = $row["name"];
			$templ['ls']['row']['module']['caption'] = $func->translate($row["caption"]);
			$templ['ls']['row']['module']['description'] = $func->translate($row["description"]);

			if ($row["email"]) $templ['ls']['row']['module']['author'] = "<a href=\"mailto:{$row["email"]}\">{$row["author"]}</a>";
			else $templ['ls']['row']['module']['author'] = $row["author"];

			if ($row["active"]) $templ['ls']['row']['module']['active'] = " checked";
			else $templ['ls']['row']['module']['active'] = " ";

			if ($row["changeable"]) $templ['ls']['row']['module']['readonly'] = "";
			else $templ['ls']['row']['module']['readonly'] = " disabled";

			$templ['ls']['row']['module']['version'] = $row["version"];
			
			($row["state"] == "Stable")? $templ['ls']['row']['module']['state'] = $row["state"]
			: $templ['ls']['row']['module']['state'] = "<font color=\"red\">{$row["state"]}</font>";

			(file_exists("modules/{$row["name"]}/images/admin_icon.gif"))? $templ['ls']['row']['module']['img'] = "modules/{$row["name"]}/images/admin_icon.gif"
			: $templ['ls']['row']['module']['img'] = "modules/sample/images/admin_icon.gif";

			$cfg_grp = $row["name"] . "_";
			if ($cfg_grp == "downloads_") $cfg_grp = "Download";
			if ($cfg_grp == "usrmgr_") $cfg_grp = "Userdetails";
			if ($cfg_grp == "tournament2_") $cfg_grp = "t";
			$find_config = $db->query_first("SELECT cfg_key
					FROM {$config["tables"]["config"]}
					WHERE (cfg_group = '$cfg_grp')
					OR (cfg_key LIKE '$cfg_grp%')
					");
			if ($find_config["cfg_key"]) $templ['ls']['row']['module']['settings_link'] = " | <a href=\"install.php?mod=install&action=modules&step=10&module={$row["name"]}\">{$lang["install"]["modules_config"]}</a>";
			else $templ['ls']['row']['module']['settings_link'] = "";

			$find_mod = $db->query_first("SELECT module
					FROM {$config["tables"]["menu"]}
					WHERE module='{$row["name"]}'
					");
			if ($find_mod["module"]) $templ['ls']['row']['module']['menu_link'] = " | <a href=\"install.php?mod=install&action=modules&step=20&module={$row["name"]}\">{$lang["install"]["modules_menu"]}</a>";
			else $templ['ls']['row']['module']['menu_link'] = "";

			if (file_exists("modules/{$row["name"]}/mod_settings/db.xml")) $templ['ls']['row']['module']['db_link'] = " | <a href=\"install.php?mod=install&action=modules&step=30&module={$row["name"]}\">{$lang["install"]["modules_db"]}</a>";
			else $templ['ls']['row']['module']['db_link'] = "";

			$dsp->AddModTpl("install", "module");
		}
		$db->free_result($res);

		$dsp->AddFormSubmitRow("next");
		$dsp->AddBackButton("install.php?mod=install", "install/modules"); 
		$dsp->AddContent();
	break;
} // Switch Action
?>
