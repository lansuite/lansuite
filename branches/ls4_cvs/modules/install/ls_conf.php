<?php

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
			$func->error($lang["install"]["conf_err_write"]);
		} else {
			$func->confirmation($lang["install"]["conf_success"], "index.php?mod=install&action=ls_conf");
		}
	break;
	
	default:
		$dsp->NewContent($lang["install"]["conf_caption"], $lang["install"]["conf_subcaption"]);
		$dsp->SetForm("index.php?mod=install&action=ls_conf&step=2");

		if ($_POST["host"] == "") $_POST["host"] = $config['database']['server'];
		if ($_POST["user"] == "") $_POST["user"] = $config['database']['user'];
		if ($_POST["pass"] == "") $_POST["pass"] = $config['database']['passwd'];
		if ($_POST["database"] == "") $_POST["database"] = $config['database']['database'];
		if ($_POST["prefix"] == "") $_POST["prefix"] = $config['database']['prefix'];
		if ($_POST["display_debug_errors"] == "") $_POST["display_debug_errors"] = $config['database']['display_debug_errors'];

		#### Database Access
		$dsp->AddSingleRow("<b>". $lang["install"]["conf_dbdata"] ."</b>");
		$dsp->AddTextFieldRow("host", $lang["install"]["conf_host"], $_POST["host"], "");
		$dsp->AddTextFieldRow("user", $lang["install"]["conf_user"], $_POST["user"], "");
		$dsp->AddPasswordRow("pass", $lang["install"]["conf_pass"], $_POST["pass"], "");
		$dsp->AddTextFieldRow("database", $lang["install"]["conf_db"], $_POST["database"], "");
		$dsp->AddTextFieldRow("prefix", $lang["install"]["conf_prefix"], $_POST["prefix"], "");
		$t_array = array();
		(!$_POST["display_debug_errors"])? $selected = ' selected' :  $selected = ''; 
		array_push ($t_array, "<option $selected value=\"0\"$selected>{$lang["sys"]["no"]}</option>");
		($_POST["display_debug_errors"])? $selected = ' selected' :  $selected = ''; 
		array_push ($t_array, "<option $selected value=\"1\">{$lang["sys"]["yes"]}</option>");
		$dsp->AddDropDownFieldRow("display_debug_errors", $lang["install"]["conf_display_debug_errors"], $t_array, "");

		#### Default Design
		// Open the design-dir
		$design_dir = opendir("design/");

		// Check all Subdirs of $design_dir fpr valid design-xml-files
		$t_array = array();
		while ($akt_design = readdir($design_dir)) if ($akt_design != "." AND $akt_design != ".." AND $akt_design != "CVS" AND $akt_design != "templates") {

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

		$dsp->AddFormSubmitRow("next");
		$dsp->AddBackButton("index.php?mod=install", "install/ls_conf");
		$dsp->AddContent();
	break;
}
?>
