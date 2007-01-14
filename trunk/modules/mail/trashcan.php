<?php
$mail_total = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["mail_messages"]} WHERE ToUserID = '{$auth['userid']}' AND mail_status = 'delete'");
$mail_unread_total = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["mail_messages"]} WHERE ToUserID = '{$auth['userid']}' AND mail_status = 'delete' AND des_status = 'new'");


include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2();

$ms2->query['from'] = "{$config["tables"]["mail_messages"]} AS m LEFT JOIN {$config["tables"]["user"]} AS u ON m.FromUserID = u.userid";
$ms2->query['where'] = "m.toUserID = '{$auth['userid']}' AND m.mail_status = 'delete'";
$ms2->query['default_order_by'] = 'm.tx_date';
$ms2->query['default_order_dir'] = 'DESC';

$ms2->config['EntriesPerPage'] = 30;

$ms2->AddTextSearchField('Mail', array('m.subject' => 'fulltext', 'm.msgbody' => 'fulltext'));
$ms2->AddTextSearchField($lang['mail']['showmail_mail_from'], array('u.userid' => 'exact', 'u.username' => '1337', 'u.name' => 'like', 'u.firstname' => 'like'));

$ms2->AddSelect('u.userid');
$ms2->AddResultField($lang['mail']['newsletter_subject'], 'm.subject', '', 80);
$ms2->AddResultField($lang['mail']['showmail_mail_from'], 'u.username', 'UserNameAndIcon');
$ms2->AddResultField($lang['mail']['showmail_mail_send'], 'm.tx_date', 'MS2GetDate');
$ms2->AddResultField($lang['mail']['showmail_mail_read'], 'm.rx_date', 'MS2GetDate');

$ms2->AddIconField('details', 'index.php?mod=mail&action=showmail&ref=trash&mailID=', $lang['ms2']['details']);

$ms2->AddMultiSelectAction('Löschen', 'index.php?mod=mail&action=delete&mailid=', 1);

$ms2->PrintSearch('index.php?mod=mail&action=trash', 'm.mailid');
?>