<?php
$mail_total = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["mail_messages"]} WHERE ToUserID = '{$auth['userid']}' AND mail_status = 'delete'");
$mail_unread_total = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["mail_messages"]} WHERE ToUserID = '{$auth['userid']}' AND mail_status = 'delete' AND des_status = 'new'");

$templ['mail']['case']['info']['page_title'] = $lang["mail"]["del_trashcan"];
$templ['mail']['case']['info']['mail'] = str_replace("%TOTAL%", $mail_total["n"], str_replace("%UNREAD%", $mail_unread_total["n"], $lang["mail"]["del_confirm"]));

eval("\$templ['index']['info']['content'] .= \"". $func->gettemplate("mail_case_boxheader")."\";");

$mastersearch = new MasterSearch( $vars, "index.php?mod=mail&action=trash", "index.php?mod=mail&action=showmail&ref=trash&mailID=", "");
$mastersearch->LoadConfig("mail_trashcan", $lang["mail"]["del_ms_search"], "");
$mastersearch->Search();
$mastersearch->PrintResult();
$mastersearch->PrintForm();

$templ['index']['info']['content'] .= $mastersearch->GetReturn();
?>
