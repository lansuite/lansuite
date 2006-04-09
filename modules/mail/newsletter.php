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

		$dsp->AddCheckBoxRow("onlynewsletter", $lang["mail"]["newsletter_onlynewsletter"], $lang["mail"]["newsletter_onlynewsletter2"], "", 1, $_POST["onlynewsletter"]);
		$dsp->AddCheckBoxRow("onlysignon", $lang["mail"]["newsletter_onlysignon"], $lang["mail"]["newsletter_onlysignon2"], "", 1, $_POST["onlysignon"]);
		
		$t_array = array();
		array_push ($t_array, "<option $selected value=\"0\">{$lang['mail']['newsletter_onlypaid_all']}</option>");
		array_push ($t_array, "<option $selected value=\"1\">{$lang['mail']['newsletter_onlypaid2']}</option>");
		array_push ($t_array, "<option $selected value=\"2\">{$lang['mail']['newsletter_onlypaid_not']}</option>");
		$dsp->AddDropDownFieldRow("onlypaid", $lang["mail"]["newsletter_onlypaid"], $t_array, '');
		

		$dsp->AddHRuleRow();

		$dsp->AddCheckBoxRow("toinet", $lang["mail"]["newsletter_toinet"], $lang["mail"]["newsletter_toinet2"], $inet_error, 1, $_POST["toinet"]);
		$dsp->AddCheckBoxRow("tosys", $lang["mail"]["newsletter_tosys"], $lang["mail"]["newsletter_tosys2"], "", 1, $_POST["tosys"]);

		$dsp->AddHRuleRow();

		$dsp->AddDoubleRow("Variablen", "%NACHNAME%" . HTML_NEWLINE ."
			%VORNAME%" . HTML_NEWLINE ."
			%NICK%" . HTML_NEWLINE ."
			%EMAIL%" . HTML_NEWLINE ."
			%WWCLID%" . HTML_NEWLINE ."
			%WWCLCLANID%" . HTML_NEWLINE ."
			%NGLID%" . HTML_NEWLINE ."
			%NGLCLANID%" . HTML_NEWLINE ."
			%IP%" . HTML_NEWLINE ."
			%CLAN%" . HTML_NEWLINE ."
			%CLANURL%" . HTML_NEWLINE ."
			%BEZAHLT% ({$lang["mail"]["newsletter_yes_no"]})" . HTML_NEWLINE ."
			%EINGECHECKT% ({$lang["mail"]["newsletter_yes_no"]})" . HTML_NEWLINE ."
			%ANGEMELDET% ({$lang["mail"]["newsletter_yes_no"]})" . HTML_NEWLINE ."
			");

		$dsp->AddTextFieldRow("subject", $lang["mail"]["newsletter_subject"], $_POST["subject"], $subject_error);
		$dsp->AddTextAreaRow("text", $lang["mail"]["newsletter_text"], $_POST["text"], $text_error);

		$dsp->AddFormSubmitRow("send");
		$dsp->AddBackButton("install.php?mod=install", "install/db"); 
		$dsp->AddContent();
	break;

	case 2:
		$where = "(u.username != 'LS_SYSTEM')";
		if ($_POST["onlynewsletter"]) $where .= " AND (u.newsletter = 1) ";
		if ($_POST["onlysignon"]) $where .= " AND p.party_id={$party->party_id}";
		if ($_POST["onlypaid"] == 1) $where .= " AND p.party_id={$party->party_id} AND (p.paid = 1)";
		elseif ($_POST["onlypaid"] == 2) $where .= " AND p.party_id={$party->party_id} AND (p.paid = 0)";
		$where .= " AND (u.type > 0)";

		$success = "";
		$fail = "";
		$users = $db->query("SELECT * FROM {$config["tables"]["user"]} AS u
      LEFT JOIN {$config["tables"]["party_user"]} AS p ON p.user_id=u.userid
      WHERE $where
      GROUP BY u.email");

		while ($user = $db->fetch_array($users)){
			$text = $_POST["text"];

			// Variablen ersetzen
			$text = str_replace("%NACHNAME%", $user["name"], $text);
			$text = str_replace("%VORNAME%", $user["firstname"], $text);
			$text = str_replace("%NICK%", $user["username"], $text);
			$text = str_replace("%EMAIL%", $user["email"], $text);
			$text = str_replace("%WWCLID%", $user["wwclid"], $text);
			$text = str_replace("%WWCLCLANID%", $user["wwclclanid"], $text);
			$text = str_replace("%NGLID%", $user["nglid"], $text);
			$text = str_replace("%NGLCLANID%", $user["nglclanid"], $text);
			$text = str_replace("%IP%", $user["ipaddress"], $text);
			$text = str_replace("%CLAN%", $user["clan"], $text);
			$text = str_replace("%CLANURL%", $user["clanurl"], $text);

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
			if ($_POST["tosys"]) $mail->create_sys_mail($user["userid"], $_POST["subject"], $text);
		}
		$db->free_result($users);

		if ($_POST["toinet"]) $inet_success = $lang["mail"]["newsletter_success"] .HTML_NEWLINE. $success .HTML_NEWLINE . HTML_NEWLINE . $lang["mail"]["newsletter_fail"] .HTML_NEWLINE. $fail . HTML_NEWLINE . HTML_NEWLINE;
		if ($_POST["tosys"]) $sys_success = $lang["mail"]["newsletter_system_success"];

		$func->confirmation($inet_success . $sys_success, "index.php?mod=mail&action=newsletter&step=1");
	break;
}

?>
