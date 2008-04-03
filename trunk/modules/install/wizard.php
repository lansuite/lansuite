<?php
include_once("modules/install/class_import.php");
$import = New Import();
$_SESSION['auth']['design'] = 'standard';

switch ($_GET["step"]){
	// Check Environment
	default:
		$dsp->NewContent($lang["install"]["wizard_caption"], $lang["install"]["wizard_subcaption"]);

		$dsp->SetForm("index.php?mod=install&action=wizard");
		$lang_array = array();
		if ($language == "de") $selected = 'selected'; else $selected = '';
		array_push ($lang_array, "<option $selected value=\"de\">Deutsch</option>");
		if ($language == "en") $selected = 'selected'; else $selected = '';
		array_push ($lang_array, "<option $selected value=\"en\">English</option>");
		$dsp->AddDropDownFieldRow("language", $lang["sys"]["language"], $lang_array, "");
		$dsp->AddFormSubmitRow("change");

		$continue = $install->envcheck();

		if ($continue) $dsp->AddDoubleRow("", $dsp->FetchButton("index.php?mod=install&action=wizard&step=2", "next"));
		$dsp->AddContent();
	break;


	// Setting up ls_conf
	case 2:
		$dsp->NewContent($lang["install"]["conf_caption"], $lang["install"]["conf_subcaption"]);
		$dsp->SetForm("index.php?mod=install&action=wizard&step=3");

		// Set default settings from Config-File
		if ($_POST["host"] == "") $_POST["host"] = $config['database']['server'];
		if ($_POST["user"] == "") $_POST["user"] = $config['database']['user'];
		if ($_POST["database"] == "") $_POST["database"] = $config['database']['database'];
		if ($_POST["prefix"] == "") $_POST["prefix"] = $config['database']['prefix'];

		#### Database Access
		$dsp->AddSingleRow("<b>". $lang["install"]["conf_dbdata"] ."</b>");
		$dsp->AddTextFieldRow("host", $lang["install"]["conf_host"], $_POST["host"], "");
		$dsp->AddTextFieldRow("user", $lang["install"]["conf_user"], $_POST["user"], "");
		$dsp->AddPasswordRow("pass", $lang["install"]["conf_pass"], $_POST["pass"], "");
		$dsp->AddTextFieldRow("database", $lang["install"]["conf_db"], $_POST["database"], "");
		$dsp->AddTextFieldRow("prefix", $lang["install"]["conf_prefix"], $_POST["prefix"], "");

		#### Default Design
		// Open the design-dir
		$design_dir = opendir("design/");

		// Check all Subdirs of $design_dir fpr valid design-xml-files
		$t_array = array();
		while ($akt_design = readdir($design_dir)) if ($akt_design != "." AND $akt_design != ".." AND $akt_design != ".svn" AND $akt_design != "templates") {

			$file = "design/$akt_design/design.xml";
			if (file_exists($file)) {

				// Read Names from design.xml
				$xml_file = fopen($file, "r");
				$xml_content = fread($xml_file, filesize($file));
				if ($xml_content != "") {
					($config['lansuite']['default_design'] == $akt_design) ? $selected = "selected" : $selected = "";
					array_push ($t_array, "<option $selected value=\"$akt_design\">". $xml->get_tag_content("name", $xml_content) ."</option>");
				}
				fclose($xml_file);
			}
		}
		$dsp->AddDropDownFieldRow("design", $lang["install"]["conf_design"], $t_array, "");

		$dsp->AddCheckBoxRow("resetdb", $lang["install"]["wizard_overwrite"], $lang["install"]["wizard_overwrite2"], "", 0, "");
		$dsp->AddSingleRow($lang["install"]["wizard_loadwarning"]);

		$dsp->AddFormSubmitRow("next");
		$dsp->AddBackButton("index.php?mod=install&action=wizard&step=1", "install/ls_conf");
		$dsp->AddContent();
	break;


	// Writing ls_conf & try to create DB-Strukture
	case 3:
		$continue = 1;

		$fail_leadin = "<font color=\"#ff0000\">";
		$leadout = "</font>";
		$output = "";

		// Set new $config-Vars
		if ($_POST["host"]) $config["database"]["server"] = $_POST["host"];
		if ($_POST["user"]) $config["database"]["user"] = $_POST["user"];
		if ($_POST["pass"]) $config["database"]["passwd"] = $_POST["pass"];
		if ($_POST["database"]) $config["database"]["database"] = $_POST["database"];
		if ($_POST["prefix"]) $config["database"]["prefix"] = $_POST["prefix"];
		if ($_POST["design"]) $config["lansuite"]["default_design"] = $_POST["design"];

		// Write new $config-Vars to config.php-File
		if (!$install->WriteConfig()) {
			$continue = 0;
			$output .= $fail_leadin . $lang["install"]["conf_err_write"] . $leadout . HTML_NEWLINE . HTML_NEWLINE;
		} else {
			$output .= $lang["install"]["conf_success"] .HTML_NEWLINE . HTML_NEWLINE;

			$res = $install->TryCreateDB($_POST["resetdb"]);
			switch ($res){
				case 0: $output .= $fail_leadin . $lang["install"]["wizard_db_notavailable"] . $leadout; break;
				case 1: $output .= str_replace("%DB%", $config["database"]["database"], $lang["install"]["wizard_db_exist"]); break;
				case 2: $output .= $fail_leadin . $lang["install"]["wizard_db_createfailed"] . $leadout; break;
				case 3: $output .= $lang["install"]["wizard_db_createsuccess"]; break;
			}
			$output .= HTML_NEWLINE . HTML_NEWLINE;

			if (($res == 1) || ($res == 3)){
				$db->connect();

				// Check for Updates
#				if($res == 1){
#					$install->check_updates();
#				}
				// Scan the modules-dir for mod_settings/db.xml-File, read data, compare with db and create/update DB, if neccessary
				$install->CreateNewTables(0);
				// Read table-names from DB an save them in $config['tables']
				$install->SetTableNames();

				// Insert PLZs from modules/install/db_insert_locations.sql in DB, if not exist
				$install->InsertPLZs();
				// Insert modules-settings from mod_settings/module.xml in DB, if not exist
				$install->InsertModules(1);
				// Insert menus from mod_settings/menu.xml in DB, if not exist
				$install->InsertMenus(0);
				// Insert translations of DB-items
#				$install->InsertTranslations();
			}
		}

		$dsp->NewContent($lang["install"]["wizzard_db_caption"], $lang["install"]["wizzard_db_subcaption"]);
		$dsp->AddSingleRow($output);

		if ($continue) $dsp->AddDoubleRow("", $dsp->FetchButton("index.php?mod=install&action=wizard&step=4", "next"));
		$dsp->AddBackButton("index.php?mod=install&action=wizard&step=2", "install/db");
		$dsp->AddContent();
	break;


	// Display import form
	case 4:

		$dsp->NewContent($lang["install"]["wizard_import_caption"], $lang["install"]["wizard_import_subcaption"]);

		$dsp->SetForm("index.php?mod=install&action=wizard&step=5", "", "", "multipart/form-data");

		$dsp->AddSingleRow("<b>{$lang["install"]["import_file"]}</b>");
		$dsp->AddFileSelectRow("importdata", $lang["install"]["import_import"], "");
		$dsp->AddHRuleRow();
		$dsp->AddSingleRow("<b>{$lang["install"]["import_settings_new"]}</b>");
		$dsp->AddCheckBoxRow("rewrite", $lang["install"]["import_settings_overwrite"], "", "", 1, 1);
		$dsp->AddHRuleRow();
		$dsp->AddSingleRow("<b>{$lang["install"]["import_settings_old"]}</b>");
		$dsp->AddTextFieldRow("comment", $lang["install"]["import_comment"], "", "", "", 1);
		$dsp->AddCheckBoxRow("deldb", $lang["install"]["import_deldb"], "", "", 1, 1);
		$dsp->AddCheckBoxRow("replace", $lang["install"]["import_replace"], "", "", 1, 1);
		$dsp->AddCheckBoxRow("signon", $lang["install"]["import_signon"], "", "", 1, 1);
		$dsp->AddHRuleRow();
		$dsp->AddSingleRow("<b>{$lang["install"]["import_settings_lansurfer"]}</b>");
		$dsp->AddCheckBoxRow("noseat", $lang["install"]["import_noseat"], "", "", 1, "");

		$dsp->AddSingleRow($lang["install"]["import_warning"]);
		$dsp->AddFormSubmitRow("add");

		$dsp->AddDoubleRow("", $dsp->FetchButton("index.php?mod=install&action=wizard&step=6", "next"));
		$dsp->AddBackButton("index.php?mod=install&action=wizard&step=3", "install/import");
		$dsp->AddContent();
	break;


	// Import uploaded file
	case 5:
		switch ($import->GetUploadFileType($_FILES['importdata']['name'])){
			case "xml":
				$header = $import->GetImportHeader($_FILES['importdata']['tmp_name']);
				$dsp->NewContent($lang["install"]["wizard_importupload_caption"], $lang["install"]["wizard_importupload_subcaption"]);

				switch ($header["filetype"]) {
					case "LANsurfer_export":
					case "lansuite_import":
						$import->ImportLanSurfer($_POST["deldb"], $_POST["replace"], $_POST["noseat"], $_POST["signon"], $_POST["comment"]);

						$dsp->AddSingleRow($lang["install"]["wizard_importupload_success"]);
						$dsp->AddDoubleRow($lang["install"]["wizard_importupload_filetype"], $header["filetype"]);
						$dsp->AddDoubleRow($lang["install"]["wizard_importupload_date"], $header["date"]);
						$dsp->AddDoubleRow($lang["install"]["wizard_importupload_source"], $header["source"]);
						$dsp->AddDoubleRow($lang["install"]["wizard_importupload_event"], $header["event"]);
						$dsp->AddDoubleRow($lang["install"]["wizard_importupload_version"], $header["version"]);
					break;

					case "LanSuite":
						$import->ImportXML($_POST["rewrite"]);
						$dsp->AddSingleRow("Import erfolgreich");
					break;

					default:
						$func->Information(str_replace("%FILETYPE%", $header["filetype"], $lang["install"]["import_err_filetype"]), "index.php?mod=install&action=wizard&step=4");
					break;
				}

				$dsp->AddDoubleRow("", $dsp->FetchButton("index.php?mod=install&action=wizard&step=6", "next"));
				$dsp->AddBackButton("index.php?mod=install&action=wizard&step=4", "install/import");
				$dsp->AddContent();
			break;

			case "csv":
				$check = $import->ImportCSV($_FILES['importdata']['tmp_name'], $_POST["deldb"], $_POST["replace"], $_POST["signon"], $_POST["comment"]);

				$dsp->NewContent($lang["install"]["wizard_importupload_caption"], $lang["install"]["wizard_importupload_subcaption"]);
				$dsp->AddSingleRow(str_replace("%ERROR%", $check["error"], str_replace("%NOTHING%", $check["nothing"], str_replace("%INSERT%", $check["insert"], str_replace("%REPLACE%", $check["replace"], $lang["install"]["import_csv_report"])))));

				$dsp->AddDoubleRow("", $dsp->FetchButton("index.php?mod=install&action=wizard&step=6", "next"));
				$dsp->AddBackButton("index.php?mod=install&action=wizard&step=4", "install/import");
				$dsp->AddContent();
			break;

			default:
				$func->information($lang["install"]["wizard_importupload_unsuportetfiletype"], "index.php?mod=install&action=wizard&step=4");
			break;
		}
	break;


	// Display form to create Adminaccount
	case 6:
		$dsp->NewContent($lang["install"]["wizard_admin_caption"], $lang["install"]["wizard_admin_subcaption"]);
		$dsp->SetForm("index.php?mod=install&action=wizard&step=7");
		// FIX language
        if (func::admin_exists()) $dsp->AddDoubleRow("Info", "Es existiert bereits ein Adminaccount");
        
		$dsp->AddTextFieldRow("email", $lang["install"]["admin_email"], 'admin@admin.de', '');
		$dsp->AddPasswordRow("password", $lang["install"]["conf_pass"], '', '', '', '', "onkeyup=\"CheckPasswordSecurity(this.value, document.images.seclevel1)\"");
		$dsp->AddPasswordRow("password2", $lang["install"]["admin_pass2"], '', '');
        $templ['pw_security']['id'] = 1;
        $dsp->AddDoubleRow('', $dsp->FetchTpl('design/templates/ls_row_pw_security.htm'));
		$dsp->AddFormSubmitRow("add");

		$dsp->AddDoubleRow("", $dsp->FetchButton("index.php?mod=install&action=wizard&step=8", "next"));
		$dsp->AddBackButton("index.php?mod=install&action=wizard&step=4", "install/admin");
		$dsp->AddContent();
	break;


	// Create Adminaccount
	case 7:
		if ($_POST["email"] == "") $func->error($lang["install"]["admin_err_noemail"], "index.php?mod=install&action=wizard&step=6");
		elseif ($_POST["password"] == "") $func->error($lang["install"]["admin_err_nopw"], "index.php?mod=install&action=wizard&step=6");
		elseif ($_POST["password"] != $_POST["password2"]) $func->error($lang["install"]["admin_err_pwnotequal"], "index.php?mod=install&action=wizard&step=6");
		else {
			// Check for existing Admin-Account.
			$row = $db->query_first("SELECT email FROM {$config["tables"]["user"]} WHERE email='{$_POST["email"]}'");

			// If found, update password
			if ($row['email']) $db->query("UPDATE {$config["tables"]["user"]} SET
				password = '". md5($_POST["password"]) ."',
				type = '3'
				WHERE email='{$_POST["email"]}'
				");

			// If not found, insert
			else {
				$db->query("INSERT INTO {$config["tables"]["user"]} SET
						username = 'ADMIN',
						firstname = 'ADMIN',
						name = 'ADMIN',
						email='{$_POST["email"]}',
						password = '". md5($_POST["password"]) ."',
						type = '3'
						");
				$userid = $db->insert_id();
				$db->query("INSERT INTO {$config["tables"]["usersettings"]} SET userid = '$userid'");
			}
		}
	// No break!


	// Set main config-variables
	case 8:
		$dsp->NewContent($lang["install"]["wizard_vars_caption"], $lang["install"]["wizard_vars_subcaption"]);

		$dsp->SetForm("index.php?mod=install&action=wizard&step=9");

		// Country
		// Get Selections
		$get_cfg_selection = $db->query("SELECT cfg_display, cfg_value
				FROM {$config["tables"]["config_selections"]}
				WHERE cfg_key = 'country'
				");
		$country_array = array();
		while ($selection = $db->fetch_array($get_cfg_selection)){
			($language == $selection["cfg_value"]) ? $selected = "selected" : $selected = "";
			array_push ($country_array, "<option $selected value=\"{$selection["cfg_value"]}\">". t($selection["cfg_display"]) ."</option>");
		}
		$dsp->AddDropDownFieldRow("country", $lang["install"]["vars_country"], $country_array, "");

		// URL & Admin-Mail
		$dsp->AddHRuleRow();
		$dsp->AddTextFieldRow("url", $lang["install"]["vars_url"], 'http://'. $_SERVER['HTTP_HOST'], "");
		$dsp->AddTextFieldRow("email", $lang["install"]["vars_email"], 'webmaster@'. $_SERVER['HTTP_HOST'], "");

		// Online, or offline mode?
		$dsp->AddHRuleRow();
		$mode_array = array();
		if ($_SERVER['HTTP_HOST'] == 'localhost' or $_SERVER['HTTP_HOST'] == '127.0.0.1') $selected = ""; else $selected = "selected";
		array_push ($mode_array, '<option $selected value="1">'. $lang['install']['vars_system_mode_internet'] .'</option>');
		if ($_SERVER['HTTP_HOST'] == 'localhost' or $_SERVER['HTTP_HOST'] == '127.0.0.1') $selected = "selected"; else $selected = "";
		array_push ($mode_array, '<option $selected value="0">'. $lang['install']['vars_system_mode_intranet'] .'</option>');
		$dsp->AddDropDownFieldRow("mode", $lang["install"]["vars_system_mode"], $mode_array, "");

		$dsp->AddFormSubmitRow("next");

		$dsp->AddBackButton("index.php?mod=install&action=wizard&step=6", "install/vars");
		$dsp->AddContent();
	break;


	// Display final hints
	case 9:
		// Set variables
		$db->query("UPDATE {$config['tables']['config']} SET cfg_value = '{$language}' WHERE cfg_key = 'sys_language'");
		$db->query("UPDATE {$config['tables']['config']} SET cfg_value = '{$_POST['country']}' WHERE cfg_key = 'sys_country'");
		$db->query("UPDATE {$config['tables']['config']} SET cfg_value = '{$_POST['url']}' WHERE cfg_key = 'sys_partyurl'");
		$db->query("UPDATE {$config['tables']['config']} SET cfg_value = '{$_POST['email']}' WHERE cfg_key = 'sys_party_mail'");
		$db->query("UPDATE {$config['tables']['config']} SET cfg_value = '{$_POST['mode']}' WHERE cfg_key = 'sys_internet'");

		unset($_SESSION['language']);

		$dsp->NewContent($lang["install"]["wizard_final_caption"], $lang["install"]["wizard_final_subcaption"]);
        	
		$dsp->AddSingleRow($lang["install"]["wizard_final_text"]);
		if (!func::admin_exists()) $dsp->AddSingleRow("<font color=red>". $lang["install"]["wizard_warning_noadmin"] ."</font>");

		$dsp->AddDoubleRow("", $dsp->FetchButton("index.php?mod=install", "login"));
		$dsp->AddBackButton("index.php?mod=install&action=wizard&step=6", "install/admin");
		$dsp->AddContent();
		
		$config["environment"]["configured"] = 1;
		$install->WriteConfig($cfg_set);
	break;
}

?>
