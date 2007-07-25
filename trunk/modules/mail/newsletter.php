<?php

switch($_GET["step"]) {
	case 2:
		if ($_POST["subject"] == ""){
			$subject_error = $lang["mail"]["newsletter_err_nosubject"];
			$_GET["step"] = 1;
		}
		if ($_POST["text"] == ""){
			$text_error = $lang["mail"]["newsletter_err_notext"];
			$_GET["step"] = 1;
		}
		if (($_POST["toinet"] == "") && ($_POST["tosys"] == "")){
			$inet_error = $lang["mail"]["newsletter_err_noto"];
			$_GET["step"] = 1;
		}
	break;
}

switch($_GET["step"]) {
	default:
		$dsp->NewContent($lang["mail"]["newsletter_caption"], $lang["mail"]["newsletter_subcaption"]);
		$dsp->SetForm("index.php?mod=mail&action=newsletter&step=2");

		if ($_POST["onlynewsletter"] == "") $_POST["onlynewsletter"] = 1;
		if ($_POST["toinet"] == "") $_POST["toinet"] = 1;

		$dsp->AddFieldSetStart('Zielgruppen-Einschränkung');
		$dsp->AddCheckBoxRow("onlynewsletter", $lang["mail"]["newsletter_onlynewsletter"], $lang["mail"]["newsletter_onlynewsletter2"], "", 1, $_POST["onlynewsletter"]);
#		$dsp->AddCheckBoxRow("onlysignon", $lang["mail"]["newsletter_onlysignon"], $lang["mail"]["newsletter_onlysignon2"], "", 1, $_POST["onlysignon"]);

		$t_array = array();
		array_push($t_array, '<option $selected value="0">'. t('An alle Benutzer') .'</option>');
		array_push($t_array, '<option $selected value="-1">'. t('Zu keiner Party angemeldet') .'</option>');
    $row = $db->query("SELECT party_id, name FROM {$config['tables']['partys']}");
    while($res = $db->fetch_array($row)) array_push($t_array, '<option $selected value="'. $res['party_id'] .'">'. $res['name'] .'</option>');
    $db->free_result($row);
		$dsp->AddDropDownFieldRow("onlysignon", t('Nur Angemeldete an folgender Party'), $t_array, '');

		$t_array = array();
		array_push ($t_array, "<option $selected value=\"0\">{$lang['mail']['newsletter_onlypaid_all']}</option>");
		array_push ($t_array, "<option $selected value=\"1\">{$lang['mail']['newsletter_onlypaid2']}</option>");
		array_push ($t_array, "<option $selected value=\"2\">{$lang['mail']['newsletter_onlypaid_not']}</option>");
		$dsp->AddDropDownFieldRow("onlypaid", t('Nur Benutzer die zu oben ausgewählter Party bezahlt haben'), $t_array, '');

		$t_array = array();
		array_push ($t_array, "<option $selected value=\"0\">". t('An alle Benutzer') ."</option>");
		array_push ($t_array, "<option $selected value=\"1\">". t('Nur an Gäste') ."</option>");
		array_push ($t_array, "<option $selected value=\"2\">". t('Nur an Admins und Oprtatoren') ."</option>");
		array_push ($t_array, "<option $selected value=\"3\">". t('Nur an Oprtatoren') ."</option>");
		$dsp->AddDropDownFieldRow("type", t('Nur an folgende Benutzertypen'), $t_array, '');

		$t_array = array();
		array_push($t_array, '<option $selected value="0">'. t('An alle Gruppen') .'</option>');
		array_push($t_array, '<option $selected value="-1">'. t('Nur an Benutzer ohne Gruppe') .'</option>');
    $row = $db->query("SELECT group_id, group_name FROM {$config['tables']['party_usergroups']}");
    while($res = $db->fetch_array($row)) array_push($t_array, '<option $selected value="'. $res['group_id'] .'">'. $res['group_name'] .'</option>');
    $db->free_result($row);
		$dsp->AddDropDownFieldRow("group_id", t('Nur an folgende Gruppen'), $t_array, '');
		$dsp->AddFieldSetEnd();

		$dsp->AddFieldSetStart('An');
		$dsp->AddCheckBoxRow("toinet", t('E-Mail-Adresse'), t('An die bei der Anmeldung angegebene E-Mail-Adresse'), $inet_error, 1, $_POST["toinet"]);
		$dsp->AddCheckBoxRow("tosys", t('System-Mailbox'), t('An die System-Mailbox des Benutzers'), "", 1, $_POST["tosys"]);
		$dsp->AddFieldSetEnd();

		$dsp->AddFieldSetStart('Nachricht');
		$dsp->AddTextFieldRow("subject", $lang["mail"]["newsletter_subject"], $_POST["subject"], $subject_error);
		$dsp->AddTextAreaMailRow("text", $lang["mail"]["newsletter_text"], $_POST["text"], $text_error);
		$dsp->AddFieldSetEnd();

		$dsp->AddFormSubmitRow("send");
		$dsp->AddContent();
	break;

	case 2:
		$where = "u.username != 'LS_SYSTEM'";
		if ($_POST["onlynewsletter"]) $where .= ' AND u.newsletter = 1 ';

		if ($_POST['onlysignon'] == -1) $where .= ' AND p.party_id IS NULL';
		elseif ($_POST['onlysignon']) $where .= " AND p.party_id=". (int)$_POST['onlysignon'];

		if ($_POST["onlypaid"] == 1) $where .= " AND p.paid > 0";
		elseif ($_POST["onlypaid"] == 2) $where .= " AND p.paid = 0";

		if ($_POST["type"] == 1) $where .= " AND u.type = 1";
		elseif ($_POST["type"] == 2) $where .= " AND (u.type = 2 OR u.type = 3)";
		elseif ($_POST["type"] == 3) $where .= " AND u.type = 3";
		else $where .= " AND u.type > 0";

		if ($_POST['group_id'] == -1) $where .= ' AND u.group_id = 0';
		elseif ($_POST['group_id']) $where .= " AND u.group_id=". (int)$_POST['group_id'];

		$success = "";
		$fail = "";
		$users = $db->query("SELECT s.ip, u.*, p.*, c.name AS clan, c.url AS clanurl FROM {$config["tables"]["user"]} AS u
      LEFT JOIN {$config["tables"]["party_user"]} AS p ON p.user_id=u.userid
      LEFT JOIN {$config["tables"]["clan"]} AS c ON c.clanid=u.clanid
      LEFT JOIN {$config["tables"]["seat_seats"]} AS s ON s.userid=u.userid
      WHERE $where
      GROUP BY u.email");

		while ($user = $db->fetch_array($users)){
			$text = $__POST["text"];

			// Variablen ersetzen
			$text = str_replace("%USERNAME%", $user["username"], $text);
			$text = str_replace("%VORNAME%", $user["firstname"], $text);
			$text = str_replace("%NACHNAME%", $user["name"], $text);
			$text = str_replace("%EMAIL%", $user["email"], $text);
			$text = str_replace("%CLAN%", $user["clan"], $text);
			$text = str_replace("%CLANURL%", $user["clanurl"], $text);
			
			$text = str_replace("%PARTYNAME%", $party_data["name"], $text);
			$text = str_replace('%PARTYURL%', $cfg['sys_partyurl'], $text);
			$text = str_replace("%MAXGUESTS%", $party_data['max_guest'], $text);
			
			$text = str_replace("%WWCLID%", $user["wwclid"], $text);
			$text = str_replace("%WWCLCLANID%", $user["wwclclanid"], $text);
			$text = str_replace("%NGLID%", $user["nglid"], $text);
			$text = str_replace("%NGLCLANID%", $user["nglclanid"], $text);
			$text = str_replace("%IP%", $user["ip"], $text);

			($user["paid"]) ? $text = str_replace("%BEZAHLT%", $lang["sys"]["yes"], $text)
				: $text = str_replace("%BEZAHLT%", $lang["sys"]["no"], $text);

			($user["checkin"]) ? $text = str_replace("%EINGECHECKT%", $lang["sys"]["yes"], $text)
				: $text = str_replace("%EINGECHECKT%", $lang["sys"]["no"], $text);

			($user["party_id"]) ? $text = str_replace("%ANGEMELDET%", $lang["sys"]["yes"], $text)
				: $text = str_replace("%ANGEMELDET%", $lang["sys"]["no"], $text);

			// Mail senden
			if ($_POST["toinet"]) {
				if ($mail->create_inet_mail($user["firstname"] ." ". $user["name"], $user["email"], $_POST["subject"], $text, $cfg["sys_party_mail"])) $success .= $user["firstname"] ." ". $user["name"] ."[". $user["email"] ."]" . HTML_NEWLINE;
				else $fail .= $user["firstname"] ." ". $user["name"] ."[". $user["email"] ."]" . HTML_NEWLINE;
			}
			if ($_POST["tosys"]) $mail->create_sys_mail($user["userid"], $__POST["subject"], $text);
		}
		$db->free_result($users);

		if ($_POST["toinet"]) $inet_success = $lang["mail"]["newsletter_success"] .HTML_NEWLINE. $success .HTML_NEWLINE . HTML_NEWLINE . $lang["mail"]["newsletter_fail"] .HTML_NEWLINE. $fail . HTML_NEWLINE . HTML_NEWLINE;
		if ($_POST["tosys"]) $sys_success = $lang["mail"]["newsletter_system_success"];

		$func->confirmation($inet_success . $sys_success, "index.php?mod=mail&action=newsletter&step=1");
	break;
}

?>
