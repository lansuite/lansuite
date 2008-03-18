<?php // by denny@esa-box.de

function optionrow($name, $text) {
 	return "<option value=\"$name\">$text</option>";
}


switch ($_GET["step"]) {
	case 3:
		if (strlen($_POST["tticket_orgatext"]) > 5000) {
			$func->information(t('Der Text darf nicht mehr als 5000 Zeichen enthalten'), "index.php?mod=troubleticket&action=change&step=2&ttid={$_GET["ttid"]}");
			$_GET["step"] = 2;
		}

		if (strlen($_POST["tticket_publictext"]) > 5000) {       // QO: mit vorheriger if zusammenfassen ?
			$func->information(t('Der Text darf nicht mehr als 5000 Zeichen enthalten'), "index.php?mod=troubleticket&action=change&step=2&ttid={$_GET["ttid"]}");
			$_GET["step"] = 2;
		}

		if (!$_POST["tticket_publictext"] and $_POST["tticket_status"] == 5) {
			$func->information(t('Bei einer direkten Ablehnung ist die Angabe eines Grundes notwendig.'), "index.php?mod=troubleticket&action=change&step=2&ttid={$_GET["ttid"]}");
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

		// Pr�fen ob ticketid leer ist
		if ($tt_id == "") $func->information(t('Es wurde keine Troubleticket-ID übergeben. Aufruf inkorrekt.'), "");

		// Pr�fen ob ticketid g�ltig ist
		elseif ($numrows == "") { $func->information(t('Es wurde keine Troubleticket-ID übergeben. Aufruf inkorrekt.'),""); }

		else {
			$dsp->NewContent(t('Troubleticket bearbeiten'), "");

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
					array_push($status_wahl, optionrow(4, t(' Auf Erledigt setzen ')));
					array_push($status_wahl, optionrow(5, t(' Bearbeitung ablehnen ')));
					$time_text = "";
					$time_val = "";
				break;

				// status: GEPR�FT / ggf. VON EINEM ORGA NEU EINGETRAGEN
				case 2:
					$status	= t('Überprüft / Akzeptiert');
					array_push($status_wahl, optionrow(0, t(' Keine Änderung ')));
					array_push($status_wahl, optionrow(2, t(' Problem nicht übernehmen und zurückgeben ')));
					array_push($status_wahl, optionrow(3, t(' Problem übernehmen und Bearbeitung beginnen ')));
					array_push($status_wahl, optionrow(4, t(' Auf Erledigt setzen ')));
					array_push($status_wahl, optionrow(5, t(' Bearbeitung ablehnen ')));
					$time_text = t('Überprüft am/um');
					$time_val = $func->unixstamp2date($row["verified"], "daydatetime");
				break;

				// status: ORGA HAT ARBEIT BEGONNEN
				case 3:
					$status	= t('In Arbeit');
					array_push($status_wahl, optionrow(0, t(' Keine Änderung ')));
					array_push($status_wahl, optionrow(4, t(' Auf Erledigt setzen ')));
					$time_text = t('In Bearbeitung seit');
					$time_val = $func->unixstamp2date($row["process"], "daydatetime");
				break;

				// status: BEARBEITUNG ABGESCHLOSSEN
				case 4:
					$status	= t('Abgeschlossen');
					array_push($status_wahl, optionrow(0, t(' Keine Änderung ')));
					array_push($status_wahl, optionrow(3, t(' Problem übernehmen und Bearbeitung beginnen ')));
					$time_text = t('Beendet am/um');
					$time_val = $func->unixstamp2date($row["finished"],"daydatetime");
				break;

				// status: BEARBEITUNG ABGELEHNT
				case 5:
					$status	= t('Abgelehnt');
					array_push($status_wahl, optionrow(0, t(' Keine Änderung ')));
					$time_text = t('Bearbeitung abgelehnt am/um');
					$time_val = $func->unixstamp2date($row["finished"], "daydatetime");
				break;
			}
			$dsp->AddDoubleRow(t('Ticketstatus'), $status);
			if ($time_text and $time_val) $dsp->AddDoubleRow($time_text, $time_val);
			$dsp->AddDoubleRow(t('Bearbeitender Orga'), $get_targetuser["username"]);

			$dsp->SetForm("index.php?mod=troubleticket&action=change&step=3&ttid=$tt_id");

			$dsp->AddDropDownFieldRow("tticket_status",t('Status auswählen'), $status_wahl, $error["tticket_status"], 1);

			$dsp->AddTextAreaPlusRow("tticket_publictext", t('Kommentar für Benutzer'), $_POST['tticket_publictext'], $error["tticket_publictext"]);
			$dsp->AddTextAreaPlusRow("tticket_orgatext", t('Kommentar für Orgas'), $_POST['tticket_orgatext'], $error["tticket_orgatext"]);

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

		$func->confirmation(t('Das Troubleticket wurde erfolgreich geändert'), "index.php?mod=troubleticket&action=change");
	break;
}
?>