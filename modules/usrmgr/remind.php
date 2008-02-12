<?php

$step 	= $vars["step"];
$fcode 	= $vars["fcode"];
$pwr_mail 	= $vars["pwr_mail"];

$dsp->NewContent($lang['usrmgr']['remind_caption'], $lang['usrmgr']['remind_subcaption']);

if (!$cfg['sys_internet']) $func->information($lang['usrmgr']['remind_only_online'], "");

else switch ($step) {
	case 2: // Email prüfen, Freischaltecode generieren, Email senden
		$user_data = $db->query_first("SELECT username FROM {$config["tables"]["user"]} WHERE email = '$pwr_mail'");
		if ($user_data['username'] == "LS_SYSTEM"){
			$func->information($lang['usrmgr']['remind_err_sysacc'], "index.php?mod=usrmgr&action=pwrecover&step=1");
		} else if ($user_data['username']){
			$fcode="";
			for ($x=0; $x<=24; $x++) $fcode.=chr(mt_rand(65,90));

			$db->query("UPDATE {$config["tables"]["user"]} SET fcode='$fcode' WHERE email = '$pwr_mail'");

			$path = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], "index.php"));

			$mail->create_inet_mail($user_data['username'], $pwr_mail, $lang['usrmgr']['remind_mail_head'], str_replace("%USERNAME%", $user_data['username'], str_replace("%PATH%", "http://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}{$path}index.php?mod=usrmgr&action=pwrecover&step=3&fcode=$fcode", $lang['usrmgr']['remind_mail'])), $cfg['sys_party_mail']);

			$func->confirmation($lang['usrmgr']['remind_success'], "index.php");
		} else $func->information($lang['usrmgr']['remind_err_email'], "index.php?mod=usrmgr&action=pwrecover&step=1");
	break;

	case 3: // Freischaltecode prüfen, Passwort generieren
		$user_data = $db->query_first("SELECT fcode FROM {$config["tables"]["user"]} WHERE fcode = '$fcode'");
		if (($user_data['fcode']) && ($fcode != "")){
			$new_pwd = "";
			for ($x=0; $x<=8; $x++) $new_pwd .= chr(mt_rand(65,90));
		
			$db->query("UPDATE {$config["tables"]["user"]} SET password = '". md5($new_pwd) ."' WHERE fcode = '$fcode'");

			$func->confirmation($lang['usrmgr']['remind_pw_generated'] ." <b>$new_pwd</b>", "index.php");
		} else $func->error($lang['usrmgr']['remind_wrong_fcode'], "index.php?mod=usrmgr&action=pwrecover&step=1");
	break;

	default:
		$dsp->SetForm("index.php?mod=usrmgr&action=pwrecover&step=2");
		$dsp->AddSingleRow($lang['usrmgr']['remind_email_hint']);
		$dsp->AddTextFieldRow("pwr_mail", $lang['usrmgr']['remind_email'], $pwr_mail, $mail_error);
		$dsp->AddFormSubmitRow("send");
		$dsp->AddBackButton("index.php", "usrmgr/pwremind");
	break;
}

$dsp->AddContent();
?>
