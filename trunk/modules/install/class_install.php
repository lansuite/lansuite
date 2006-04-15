<?php
include_once("modules/install/class_import.php");
$import = New Import();

class Install {

	// Get Config (/inc/base/config.php) an change it
	function WriteConfig($values = NULL) {
		global $config;

		$conf = @file("inc/base/config.php");

		$i = 1;
		while ($row = $conf[$i]) {
			// Get Next Element if this is a Comment or a "Header"
			if (stristr($row, ";")) {
				$i++;
				continue;
			}
			if (stristr($row, "[")) {
				$setting['category'] = substr(trim($row), 1, -1);
				$i++;
				continue;
			}

			$setting['name'] = trim(strtok($row, "="));

			$tabs = "";
			for ($z = 0; $z < (4 - (strlen($setting['name']) / 8)); $z++) $tabs .= "\t";
			if ($setting['name']) $conf[$i] = $setting['name'] . $tabs ."= \"". $config[$setting['category']][$setting['name']] ."\"\r\n";


			$i++;
		} // END while( $row = $file[$i] )


		// Write new settings to the config.php file
		// Have we opened the file??? If not, tell the user ...
		if($fh = @fopen("inc/base/config.php", "w")) {
			foreach($conf AS $row) {
				@fwrite($fh, $row, strlen($row));
			}
			@fclose( $fh );
			return 1;
		} else return 0;
	}


	// Connect to DB and create Database, if not exist
	function TryCreateDB($createnew = NULL){
		global $config, $_GET;

		$dbserver	= $config["database"]["server"];
		$dbuser		= $config["database"]["user"];
		$dbpasswd	= $config["database"]["passwd"];
		$database	= $config["database"]["database"];

		$link_id = mysql_connect($dbserver, $dbuser, $dbpasswd);
		if ($link_id) {

			// If User wants to rewrite all tables, drop databse. It will be created anew in the next step
			if ((!$_GET["quest"]) && ($createnew) && ($_GET["step"] == 3)) mysql_query("DROP DATABASE $database");

			// Try to select DB
			if (@mysql_select_db($database, $link_id)) $ret_val = 1;
			else {

  			@mysql_query("/*!40101 SET NAMES utf8_general_ci */;", $link_id);
         
				// Try to create DB
				$query_id = @mysql_query("CREATE DATABASE $database CHARACTER SET utf8 COLLATE utf8_general_ci", $link_id);
				if ($query_id) $ret_val = 3; else $ret_val = 2;
			}
		} else $ret_val = 0;
		@mysql_close($link_id);

		return $ret_val;

		// Return-Values:
		// 0 = Server not available
		// 1 = DB already exists
		// 2 = Create failed (i.e. insufficient rights)
		// 3 = Create successe
	}


	function SetTableNames() {
		global $db, $config;

		// Importent Tables
		$config['tables']['config'] 	= $config['database']['prefix'].'config';
		$config['tables']['user'] 	= $config['database']['prefix'].'user';

		$res = $db->query("SELECT name FROM {$config["database"]["prefix"]}table_names");
		while ($row = $db->fetch_array($res)){
			$config['tables'][$row['name']] = $config['database']['prefix'] . $row['name'];
		}
		$db->free_result($res);
	}


	// Creates a DB-table using the file $table, located in the mod_settings-directory of the module $mod
	function WriteTableFromFile($mod, $table){
		global $db;

		// Read Tablefile an insert it in database
		if ($mod == "") $mod = "install";

		$file = "modules/$mod/mod_settings/lansuite_$table.sql";
		$handle = fopen ($file, "rb");
		$contents = fread ($handle, filesize ($file));
		fclose ($handle);

		$querrys = explode(";", trim($contents));

		while (list ($key, $val) = each ($querrys)) {
			if ($val) if (!$db->query($val)) return 0;
		}
		return 1;
	}


	// Creates a DB-table using the file $table, located in the mod_settings-directory of the module $mod
	function WriteTableFromXMLFile($mod, $rewrite = NULL){
		global $import;

		$import->GetImportHeader("modules/$mod/mod_settings/db.xml");
		$import->ImportXML($rewrite);
	}




	function CreateTableNames() {
		global $db, $config, $xml;

		$modules = opendir("modules");
		while ($module = readdir($modules)) if ($module != "." and $module != ".." and $module != "CVS" and is_dir($module)) {
			if (is_dir("modules/$module/mod_settings")) {
				// Try db.xml
				$file = "modules/$module/mod_settings/db.xml";
				if (file_exists($file)) {
					$xml_file = fopen($file, "r");
					$xml_content = fread($xml_file, filesize($file));
					fclose($xml_file);

					$lansuite = $xml->get_tag_content("lansuite", $xml_content);
					$tables = $xml->get_tag_content_array("table", $lansuite);
					foreach ($tables as $table) {
						$table_head = $xml->get_tag_content("table_head", $table);
						$table_name = $xml->get_tag_content("name", $table_head);
						$db->query("INSERT INTO {$config["database"]["prefix"]}table_names SET name = '$table_name'");
					}
				}
			}
		}
	}



	// Scans 'install/db_skeleton/' for non-existand tables and creates them
	// Puts the results to the screen, by using $dsp->AddSingleRow for each table, if $display_to_screen = 1
	function CreateNewTables($display_to_screen) {
		global $dsp, $config, $lang, $db, $import;

		$tablecreate = Array("anz" => 0, "created" => 0, "exist" => 0, "failed" => "");
		$dsp->AddSingleRow("<b>". $lang["install"]["db_createtables"] ."</b>");

		$db->query("CREATE TABLE IF NOT EXISTS {$config["database"]["prefix"]}table_names (name varchar(80) NOT NULL default '') TYPE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci");
		$db->query("REPLACE INTO {$config["database"]["prefix"]}table_names SET name = 'table_names'");

		if (is_dir("modules")) {
			$modules_dir = opendir("modules/");
			while ($module = readdir($modules_dir)) if ($module != "." AND $module != ".." AND $module != "CVS" AND is_dir("modules/$module")) {
			
				if (is_dir("modules/$module/mod_settings")) {
					// Try to find DB-XML-File
					if (file_exists("modules/$module/mod_settings/db.xml")){
						$this->WriteTableFromXMLFile($module);
						if ($display_to_screen) $dsp->AddDoubleRow("Modul '$module'", "[<a href=\"install.php?mod=install&action=db&step=7&module=$module&quest=1\">{$lang["install"]["db_rewrite"]}</a>]");
					}
				}
			}
			closedir($modules_dir);
		}

		if ($display_to_screen) $dsp->AddDoubleRow("<b>". $lang["install"]["db_alltables"] ."</b>", "[<a href=\"install.php?mod=install&action=db&step=3&quest=1\">{$lang["install"]["db_rewrite"]}</a>]");

		return $tablecreate;
	}


	// Insert Setting-Entrys in DB, if not exist
	function InsertSettings($module) {
		global $db, $config, $xml, $func;

		if (is_dir('modules')) {
			$modules_dir = opendir('modules/');
			while ($module = readdir($modules_dir)) if ($module != '.' and $module != '..' and $module != 'CVS' and is_dir("modules/$module")) {

				if (is_dir("modules/$module/mod_settings")) {
					// Try to find DB-XML-File
					$ConfigFileName = "modules/$module/mod_settings/config.xml";
					if (file_exists($ConfigFileName)){

    				$handle = fopen ($ConfigFileName, "r");
    				$xml_file = fread ($handle, filesize ($ConfigFileName));
    				fclose ($handle);

            $SettingList = array();

            $xml_config = $xml->get_tag_content('config', $xml_file);
            $xml_groups = $xml->get_tag_content_array('group', $xml_config);
            if ($xml_groups) while ($xml_group = array_shift($xml_groups)) {
              $xml_head = $xml->get_tag_content('head', $xml_group);
              $group = $xml->get_tag_content('name', $xml_head);

              $xml_items = $xml->get_tag_content_array('item', $xml_group);
              if ($xml_items) while ($xml_item = array_shift($xml_items)) {
        				$name = $xml->get_tag_content('name', $xml_item);
        				$type = $xml->get_tag_content('type', $xml_item);
        				$default = $xml->get_tag_content('default', $xml_item);
        				$description = $xml->get_tag_content('description', $xml_item);
        				array_push($SettingList, $name);
      				
        				// Insert into DB, if not exists
        				$found = $db->query_first("SELECT cfg_key FROM {$config["database"]["prefix"]}config WHERE cfg_key = '$name' and cfg_module = '$module'");
        				if (!$found['cfg_key']) $db->query("INSERT INTO {$config["database"]["prefix"]}config SET
        				  cfg_key = '". $func->escape_sql($name) ."',
        				  cfg_value = '". $func->escape_sql($default) ."',
        				  cfg_type = '". $func->escape_sql($type) ."',
        				  cfg_group = '". $func->escape_sql($group) ."',
        				  cfg_desc = '". $func->escape_sql($description) ."',
        				  cfg_module = '$module'
        				  ");
        			}
            }

      			// Delete Settings from DB, which are no longer in the modules config.sql
      			$settings_db = $db->query("SELECT cfg_key FROM {$config["database"]["prefix"]}config WHERE (cfg_module = '$module')");
      			while ($setting_db = $db->fetch_array($settings_db)) {
      				if (!in_array($setting_db["cfg_key"], $SettingList)) $db->query("DELETE FROM {$config["database"]["prefix"]}config WHERE cfg_key = '{$setting_db["cfg_key"]}'");
      			}
					}
				}
			}
			closedir($modules_dir);
		}

/*
		$file = "modules/$module/mod_settings/config.sql";
		if (file_exists($file)) {
			$fp2 = fopen($file, "r");
			$contents2 = fread ($fp2, 1024*256);
			fclose ($fp2);

			// Read cfg_key fro config.sql-File
			$querrys2 = explode(";", trim($contents2));
			$settings_cfg = array();
			$settings_cfg_found = array();
			while (list ($key, $val) = each ($querrys2)) if ($val) {
				$entry_key = substr($val, strpos($val, "('") + 2, strlen($val));
				$entry_key = substr($entry_key, 0, strpos($entry_key, "'"));

				array_push($settings_cfg, $entry_key);
			}

			$module .= "_";
			if ($module == "downloads_") $module = "Download";
			if ($module == "usrmgr_") $module = "Userdetails";
			if ($module == "tournament2_") $module = "Turnier";
			if ($module == "install_") $module = "System";

			// Delete Settings from DB, which are no longer in the modules config.sql
			$settings_db = $db->query("SELECT cfg_key
					FROM {$config["database"]["prefix"]}config
					WHERE (cfg_group = '$module') OR (cfg_key LIKE '$module%')
					");
			while ($setting_db = $db->fetch_array($settings_db)) {
				if (in_array($setting_db["cfg_key"], $settings_cfg)) array_push($settings_cfg_found, $setting_db["cfg_key"]);
				else $db->query("DELETE FROM {$config["database"]["prefix"]}config WHERE cfg_key = '{$setting_db["cfg_key"]}'");
			}

			// Insert Settings in DB, which only exist in the modules config.sql
			$add_to_db = array_diff($settings_cfg, $settings_cfg_found);

			reset($querrys2);
			while (list ($key, $val) = each ($querrys2)) if ($val) {
				$entry_key = substr($val, strpos($val, "('") + 2, strlen($val));
				$entry_key = substr($entry_key, 0, strpos($entry_key, "'"));

				if (in_array($entry_key, $add_to_db))
					$db->query("REPLACE INTO {$config["database"]["prefix"]}config (cfg_key, cfg_value, cfg_type, cfg_group, cfg_desc) VALUES $val");
			}
		}
*/
	}


	// Insert PLZ-Entrys in DB, if not exist
	function InsertPLZs() {
		global $db, $config;

		$return_val = 1;
		$find = $db->query("SELECT * FROM {$config["tables"]["locations"]}");
		if ($db->num_rows($find) == 0) {
			$return_val = 2;
			$file = "modules/install/db_insert_locations.sql";
			if (file_exists($file)) {
				$fp = fopen($file, "r");
				$contents = fread($fp, filesize($file));
				fclose($fp);

				$querrys = explode(";", trim($contents));
				while (list ($key, $val) = each ($querrys)) if ($val) {
					if (!$db->query("REPLACE INTO {$config["database"]["prefix"]}locations (plz, breite, laenge) VALUES ". $val)) $return_val = 0;
				}
			}
		}
		$db->free_result($find);

		return $return_val;
		// 0 = At least one create failed
		// 1 = Alreday existing
		// 2 = Create success
	}


	// Auto-Load Modules from XML-Files
	function InsertModules($rewrite = false) {
		global $db, $config, $xml, $func;

		// Tabelle Modules leeren um Module zu deinstallieren
		if($_GET["action"] == "wizard"){
			$db->query("TRUNCATE TABLE {$config["tables"]["modules"]}");
		}
		
		$mod_list = array();
		$modules_dir = opendir("modules/");
		while ($module = readdir($modules_dir)) if ($module != "." AND $module != ".." AND $module != "CVS" AND is_dir("modules/$module")) {

			$file = "modules/$module/mod_settings/module.xml";
			if (file_exists($file)) {
				$handle = fopen ($file, "r");
				$xml_file = fread ($handle, filesize ($file));
				fclose ($handle);

				array_push($mod_list, $module);

				$name = $xml->get_tag_content("name", $xml_file);
				$caption = $xml->get_tag_content("caption", $xml_file);
				$description = $xml->get_tag_content("description", $xml_file);
				$author = $xml->get_tag_content("author", $xml_file);
				$email = $xml->get_tag_content("email", $xml_file);
				$active = $xml->get_tag_content("active", $xml_file);
				$changeable = $xml->get_tag_content("changeable", $xml_file);
				$version = $xml->get_tag_content("version", $xml_file);
				$state = $xml->get_tag_content("state", $xml_file);

				$mod_found = $db->query_first("SELECT 1 AS found FROM {$config["tables"]["modules"]} WHERE name = '$module'");

				$this->InsertSettings($module);

				if ($name) {
					if (!$mod_found["found"]) $db->query_first("REPLACE INTO {$config["tables"]["modules"]} SET
						name='". $func->escape_sql($name) ."',
						caption='". $func->escape_sql($caption) ."',
						description='". $func->escape_sql($description) ."',
						author='". $func->escape_sql($author) ."',
						email='". $func->escape_sql($email) ."',
						active='". $func->escape_sql($active) ."',
						changeable='". $func->escape_sql($changeable) ."',
						version='". $func->escape_sql($version) ."',
						state='". $func->escape_sql($state) ."'
						");
					elseif ($rewrite) $db->query_first("REPLACE INTO {$config["tables"]["modules"]} SET
						name='". $func->escape_sql($name) ."',
						caption='". $func->escape_sql($caption) ."',
						description='". $func->escape_sql($description) ."',
						author='". $func->escape_sql($author) ."',
						email='". $func->escape_sql($email) ."',
						changeable='". $func->escape_sql($changeable) ."',
						version='". $func->escape_sql($version) ."',
						state='". $func->escape_sql($state) ."'
						");
				}
			}
		}

		// Delete non-existend Modules from DB
		$mods = $db->query("SELECT name FROM {$config["tables"]["modules"]}");
		while($row = $db->fetch_array($mods)) {
			if (!in_array($row["name"], $mod_list)) $db->query("DELETE FROM {$config["tables"]["modules"]} WHERE name = '{$row["name"]}'");
		}
		$db->free_result($mods);
	}


	// Auto-Load Menuentries from XML-Files
	function InsertMenus($rewrite = false) {
		global $db, $config, $xml;

		$modules_dir = opendir("modules/");
		while ($module = readdir($modules_dir)) if ($module != "." AND $module != ".." AND $module != "CVS" AND is_dir("modules/$module")) {
			$file = "modules/$module/mod_settings/menu.xml";
			if (file_exists($file)) {

				$menu_found = $db->query_first("SELECT 1 AS found FROM {$config["tables"]["menu"]} WHERE module = '$module'");

				$i = 0;
				if ($rewrite or (!$menu_found["found"])) {
					$db->query_first("DELETE FROM {$config["tables"]["menu"]} WHERE module = '$module'");

					$handle = fopen ($file, "r");
					$xml_file = fread ($handle, filesize ($file));
					fclose ($handle);

					$menu = $xml->get_tag_content("menu", $xml_file);

					$main_pos = $xml->get_tag_content("pos", $menu);
					$entrys = $xml->get_tag_content_array("entry", $menu);

					foreach ($entrys as $entry) {
						$action = $xml->get_tag_content("action", $entry);
						$file = $xml->get_tag_content("file", $entry);
						$caption = $xml->get_tag_content("caption", $entry);
						$hint = $xml->get_tag_content("hint", $entry);
						$link = $xml->get_tag_content("link", $entry);
						$requirement = $xml->get_tag_content("requirement", $entry);
						$level = $xml->get_tag_content("level", $entry);
						$needed_config = $xml->get_tag_content("needed_config", $entry);

						if ($file == "") $file = $action;
						if (!$level) $level = 0;
						if (!$requirement) $requirement = 0;

						($level == 0)? $pos = $main_pos : $pos = $i;
						$i++;

						$db->query_first("INSERT INTO {$config["tables"]["menu"]} SET
								module='$module',
								action='$action',
								file='$file',
								caption='$caption',
								hint='$hint',
								link='$link',
								requirement=$requirement,
								level=$level,
								pos=$pos,
								needed_config='$needed_config'
								");
					}
				}
			}
		}
	}


	function InsertTranslations() {
		global $db, $config, $xml, $func;

		$modules_dir = opendir("modules/");
		while ($module = readdir($modules_dir)) if ($module != "." AND $module != ".." AND $module != "CVS" AND is_dir("modules/$module")) {
			$file = "modules/$module/mod_settings/translations.xml";
			if (file_exists($file)) {

				$handle = fopen ($file, "r");
				$xml_file = fread ($handle, filesize ($file));
				fclose ($handle);

				$translations = $xml->get_tag_content("translations", $xml_file);

				$entrys = $xml->get_tag_content_array("entry", $translations);

				foreach ($entrys as $entry) {
					$de = $func->escape_sql($xml->get_tag_content("de", $entry));
					$en = $func->escape_sql($xml->get_tag_content("en", $entry));

					$db->query_first("REPLACE INTO {$config["tables"]["translations"]} SET
							de='$de',
							en='$en'
							");
				}
			}
		}
	}


	// System pr�fen
	function envcheck(){
		global $lang, $dsp, $config, $func;

		$continue = 1;

		// Environment Check
		$ok = "<span class=\"okay\">{$lang["install"]["env_valid"]}</span>" . HTML_NEWLINE;
		$failed = "<span class=\"error\">{$lang["install"]["env_invalid"]}</span>" . HTML_NEWLINE;
		$warning = "<span class=\"warning\">{$lang["install"]["env_warning"]}</span>" . HTML_NEWLINE;
		$not_possible = "<span class=\"warning\">{$lang["install"]["env_stats_info"]}</span>" . HTML_NEWLINE;

		// Display System-Variables
		$mysql_version = @mysql_get_server_info();
		if (!$mysql_version) $mysql_version = $lang["install"]["unknown"];
		$dsp->AddDoubleRow("System-Info", "<table width=\"99%\">"
			."<tr><td class=\"row_value\">PHP-Version:</td><td class=\"row_value\">". phpversion() ."</td></tr>"
			."<tr><td class=\"row_value\">MySQL-Version:</td><td class=\"row_value\">$mysql_version</td></tr>"
			."<tr><td class=\"row_value\">Max. Script-Execution-Time:</td><td class=\"row_value\">". ini_get('max_execution_time') ." Sec.</td></tr>"
			."<tr><td class=\"row_value\">Max. Data-Input-Zeit:</td><td class=\"row_value\">". ini_get('max_input_time') ." Sec.</td></tr>"
			."<tr><td class=\"row_value\">Memory Limit:</td><td class=\"row_value\">". ini_get('memory_limit') ." MB</td></tr>"
			."<tr><td class=\"row_value\">Max. Post-Form Size:</td><td class=\"row_value\">". (int)ini_get('post_max_size') ." MB</td></tr>"
			."<tr><td class=\"row_value\">Free space:</td><td class=\"row_value\">". $func->FormatSize(disk_free_space('.')) .' / '. $func->FormatSize(disk_total_space('.')) .'</td></tr>'
			."</table>"
			);

		// PHP-Version
		if (version_compare(phpversion(), "4.1.2") >= 0) $phpv_check = $ok;
		else $phpv_check = $failed . str_replace("%VERSION%", phpversion(), $lang["install"]["env_phpversion"]);
		$dsp->AddDoubleRow("PHP Version", $phpv_check);

		// MySQL installed?
		if (extension_loaded("mysql")) $mysql_check = $ok;
		else {
			$mysql_check = $failed . $lang["install"]["env_no_mysql"];
			$continue = 0;
		}
		$dsp->AddDoubleRow("MySQL", $mysql_check);

		// Register Globals
		if (ini_get('register_globals') == FALSE) $rg_check = $ok;
		else $rg_check = $warning . $lang["install"]["env_rg"];
		$dsp->AddDoubleRow("Register Globals", $rg_check);

		// Test Safe-Mode
		if (!ini_get("safe_mode")) $safe_mode = $ok;
		else $safe_mode = $not_possible . $lang["install"]["env_safe_mode"];
		$dsp->AddDoubleRow("Safe Mode", $safe_mode);

		// Magic Quotes
		if (get_magic_quotes_gpc()){
			$mq_check = $ok;
			$config["environment"]["mq"] = 1;
		} else {
			$mq_check = $not_possible . $lang["install"]["env_mq"];
			$config["environment"]["mq"] = 0;
		}
		$dsp->AddDoubleRow("Magic Quotes", $mq_check);

		// GD-Lib
		if (extension_loaded ('gd')){
			if (function_exists("gd_info")) {
				$GD = gd_info();
				if (!strstr($GD["GD Version"], "2.0")) $gd_check = $warning . $lang["install"]["env_gd1"];
				elseif (!$GD["FreeType Support"]) $gd_check = $warning . $lang["install"]["env_gd2"];
				else $gd_check = $ok;
				$gd_check .= '<table>';
				foreach ($GD as $key => $val) $gd_check .= '<tr><td class="content">'. $key .'</td><td class="content">'. $val .'</td></tr>';
				$gd_check .= '</table>';
				$config["environment"]["gd"] = 1;
			} else $gd_check = $warning . $lang["install"]["env_gd1"];
		} else {
			$gd_check = $failed . $lang["install"]["env_gd"];
			$config["environment"]["gd"] = 0;
		}
		$dsp->AddDoubleRow("GD Library", $gd_check);

		// SNMP-Lib
		if (extension_loaded('snmp')){
			$snmp_check = $ok;
			$config["environment"]["snmp"] = 1;
		} else {
			$snmp_check = $not_possible . $lang["install"]["env_snmp"];
			$config["environment"]["snmp"] = 0;
		}
		$dsp->AddDoubleRow("SNMP Library", $snmp_check);

		// FTP-Lib
		if (extension_loaded('ftp')){
			$ftp_check = $ok;
			$config["environment"]["ftp"] = 1;
		} else {
			$ftp_check = $not_possible . $lang["install"]["env_ftp"];
			$config["environment"]["ftp"] = 0;
		}
		$dsp->AddDoubleRow("FTP Library", $ftp_check);

		// config.php Rights
		$lansuite_conf = "inc/base/config.php";
		if (!file_exists($lansuite_conf)) $cfgfile_check = $failed . $lang["install"]["env_no_cfgfile"];
		elseif (!is_writable($lansuite_conf)) $cfgfile_check = $failed . $lang["install"]["env_cfg_file"];
		else $cfgfile_check = $ok;
		$dsp->AddDoubleRow($lang["install"]["env_cfg_file_key"], $cfgfile_check);
		if ($cfgfile_check != $ok) $continue = 0;

		// Server statistic
		$config["server_stats"]["status"] = 0;
		$config["server_stats"]["uptime"] = 0;
		$config["server_stats"]["cpuinfo"] = 0;
		$config["server_stats"]["meminfo"] = 0;
		$config["server_stats"]["loadavg"] = 0;
		$config["server_stats"]["ifconfig"] = 0;
		$config["server_stats"]["ls_getinfo"] = 0;

		// Testing Safe-Mode and execution of system-programs
		if (!ini_get("safe_mode")) {
			if (stristr(strtolower($_SERVER['SERVER_SOFTWARE']) , "win") == ""){
				if (@shell_exec("cat /proc/uptime") == ""){
					$env_stats .= "<strong>/proc/uptime</strong>" . HTML_NEWLINE;
					$config["server_stats"]["uptime"] = 0;
				} else $config["server_stats"]["uptime"] = 1;

				if (@shell_exec("ifconfig") == ""){
					$env_stats .= "<strong>ifconfig</strong>" . HTML_NEWLINE;
					$config["server_stats"]["ifconfig"] = 0;
				} else $config["server_stats"]["ifconfig"] = 1;

				if (@shell_exec("cat /proc/loadavg") == ""){
					$env_stats .= "<strong>/proc/loadavg</strong>" . HTML_NEWLINE;
					$config["server_stats"]["loadavg"] = 0;
				} else $config["server_stats"]["loadavg"] = 1;

				if (@shell_exec("cat /proc/cpuinfo") == ""){
					$env_stats .= "<strong>/proc/cpuinfo</strong>" . HTML_NEWLINE;
					$config["server_stats"]["cpuinfo"] = 0;
				} else $config["server_stats"]["cpuinfo"] = 1;	

				if (@shell_exec("cat /proc/meminfo") == ""){
					$env_stats .= "<strong>/proc/meminfo</strong>" . HTML_NEWLINE;
					$config["server_stats"]["meminfo"] = 0;
				} else $config["server_stats"]["meminfo"] = 0;

				if ($env_stats == "") $server_stats = $ok;
				else $server_stats = $not_possible . ereg_replace("{FEHLER}", $env_stats, $lang["install"]["env_stats"]);

				$config["server_stats"]["status"] = 1;
			} else {
				system("modules\stats\ls_getinfo.exe", $status);
				if ($status == 0){
					$config["server_stats"]["status"] = 1;
					$config["server_stats"]["ls_getinfo"] = 1;
					$server_stats = $ok;
				} else {
					$env_stats = "<strong>modules/stats/ls_getinfo.exe</strong>" . HTML_NEWLINE;
					$server_stats = $not_possible . ereg_replace("{FEHLER}", $env_stats, $lang["install"]["env_stats"]);
					$config["server_stats"]["status"] = 0;
				}
			}
			$dsp->AddDoubleRow("Server Stats", $server_stats);
		}

		// Ext_inc Rights
		$ext_inc = "ext_inc";
		if (!file_exists($ext_inc)) $ext_inc_check = $failed . $lang["install"]["env_no_ext_inc"];
		elseif (!is_writable($ext_inc)) $ext_inc_check = $failed . $lang["install"]["env_ext_inc"];
		else $ext_inc_check = $ok;
		$dsp->AddDoubleRow($lang["install"]["env_ext_inc_key"], $ext_inc_check);

		// Error Reporting
		if (error_reporting() <= (E_ALL ^ E_NOTICE)) $errreport_check = $ok;
		else $errreport_check = $warning . $lang["install"]["env_errreport"];
		$dsp->AddDoubleRow("Error Reporting", $errreport_check);

		// Get Operating System
		$software_arr =  preg_split('/\s/', $_SERVER['SERVER_SOFTWARE'], "", PREG_SPLIT_NO_EMPTY);
		$environment_os =  preg_replace('/\(|\)/', "", $software_arr[1]);
		$config["environment"]["os"] = $environment_os;

		// Get Directory
		$config["environment"]["dir"] = substr($_SERVER['REQUEST_URI'], 1, strpos($_SERVER['REQUEST_URI'] - 1, "install.php"));

		// Set Configs
		$this->WriteConfig();

		return $continue;
	}
	
	function check_updates(){
		global $db, $config;
		
		include("modules/install/class_update.php");
		
		$update = new update();
			// Check update for Version 2.0.2
			$file = "modules/install/update/update202.php";
			if(file_exists($file)){
				include($file);
			}
			
			
		
	}
} // END CLASS
?>
