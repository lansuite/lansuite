<?php
$mail_send_total = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["mail_messages"]} WHERE FromUserID = '{$auth['userid']}' AND mail_status != 'disabled'");
$mail_read_total = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["mail_messages"]} WHERE FromUserID = '{$auth['userid']}' AND mail_status != 'disabled' AND src_status = 'read'");	

$dsp->NewContent($lang["mail"]["out_outbox"], str_replace("%TOTAL%", $mail_send_total["n"], str_replace("%READ%", $mail_read_total["n"], $lang["mail"]["out_hint"])));
$dsp->AddContent();

$mastersearch = new MasterSearch( $vars, "index.php?mod=mail&action=outbox", "index.php?mod=mail&action=showmail&ref=out&mailID=", "");
$mastersearch->LoadConfig("mail_outbox", $lang["mail"]["out_ms_search"], "");
$mastersearch->Search();
$mastersearch->PrintResult();
$mastersearch->PrintForm();

$templ['index']['info']['content'] .= $mastersearch->GetReturn();
?>
