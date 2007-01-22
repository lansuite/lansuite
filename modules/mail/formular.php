<?php

if ($auth['login']) $senderemail = $auth['email'];
	else $senderemail = $_POST["adress"];

$receiver = "magic@lanrena.de";
$headers .= 'From:' . $senderemail;

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
			if ($_POST["adress"] == ""){
			$adress_error = $lang["mail"]["formular_err_noadress"];
			$_GET["step"] = 1;
		}
  	if ($_POST['captcha'] == '' or $_COOKIE['image_auth_code'] != md5(strtoupper($_POST['captcha']))) {
			$captcha_error = t('Captcha falsch wiedergegeben');
			$_GET["step"] = 1;
		}
		break;
}

switch($_GET["step"]) {
	default:
		$dsp->NewContent($lang["mail"]["formular_caption"], $lang["mail"]["formular_subcaption"]);
		$dsp->SetForm("index.php?mod=mail&action=formular&step=2");

		$dsp->AddHRuleRow();

    if (!$auth['login']) $dsp->AddTextFieldRow('captcha', 'Captcha <img src="ext_scripts/captcha.php">', $_POST['captcha'], $captcha_error);
		$dsp->AddTextFieldRow("adress", $lang["mail"]["formular_adress"], $senderemail, $adress_error);
		$dsp->AddTextFieldRow("subject", $lang["mail"]["newsletter_subject"], $_POST["subject"], $subject_error);
		$dsp->AddTextAreaRow("text", $lang["mail"]["newsletter_text"], $_POST["text"], $text_error);

		$dsp->AddFormSubmitRow("send");
		$dsp->AddContent();
	break;

	case 2:

		$success = "";
		$fail = "";
		$text = $_POST["text"];
		
			// Mail senden			
		mail($receiver, $_POST['subject'], $text, $headers);

		$func->confirmation($inet_success, "index.php?mod=mail&action=formular&step=1");
	break;
}

?>
