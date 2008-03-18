<?php // by denny@esa-box.de

switch ($_GET["step"]) {
	default:
    include_once('modules/troubleticket/search.inc.php');	
	break;


	case 2:
		$tt_id = $_GET['ttid'];

		$rowtest = $db->query_first("SELECT COUNT(*) AS n FROM {$config["tables"]["troubleticket"]} WHERE ttid = '$tt_id'");
		$numrows = $rowtest["n"];

		// Pr�fen ob ticketid leer ist
		if ($tt_id == "") $func->information(t('Es wurde keine Troubleticket-ID übergeben. Aufruf inkorrekt.'), "");

		// Pr�fen ob ticketid g�ltig ist
		elseif ($numrows == "") { $func->information(t('Es wurde keine Troubleticket-ID übergeben. Aufruf inkorrekt.'),""); }

		else {
			$dsp->NewContent(t('Troubleticket anzeigen'),t('Hier sehen Sie alle Informationen zu diesem Ticket'));

			// Ticket aus DB laden und ausgeben
			$row = $db->query_first("SELECT * FROM {$config["tables"]["troubleticket"]} WHERE ttid = '$tt_id'");

			$origin_user_id = $row["origin_userid"];
			$get_originuser = $db->query_first("SELECT username FROM {$config["tables"]["user"]} WHERE userid = '$origin_user_id' ");
			$target_user_id = $row["target_userid"];
			$get_targetuser = $db->query_first("SELECT username FROM {$config["tables"]["user"]} WHERE userid = '$target_user_id' ");

			$dsp->AddDoubleRow(t('Überschrift'), $row["caption"]);
			$dsp->AddDoubleRow(t('Problembeschreibung'), $func->text2html($row["text"]));
			$dsp->AddDoubleRow(t('Eingetragen am/um'), $func->unixstamp2date($row["created"], "daydatetime"));
			$dsp->AddDoubleRow(t('Von Benutzer'), $get_originuser["username"]);

			// priorit�t zahl -> text
			switch ($row["priority"]) {
				default:
					$priority = t('Niedrig');
				break;
				case 20:
					$priority = t('Normal');
				break;
				case 30:
					$priority = t('Hoch');
				break;
				case 40:
					$priority = t('Kritisch');
				break;
			}
			$dsp->AddDoubleRow(t('Priorität'), $priority);

			// entsprechend des ticketstatuses passende zeilen ausgeben
			$status_wahl = array();
			switch ($row["status"]) {
				default:
					$status	= t('default: Scriptfehler!');
				break;

				// status: NEU EINGETRAGEN / NICHT GEPR�FT
				case 1:
					$status	= t('Neu / Ungeprüft');
					$time_text = "";
					$time_val = "";
				break;

				// status: GEPR�FT / ggf. VON EINEM ORGA NEU EINGETRAGEN
				case 2:
					$status	= t('Überprüft / Akzeptiert');
					$time_text = t('Überprüft am/um');
					$time_val = $func->unixstamp2date($row["verified"], "daydatetime");
				break;

				// status: ORGA HAT ARBEIT BEGONNEN
				case 3:
					$status	= t('In Arbeit');
					$time_text = t('In Bearbeitung seit');
					$time_val = $func->unixstamp2date($row["process"], "daydatetime");
				break;

				// status: BEARBEITUNG ABGESCHLOSSEN
				case 4:
					$status	= t('Abgeschlossen');
					$time_text = t('Beendet am/um');
					$time_val = $func->unixstamp2date($row["finished"],"daydatetime");
				break;

				// status: BEARBEITUNG ABGELEHNT
				case 5:
					$status	= t('Abgelehnt');
					$time_text = t('Bearbeitung abgelehnt am/um');
					$time_val = $func->unixstamp2date($row["finished"], "daydatetime");
				break;
			}
			$dsp->AddDoubleRow(t('Ticketstatus'), $status);
			if ($time_text and $time_val) $dsp->AddDoubleRow($time_text, $time_val);
			$dsp->AddDoubleRow(t('Bearbeitender Orga'), $get_targetuser["username"]);

			if (!$row["publiccomment"]) $row["publiccomment"] = t(' Kein Hinweis eingetragen');
			$dsp->AddDoubleRow(t('Kommentar'), $row["publiccomment"]);
			if($auth['type'] > 1){
				if (!$row["orgacomment"]) $row["orgacomment"] = t(' Kein Hinweis eingetragen');
				$dsp->AddDoubleRow(t('Kommentar von und für Orgas'), $row["orgacomment"]);
			}

			$dsp->AddBackButton("index.php?mod=troubleticket", "troubleticket/change");
			$dsp->AddContent();
		}
	break;
}
?>