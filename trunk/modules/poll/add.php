<?php

/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-----------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.0
*	Filename: 			show.php
*	Module: 			Poll
*	Main editor: 		johannes@one-network.org
*	Last change: 		26.02.03 18:00
*	Description:
*	Remarks:
*
**************************************************************************/
$step = $_GET["step"];
if (($_GET["action"] == "add") && ($step == "")) $step = 2;

//
// Error switch
//

switch($step) {
	case 3:
		if($_POST['poll_caption'] == "")	{
			$caption_err = $lang["poll"]["add_err_nopoll"];
			$step = 2;
		}

		$options = 0;
		$check_values = array();
		foreach ($_POST["poll_option"] as $ind => $option) {
			if (trim($option) != "") {
				$options++;

				if (in_array($option, $check_values)) {
					$poll_option_err[$options] = $lang["poll"]["add_err_noequal"];
					$step = 2;
				}
				$check_values[] = $option;
			} else unset($_POST["poll_option"][$ind]);
		}

		if ($options < 1) {
			$poll_option_err[1] = $lang["poll"]["add_err_leasttwo"];
			$step = 2;
		}
		if ($options < 2) {
			$poll_option_err[2] = $lang["poll"]["add_err_leasttwo"];
			$step = 2;
		}
	break;
}

//
// Show switch
//
switch($step) {
	default:
	  include_once('modules/poll/search.inc.php');
	break;

	case 2:
		$_SESSION["poll_refresh"] = FALSE;

		if ($_GET["action"] == "change") {
			if (!$func->check_exist("pollid", $_GET["pollid"])) {
				$func->error($lang["poll"]["add_err_noexist"], "index.php?mod=poll&action=change");
				break;
			}

			$poll = $db->query_first("SELECT caption, comment, anonym, multi, endtime
				FROM {$config["tables"]["polls"]}
				WHERE pollid = '{$_GET["pollid"]}'
				");
			if ($_POST['poll_caption'] == "") $_POST['poll_caption'] = $poll["caption"];
			if ($_POST['poll_comment'] == "") $_POST['poll_comment'] = $poll["comment"];
			if ($_POST['poll_anonym'] == "") $_POST['poll_anonym'] = $poll["anonym"];
			if ($_POST['poll_multi'] == "") $_POST['poll_multi'] = $poll["multi"];
			if ($_POST['poll_endtime'] == "") $_POST['poll_endtime'] = $poll["endtime"];
			if ($_POST['poll_time'] == "") {
				($_POST['poll_endtime'])? $_POST['poll_time'] = 1 : $_POST['poll_time'] = 0;
			}
			if ($_POST['group_id'] == "") $_POST['group_id'] = $poll["group_id"];
		}

		($_POST['poll_anonym'])? $poll_anonym = "checked" : $poll_anonym = "";
		($_POST['poll_multi'])? $poll_multi = "checked" : $poll_multi = "";
		($_POST['poll_time'])? $poll_time = "checked" : $poll_time = "";
		($_POST['poll_reset'])? $poll_reset = "checked" : $poll_reset = "";

		$dsp->NewContent($lang["poll"]["add_caption"], $lang["poll"]["add_subcaption"]);
		$dsp->SetForm("index.php?mod=poll&action={$_GET["action"]}&step=3&pollid={$_GET["pollid"]}");
		$dsp->AddTextFieldRow("poll_caption", $lang["poll"]["add_title"], $_POST['poll_caption'], $caption_err);
		$dsp->AddTextAreaPlusRow("poll_comment", $lang["poll"]["add_comment"], $_POST['poll_comment'], "", "", "", 1);
		$dsp->AddCheckBoxRow("poll_anonym", $lang["poll"]["add_anonym"], "", "", 1, $poll_anonym);
		$dsp->AddCheckBoxRow("poll_multi", $lang["poll"]["add_multi"], "", "", 1, $poll_multi);
		$dsp->AddCheckBoxRow("poll_time", $lang["poll"]["add_time"], "", "", 1, $poll_time);
		$dsp->AddDateTimeRow("poll_endtime", "", $_POST['poll_endtime'], "");
		$party->get_user_group_dropdown("NULL",1,$_POST['group_id']);

		if ($_GET["action"] == "change") {
			$dsp->AddCheckBoxRow("poll_reset", $lang["poll"]["add_reset"], $lang["poll"]["add_reset2"], "", 1, $poll_reset);
		}

		$dsp->AddHRuleRow();
		$dsp->AddSingleRow($lang["poll"]["add_subcaption2"]);
		// Poll-Optionen ausgeben
		$polloptions = $db->query("SELECT caption
			FROM {$config["tables"]["polloptions"]}
			WHERE pollid = '{$_GET["pollid"]}'
			ORDER BY polloptionid
			");
		for ($z = 1; $row = $db->fetch_array($polloptions); $z++)
			if ($poll_option[$z] == "") $poll_option[$z] = $row["caption"];
		for ($z = 1; $z <= 10; $z++)
			$dsp->AddTextFieldRow("poll_option[$z]", $lang["poll"]["add_option"] ." $z", $poll_option[$z], $poll_option_err[$z]);

		$dsp->AddFormSubmitRow("add");
		$dsp->AddBackButton("index.php?mod=poll", "poll/form1");
		$dsp->AddContent();
	break;

	case 3:
		if ($_SESSION["poll_refresh"]) $func->error("NO_REFRESH", "index.php?mod=poll&action=add");
		else {
			$_SESSION["poll_refresh"] = TRUE;

			if (!$_POST["poll_anonym"]) $_POST["poll_anonym"] = 0;
			if (!$_POST["poll_multi"]) $_POST["poll_multi"] = 0;
			if (!$_POST["poll_time"]) $_POST["poll_time"] = 0;

			($_POST["poll_time"])?
				$poll_endtime = mktime($_POST["poll_endtime_value_hours"], $_POST["poll_endtime_value_minutes"], 0, $_POST["poll_endtime_value_month"], $_POST["poll_endtime_value_day"], $_POST["poll_endtime_value_year"])
				: $poll_endtime = 0;

			if ($_GET["action"] == "change") {
				$db->query("UPDATE {$config["tables"]["polls"]} SET
									caption = '{$_POST["poll_caption"]}',
									comment = '{$_POST["poll_comment"]}',
									anonym = '{$_POST["poll_anonym"]}',
									multi = '{$_POST["poll_multi"]}',
									endtime = '$poll_endtime',
									changedate = NOW(),
									group_id = '{$_POST['group_id']}'
									WHERE pollid = '{$_GET["pollid"]}'");
				$func->confirmation(str_replace("%NAME%", $_POST["poll_caption"], $lang["poll"]["change_success"]), "index.php?mod=poll&action=change");
			}

			if ($_GET["action"] == "add") {
				$db->query("INSERT INTO {$config['tables']['polls']}
							SET caption='{$_POST["poll_caption"]}',
							comment='{$_POST["poll_comment"]}',
							anonym='{$_POST["poll_anonym"]}',
							multi='{$_POST["poll_multi"]}',
							endtime='$poll_endtime',
							changedate = NOW(),
							group_id = '{$_POST['group_id']}'
							");
				$_GET["pollid"] = $db->insert_id();
				$func->confirmation(str_replace("%NAME%", $_POST["poll_caption"], $lang["poll"]["add_success"]), "index.php?mod=poll&action=show&step=2&pollid={$_GET["pollid"]}");
			}

			// Auswahloptionen in DB schreiben
			if (($_POST['poll_reset']) || ($_GET["action"] == "add")) {
				$db->query("DELETE FROM {$config["tables"]["polloptions"]} WHERE pollid='{$_GET["pollid"]}'");
				$db->query("DELETE FROM {$config["tables"]["pollvotes"]} WHERE pollid='{$_GET["pollid"]}'");
				foreach($_POST["poll_option"] as $option) if (trim($option) != "") {
					$db->query("INSERT INTO {$config['tables']['polloptions']}
						SET caption='$option',
						pollid ='{$_GET["pollid"]}'
						");
				}
			}
		}
	break;
} // switch
?>
