<?php

include_once('modules/install/class_install.php');
$install = new Install();

switch($_GET["step"]) {
	case 2:
		// Set new $config-Vars
		if ($_POST["host"]) $config["database"]["server"] = $_POST["host"];
		if ($_POST["user"]) $config["database"]["user"] = $_POST["user"];
		if ($_POST["pass"]) $config["database"]["passwd"] = $_POST["pass"];
		if ($_POST["database"]) $config["database"]["database"] = $_POST["database"];
		if ($_POST["prefix"]) $config["database"]["prefix"] = $_POST["prefix"];
		if ($_POST["display_debug_errors"] != '') $config["database"]["display_debug_errors"] = $_POST["display_debug_errors"];
		if ($_POST["design"]) $config["lansuite"]["default_design"] = $_POST["design"];

		if(!$install->WriteConfig()) {
			$func->error(t('Datei \'config.php\' konnte <strong>nicht</strong> geschrieben werden.'));
		} else {
			$func->confirmation(t('Datei \'config.php\' wurde erfolgreich geschrieben.'), "index.php?mod=install&action=ls_conf");
		}
	break;
	
	default:
		$dsp->NewContent(t('Grundeinstellungen'), t('Bitte geben Sie nun die Zugangsdaten zur Datenbank an.'));
		$dsp->SetForm("index.php?mod=install&action=ls_conf&step=2");

		if ($_POST["host"] == "") $_POST["host"] = $config['database']['server'];
		if ($_POST["user"] == "") $_POST["user"] = $config['database']['user'];
		if ($_POST["pass"] == "") $_POST["pass"] = $config['database']['passwd'];
		if ($_POST["database"] == "") $_POST["database"] = $config['database']['database'];
		if ($_POST["prefix"] == "") $_POST["prefix"] = $config['database']['prefix'];
		if ($_POST["display_debug_errors"] == "") $_POST["display_debug_errors"] = $config['database']['display_debug_errors'];

		#### Database Access
		$dsp->AddSingleRow("<b>". t('Datenbank-Zugangsdaten') ."</b>");
		$dsp->AddTextFieldRow("host", t('Host (Server-IP)'), $_POST["host"], "");
		$dsp->AddTextFieldRow("user", t('Benutzername'), $_POST["user"], "");
		$dsp->AddPasswordRow("pass", t('Kennwort'), $_POST["pass"], "");
		$dsp->AddTextFieldRow("database", t('Datenbank'), $_POST["database"], "");
		$dsp->AddTextFieldRow("prefix", t('Tabellen-Prefix'), $_POST["prefix"], "");
		$t_array = array();
		(!$_POST["display_debug_errors"])? $selected = ' selected' :  $selected = ''; 
		array_push ($t_array, "<option $selected value=\"0\"$selected>".t('Nein')."</option>");
		($_POST["display_debug_errors"])? $selected = ' selected' :  $selected = ''; 
		array_push ($t_array, "<option $selected value=\"1\">".t('Ja')."</option>");
		$dsp->AddDropDownFieldRow("display_debug_errors", t('MySQL-Fehler zeigen'), $t_array, "");

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
		$dsp->AddDropDownFieldRow("design", t('Standard-Design'), $t_array, "");

		$dsp->AddFormSubmitRow("next");
		$dsp->AddBackButton("index.php?mod=install", "install/ls_conf");
		$dsp->AddContent();
	break;
}
?>
