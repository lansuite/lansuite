<?php // by denny@esa-box.de

function optionrow($name, $text) {
 	return "<option value=\"$name\">$text</option>";
}


switch ($_GET["step"]) {
	case 3:
		if (strlen($_POST["tticket_orgatext"]) > 5000) {
			$func->information($lang['troubleticket']['err_max_size'], "index.php?mod=troubleticket&action=change&step=2&ttid={$_GET["ttid"]}");
			$_GET["step"] = 2;
		}

		if (strlen($_POST["tticket_publictext"]) > 5000) {       // QO: mit vorheriger if zusammenfassen ?
			$func->information($lang['troubleticket']['err_max_size'], "index.php?mod=troubleticket&action=change&step=2&ttid={$_GET["ttid"]}");
			$_GET["step"] = 2;
		}

		if (!$_POST["tticket_publictext"] and $_POST["tticket_status"] == 5) {
			$func->information($lang['troubleticket']['err_no_reason'], "index.php?mod=troubleticket&action=change&step=2&ttid={$_GET["ttid"]}");
			$_GET["step"] = 2;
		}
	break;
}


switch($_GET["step"]) {
	default:
    include_once('modules/troubleticket/search.inc.php');	
	break;

	case 2:
		$tt_id = $_GET['ttid'];

		$rowtest = $db->query_first("SELECT COUNT(*) AS n FROM {$config["tables"]["troubleticket"]} WHERE ttid = '$tt_id'");
		$numrows = $rowtest["n"];

		// Prüfen ob ticketid leer ist
		if ($tt_id == "") $func->information($lang['troubleticket']['err_no_tt_id'], "");

		// Prüfen ob ticketid gültig ist
		elseif ($numrows == "") { $func->information($lang['troubleticket']['err_no_tt_id'],""); }

		else {
			$dsp->NewContent($lang['troubleticket']['headl_edit'], "");

			// Ticket aus DB laden und ausgeben
			$row = $db->query_first("SELECT * FROM {$config["tables"]["troubleticket"]} WHERE ttid = '$tt_id'");

			$origin_user_id = $row["origin_userid"];
			$get_originuser = $db->query_first("SELECT username FROM {$config["tables"]["user"]} WHERE userid = '$origin_user_id' ");
			$target_user_id = $row["target_userid"];
			$get_targetuser = $db->query_first("SELECT username FROM {$config["tables"]["user"]} WHERE userid = '$target_user_id' ");

			$dsp->AddDoubleRow($lang['troubleticket']['head'], $row["caption"]);
			$dsp->AddDoubleRow($lang['troubleticket']['prob_descr'], $func->text2html($row["text"]));
			$dsp->AddDoubleRow($lang['troubleticket']['set_up_on'], $func->unixstamp2date($row["created"], "daydatetime"));
			$dsp->AddDoubleRow($lang['troubleticket']['from_user'], $get_originuser["username"]);

			// priorität zahl -> text
			switch ($row["priority"]) {
				default:
					$priority = $lang['troubleticket']['state_0'];
				break;
				case 20:
					$priority = $lang['troubleticket']['state_1'];
				break;
				case 30:
					$priority = $lang['troubleticket']['state_2'];
				break;
				case 40:
					$priority = $lang['troubleticket']['state_3'];
				break;
			}
			$dsp->AddDoubleRow($lang['troubleticket']['priority'], $priority);

			// entsprechend des ticketstatuses passende zeilen ausgeben
			$status_wahl = array();
			switch ($row["status"]) {
				default:
					$status	= $lang['troubleticket']['st_default'];
				break;

				// status: NEU EINGETRAGEN / NICHT GEPRÜFT
				case 1:
					$status	= $lang['troubleticket']['st_new'];
					array_push($status_wahl, optionrow(4, $lang['troubleticket']['option_finished']));
					array_push($status_wahl, optionrow(5, $lang['troubleticket']['option_refuse']));
					$time_text = "";
					$time_val = "";
				break;

				// status: GEPRÜFT / ggf. VON EINEM ORGA NEU EINGETRAGEN
				case 2:
					$status	= $lang['troubleticket']['st_acc'];
					array_push($status_wahl, optionrow(0, $lang['troubleticket']['option_nochange']));
					array_push($status_wahl, optionrow(2, $lang['troubleticket']['option_back2poll']));
					array_push($status_wahl, optionrow(3, $lang['troubleticket']['option_startwork']));
					array_push($status_wahl, optionrow(4, $lang['troubleticket']['option_finished']));
					array_push($status_wahl, optionrow(5, $lang['troubleticket']['option_refuse']));
					$time_text = $lang['troubleticket']['st_checked'];
					$time_val = $func->unixstamp2date($row["verified"], "daydatetime");
				break;

				// status: ORGA HAT ARBEIT BEGONNEN
				case 3:
					$status	= $lang['troubleticket']['st_in_work'];
					array_push($status_wahl, optionrow(0, $lang['troubleticket']['option_nochange']));
					array_push($status_wahl, optionrow(4, $lang['troubleticket']['option_finished']));
					$time_text = $lang['troubleticket']['st_in_work_since'];
					$time_val = $func->unixstamp2date($row["process"], "daydatetime");
				break;

				// status: BEARBEITUNG ABGESCHLOSSEN
				case 4:
					$status	= $lang['troubleticket']['st_finished'];
					array_push($status_wahl, optionrow(0, $lang['troubleticket']['option_nochange']));
					array_push($status_wahl, optionrow(3, $lang['troubleticket']['option_startwork']));
					$time_text = $lang['troubleticket']['st_finish_since'];
					$time_val = $func->unixstamp2date($row["finished"],"daydatetime");
				break;

				// status: BEARBEITUNG ABGELEHNT
				case 5:
					$status	= $lang['troubleticket']['st_denied'];
					array_push($status_wahl, optionrow(0, $lang['troubleticket']['option_nochange']));
					$time_text = $lang['troubleticket']['st_denied_since'];
					$time_val = $func->unixstamp2date($row["finished"], "daydatetime");
				break;
			}
			$dsp->AddDoubleRow($lang['troubleticket']['status'], $status);
			if ($time_text and $time_val) $dsp->AddDoubleRow($time_text, $time_val);
			$dsp->AddDoubleRow($lang['troubleticket']['assign_orga'], $get_targetuser["username"]);

			$dsp->SetForm("index.php?mod=troubleticket&action=change&step=3&ttid=$tt_id");

			$dsp->AddDropDownFieldRow("tticket_status",$lang['troubleticket']['status_choose'], $status_wahl, $error["tticket_status"], 1);

			$dsp->AddTextAreaPlusRow("tticket_publictext", $lang['troubleticket']['com_4user'], $_POST['tticket_publictext'], $error["tticket_publictext"]);
			$dsp->AddTextAreaPlusRow("tticket_orgatext", $lang['troubleticket']['com_4orga'], $_POST['tticket_orgatext'], $error["tticket_orgatext"]);

			$dsp->AddFormSubmitRow("add");
			$dsp->AddBackButton("index.php?mod=troubleticket", "troubleticket/change");
			$dsp->AddContent();
		}
	break;


	case 3:
		$tt_id = $_GET['ttid'];

		$db->query("UPDATE {$config["tables"]["troubleticket"]} SET
				publiccomment = '{$_POST["tticket_publictext"]}',
				orgacomment = '{$_POST["tticket_orgatext"]}'
				WHERE ttid = '{$tt_id}'");
		$zeit = time();

		switch($_POST["tticket_status"]) {
			case 1:
				$db->query("UPDATE {$config["tables"]["troubleticket"]} SET status = '1' WHERE ttid = '$tt_id'");
			break;

			case 2:
				$db->query("UPDATE {$config["tables"]["troubleticket"]} SET
					status = '2',
					target_userid = '0'
					WHERE ttid = '$tt_id'");
			break;

			case 3:
				$db->query("UPDATE {$config["tables"]["troubleticket"]} SET
					status = '3',
					processstatus = '0',
					process = '{$zeit}',
					finished = ''
					WHERE ttid = '$tt_id'");
			break;

			case 4:
				$db->query("UPDATE {$config["tables"]["troubleticket"]} SET
					status = '4',
					processstatus = '100',
					finished = '{$zeit}'
					WHERE ttid = '$tt_id'");
			break;

			case 5:
				$db->query("UPDATE {$config["tables"]["troubleticket"]} SET
					status = '5',
					processstatus = '100',
					finished = '{$zeit}'
					WHERE ttid = '$tt_id'");
			break;
		}

		$func->confirmation($lang['troubleticket']['change_confirm'], "index.php?mod=troubleticket&action=change");
	break;
}
?>
