<?php
$mail_send_total = $db->qry_first("SELECT count(*) as n FROM %prefix%mail_messages WHERE FromUserID = %int% AND mail_status != 'disabled'", $auth['userid']);
$mail_read_total = $db->qry_first("SELECT count(*) as n FROM %prefix%mail_messages WHERE FromUserID = %int% AND mail_status != 'disabled' AND des_status = 'read'", $auth['userid']);

$dsp->NewContent(t('Postausgang'), t('Sie haben <b>%1</b> Mail(s) versendet. Davon wurde(n) <b>%2</b> gelesen.',$mail_send_total["n"],$mail_read_total["n"]));
$dsp->AddContent();

include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2();

$ms2->query['from'] = "{$config["tables"]["mail_messages"]} AS m LEFT JOIN {$config["tables"]["user"]} AS u ON m.ToUserID = u.userid";
$ms2->query['where'] = "m.FromUserID = '{$auth['userid']}' AND m.mail_status != 'disabled'";
$ms2->query['default_order_by'] = 'm.tx_date';
$ms2->query['default_order_dir'] = 'DESC';

$ms2->config['EntriesPerPage'] = 30;

$ms2->AddTextSearchField('Mail', array('m.subject' => 'fulltext', 'm.msgbody' => 'fulltext'));
$ms2->AddTextSearchField(t('Empfänger'), array('u.userid' => 'exact', 'u.username' => '1337', 'u.name' => 'like', 'u.firstname' => 'like'));

$ms2->AddSelect('u.userid');
$ms2->AddResultField(t('Betreff'), 'm.subject', '', 160);
$ms2->AddResultField(t('Empfänger'), 'u.username', 'UserNameAndIcon','',100);
$ms2->AddResultField(t('Gesendet'), 'UNIX_TIMESTAMP(m.tx_date) AS tx_date', 'MS2GetDate','',75);
$ms2->AddResultField(t('Gelesen'), 'UNIX_TIMESTAMP(m.rx_date) AS rx_date', 'MS2GetDate','',45);

$ms2->AddIconField('details', 'index.php?mod=mail&action=showmail&ref=out&mailID=', t('Details'));

$ms2->PrintSearch('index.php?mod=mail&action=outbox', 'm.mailid');
?>