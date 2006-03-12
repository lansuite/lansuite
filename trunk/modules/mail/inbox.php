<?php

switch($_GET["step"]) {
	// Delete Mail
	case 99:
		if($_GET["mailid"]) $mail->set_status_delete($_GET["mailid"]);
		$_GET["STEP"] = "";
	break;
}

$mail->delete_old_messages($auth['userid']);

$mail_new_total = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["mail_messages"]} WHERE ToUserID = '{$auth['userid']}' AND mail_status = 'active' AND rx_date = '0'");
$mail_total = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["mail_messages"]} WHERE ToUserID = '{$auth['userid']}' AND mail_status = 'active'");	

$dsp->NewContent($lang["mail"]["in_inbox"], str_replace("%MAXMSG%", $cfg['mail_max_msg'] , str_replace("%UNREAD%", $mail_new_total["n"], str_replace("%TOTAL%", $mail_total["n"], $lang["mail"]["in_hint"]))));
$dsp->AddContent();

$mastersearch = new MasterSearch( $vars, "index.php?mod=mail&action=inbox", "index.php?mod=mail&action=showmail&ref=in&mailID=", "");
$mastersearch->LoadConfig("mail_inbox", $lang["mail"]["inbox_ms_search"], "" );
$mastersearch->Search();
$mastersearch->PrintResult();
$mastersearch->PrintForm();

$templ['index']['info']['content'] .= $mastersearch->GetReturn();
?>
