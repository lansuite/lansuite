<?php
$mail_total = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["mail_messages"]} WHERE ToUserID = '{$auth['userid']}' AND mail_status = 'delete'");
$mail_unread_total = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["mail_messages"]} WHERE ToUserID = '{$auth['userid']}' AND mail_status = 'delete' AND des_status = 'new'");

function MailStatus ( $status ) {
 global $lang;
 if ( $status == "new" ) return $lang['mail']['unread'];
 if ( $status == "read" ) return $lang['mail']['read'];
 if ( $status == "reply" ) return $lang['mail']['answered']; 
}

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

$ms2->AddResultField($lang['mail']['newsletter_subject'], 'm.subject', '', 160);
$ms2->AddResultField($lang['mail']['showmail_mail_from'], 'u.username', 'UserNameAndIcon','',100);
$ms2->AddResultField('Status', 'm.des_status', 'MailStatus', '',80);
$ms2->AddResultField($lang['mail']['showmail_mail_send'], 'UNIX_TIMESTAMP(m.tx_date) AS tx_date', 'MS2GetDate','',70);
$ms2->AddResultField($lang['mail']['showmail_mail_read'], 'UNIX_TIMESTAMP(m.rx_date) AS rx_date', 'MS2GetDate','',60);

$ms2->AddIconField('details', 'index.php?mod=mail&action=showmail&ref=trash&mailID=', $lang['ms2']['details']);

$ms2->PrintSearch('index.php?mod=mail&action=trash', 'm.mailid');
?>