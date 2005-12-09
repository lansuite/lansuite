<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		0.3
*	Filename: 			add.php
*	Module: 			anmeldung
*	Main editor: 		webmaster@ewep.de
*	Last change: 		10.09.2003 13:31
*	Description: 		Anmeldung für neue Benutzer
*	Remarks:
*
**************************************************************************/

// DEFINE GET/POST VARS:
$step 		= $_GET['step'];


switch($step) {
	default:
		$dsp->NewContent(str_replace("%NAME%", $cfg["sys_lanpartyname"], $lang["signon"]["add_caption"]), str_replace("%NAME%", $cfg["sys_lanpartyname"], $lang["signon"]["add_subcaption"]));
		$dsp->SetForm("index.php?mod=signon&action=config&step=2");
		$dsp->AddDoubleRow("username", "Pflichteingabe");
		$dsp->AddDoubleRow("email", "Pflichteingabe");

		$rows = $db->query("SELECT * FROM {$config["tables"]["config"]} WHERE cfg_group = 'Anmeldungsfelder' ORDER BY cfg_value DESC");
		while($row = $db->fetch_array($rows)) {
			$option_array = array($lang["signon"]["config_option_hide"], $lang["signon"]["config_option_optional"], $lang["signon"]["config_option_duty"]);
			$t_array = array();
			while (list ($key, $val) = each ($option_array)) {
				($key == $row["cfg_value"]) ? $selected = "selected" : $selected = "";
				array_push ($t_array, "<option $selected value=\"$key\">$val</option>");
			}
			$dsp->AddDropDownFieldRow($row["cfg_key"], $row["cfg_key"], $t_array, "", 1);
		}
		$db->free_result($rows);

		$dsp->AddFormSubmitRow("add");
		$dsp->AddContent();
	break;

	case 2:
		while (list($key, $val) = each($_POST)) {
			$db->query("UPDATE {$GLOBALS["config"]["tables"]["config"]} SET cfg_value = $val WHERE cfg_key = '$key'");
		}
		$func->confirmation($lang["signon"]["config_success"], "index.php?mod=signon&action=config");
	break;
}
?>
