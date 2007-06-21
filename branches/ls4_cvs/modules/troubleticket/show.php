<?php // by denny@esa-box.de

switch ($_GET["step"]) {
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
			$dsp->NewContent($lang['troubleticket']['show'],$lang['troubleticket']['show_info']);

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
					$time_text = "";
					$time_val = "";
				break;

				// status: GEPRÜFT / ggf. VON EINEM ORGA NEU EINGETRAGEN
				case 2:
					$status	= $lang['troubleticket']['st_acc'];
					$time_text = $lang['troubleticket']['st_checked'];
					$time_val = $func->unixstamp2date($row["verified"], "daydatetime");
				break;

				// status: ORGA HAT ARBEIT BEGONNEN
				case 3:
					$status	= $lang['troubleticket']['st_in_work'];
					$time_text = $lang['troubleticket']['st_in_work_since'];
					$time_val = $func->unixstamp2date($row["process"], "daydatetime");
				break;

				// status: BEARBEITUNG ABGESCHLOSSEN
				case 4:
					$status	= $lang['troubleticket']['st_finished'];
					$time_text = $lang['troubleticket']['st_finish_since'];
					$time_val = $func->unixstamp2date($row["finished"],"daydatetime");
				break;

				// status: BEARBEITUNG ABGELEHNT
				case 5:
					$status	= $lang['troubleticket']['st_denied'];
					$time_text = $lang['troubleticket']['st_denied_since'];
					$time_val = $func->unixstamp2date($row["finished"], "daydatetime");
				break;
			}
			$dsp->AddDoubleRow($lang['troubleticket']['status'], $status);
			if ($time_text and $time_val) $dsp->AddDoubleRow($time_text, $time_val);
			$dsp->AddDoubleRow($lang['troubleticket']['assign_orga'], $get_targetuser["username"]);

			if (!$row["publiccomment"]) $row["publiccomment"] = $lang['troubleticket']['no_hint'];
			$dsp->AddDoubleRow($lang['troubleticket']['com'], $row["publiccomment"]);
			if($auth['type'] > 1){
				if (!$row["orgacomment"]) $row["orgacomment"] = $lang['troubleticket']['no_hint'];
				$dsp->AddDoubleRow($lang['troubleticket']['com_fa4orga'], $row["orgacomment"]);
			}

			$dsp->AddBackButton("index.php?mod=troubleticket", "troubleticket/change");
			$dsp->AddContent();
		}
	break;
}
?>
