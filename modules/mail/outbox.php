<?php
$mail_send_total = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["mail_messages"]} WHERE FromUserID = '{$auth['userid']}' AND mail_status != 'disabled'");
$mail_read_total = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["mail_messages"]} WHERE FromUserID = '{$auth['userid']}' AND mail_status != 'disabled' AND des_status = 'read'");

$dsp->NewContent($lang["mail"]["out_outbox"], str_replace("%TOTAL%", $mail_send_total["n"], str_replace("%READ%", $mail_read_total["n"], $lang["mail"]["out_hint"])));
$dsp->AddContent();


include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2();

$ms2->query['from'] = "{$config["tables"]["mail_messages"]} AS m LEFT JOIN {$config["tables"]["user"]} AS u ON m.ToUserID = u.userid";
$ms2->query['where'] = "m.FromUserID = '{$auth['userid']}' AND m.mail_status != 'disabled'";
$ms2->query['default_order_by'] = 'm.tx_date';
$ms2->query['default_order_dir'] = 'DESC';

$ms2->config['EntriesPerPage'] = 30;

$ms2->AddTextSearchField('Mail', array('m.subject' => 'fulltext', 'm.msgbody' => 'fulltext'));
$ms2->AddTextSearchField($lang['mail']['showmail_mail_to'], array('u.userid' => 'exact', 'u.username' => '1337', 'u.name' => 'like', 'u.firstname' => 'like'));

$ms2->AddSelect('u.userid');
$ms2->AddResultField($lang['mail']['newsletter_subject'], 'm.subject', '', 80);
$ms2->AddResultField($lang['mail']['showmail_mail_to'], 'u.username', 'UserNameAndIcon');
$ms2->AddResultField($lang['mail']['showmail_mail_send'], 'UNIX_TIMESTAMP(m.tx_date) AS tx_date', 'MS2GetDate');
$ms2->AddResultField($lang['mail']['showmail_mail_read'], 'UNIX_TIMESTAMP(m.rx_date) AS rx_date', 'MS2GetDate');

$ms2->AddIconField('details', 'index.php?mod=mail&action=showmail&ref=out&mailID=', $lang['ms2']['details']);

$ms2->PrintSearch('index.php?mod=mail&action=outbox', 'm.mailid');
?>