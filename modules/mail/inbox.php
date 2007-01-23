<?php

$mail_new_total = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["mail_messages"]} WHERE ToUserID = '{$auth['userid']}' AND mail_status = 'active' AND rx_date = '0'");
$mail_total = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["mail_messages"]} WHERE ToUserID = '{$auth['userid']}' AND mail_status = 'active'");
$dsp->NewContent(t('Posteingang'), t('Sie haben %1 ungelesene Nachrichten (%2 Nachrichten insgesammt)', array($mail_new_total['n'], $mail_total['n'])));

// if logged out
if (!$auth['userid']) $dsp->AddSingleRow(t('Um Ihren Posteingang sehen zu können, müssen Sie sich zuerst einloggen').HTML_NEWLINE.
  t('Nutzen Sie das <a href="index.php?mod=mail&action=newmail">Kontaktformular</a> um Mails zu versenen. Dies ist auch im Ausgeloggten Zustand möglich'));
$dsp->AddContent();

// If logged in
if ($auth['userid']) {
  switch($_GET["step"]) {
  	// Delete Mail
  	case 99:
  		if($_GET["mailid"]) $mail->set_status_delete($_GET["mailid"]);
  		$_GET["STEP"] = "";
  	break;
  }

  include_once('modules/mastersearch2/class_mastersearch2.php');
  $ms2 = new mastersearch2();

  $ms2->query['from'] = "{$config["tables"]["mail_messages"]} AS m LEFT JOIN {$config["tables"]["user"]} AS u ON m.FromUserID = u.userid";
  $ms2->query['where'] = "m.toUserID = '{$auth['userid']}' AND m.mail_status = 'active'";
  $ms2->query['default_order_by'] = 'm.tx_date';
  $ms2->query['default_order_dir'] = 'DESC';

  $ms2->config['EntriesPerPage'] = 30;

  $ms2->AddTextSearchField('Mail', array('m.subject' => 'fulltext', 'm.msgbody' => 'fulltext'));
  $ms2->AddTextSearchField($lang['mail']['showmail_mail_from'], array('u.userid' => 'exact', 'u.username' => '1337', 'u.name' => 'like', 'u.firstname' => 'like'));

  $ms2->AddSelect('u.userid');
  $ms2->AddResultField($lang['mail']['newsletter_subject'], 'm.subject', '', 80);
  $ms2->AddResultField($lang['mail']['showmail_mail_from'], 'u.username', 'UserNameAndIcon');
  $ms2->AddResultField($lang['mail']['showmail_mail_send'], 'UNIX_TIMESTAMP(m.tx_date) AS tx_date', 'MS2GetDate');
  $ms2->AddResultField($lang['mail']['showmail_mail_read'], 'UNIX_TIMESTAMP(m.rx_date) AS rx_date', 'MS2GetDate');

  $ms2->AddIconField('details', 'index.php?mod=mail&action=showmail&ref=in&mailID=', $lang['ms2']['details']);
  $ms2->AddIconField('delete', 'index.php?mod=mail&action=inbox&step=99&mailid=', $lang['ms2']['delete']);

  $ms2->PrintSearch('index.php?mod=mail', 'm.mailid');
}
?>