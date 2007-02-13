<?php

$dsp->NewContent(t('Neue Mail verfassen'), '');
$dsp->AddContent();

function SendOnlineMail() {
  global $db, $config, $mail, $func, $__POST;

  // System-Mail: Insert will be done, by MF
  if ($_POST['fromUserID'] and $_POST['type'] == 0) return true;

  // Inet-Mail
  else {
    $row = $db->query_first("SELECT name, firstname, email FROM {$config['tables']['user']} WHERE userid = ". (int)$_POST['toUserID']);
    if ($_POST['fromUserID']) {
      $row2 = $db->query_first("SELECT email FROM {$config['tables']['user']} WHERE userid = ". (int)$_POST['fromUserID']);
      $_POST['SenderMail'] = $row2['email'];
    }

    $mail->create_inet_mail($row['firstname'].' '.$row['name'], $row['email'], $__POST['Subject'], $__POST['msgbody'], $_POST['SenderMail']);
    $func->confirmation('Die Mail wurde versendet', '');
    return false;
  }
}

include_once('inc/classes/class_masterform.php');
$mf = new masterform();

if ($_GET['userID']) $_POST['toUserID'] = $_GET['userID'];
if ($_GET['replyto']) {
  $row = $db->query_first("SELECT m.Subject, m.msgbody, UNIX_TIMESTAMP(m.tx_date) AS tx_date, u.username FROM {$config['tables']['mail_messages']} AS m
    LEFT JOIN {$config['tables']['user']} AS u ON m.fromUserID = u.userid
    WHERE m.mailID = ".(int)$_GET['replyto']);
  if (substr($row['Subject'], 0, 4) == 'Re: ') $_POST['Subject'] = $row['Subject'];
  else $_POST['Subject'] = 'Re: '.$row['Subject'];
  $_POST['msgbody'] = '


-----Ursprüngliche Nachricht-----
Von: '. $row['username'] .' ('. $func->unixstamp2date($row['tx_date'], 'datetime') .' Uhr)
Betreff: '. $row['Subject'] .'

'.$row['msgbody'];
}

if ($auth['userid']) $WhereMinType = 1;
else $WhereMinType = 2;
$selections = array();
$res = $db->query("SELECT type, userid, username, firstname, name FROM {$config['tables']['user']} WHERE type >= $WhereMinType ORDER BY type DESC, username");
while ($row = $db->fetch_array($res)) {
  if ($row['type'] > 1) $type = t('Admin');
  else $type = t('Benutzer');
  if ($auth['type'] >= 2 or !$cfg['sys_internet'] or $cfg['guestlist_shownames'])
    $selections[$row['userid']] = $type .': '. $row['username'] .' ('. $row['firstname'] .' '. $row['name'] .')';
  else $selections[$row['userid']] = $type .': '. $row['username'];
}
$db->free_result($res);
$mf->AddField(t('Empfänger'), 'toUserID', IS_SELECTION, $selections, FIELD_OPTIONAL);

if ($auth['userid']) {
  $selections = array();
  $selections[0] = t('Als System-Mail');
  $selections[1] = t('An die Email-Adresse. Hinweis: Kein LS-Code möglich!');
  $mf->AddField(t('Mail-Typ'), 'type', IS_SELECTION, $selections, FIELD_OPTIONAL);
} else {
  $mf->AddField('', 'captcha', IS_CAPTCHA);
	$mf->AddField(t('Absender'), 'SenderMail', '', '', '', CheckValidEmail);
}

$mf->AddField(t('Betreff'), 'Subject');
$mf->AddField(t('Nachricht'), 'msgbody', '', LSCODE_BIG);

$mf->AddFix('mail_status', 'active');
$mf->AddFix('src_status', 'send');
$mf->AddFix('des_status', 'new');
$mf->AddFix('fromUserID', $auth['userid']);
$mf->AddFix('priority', 'normal');
$mf->AddFix('tx_date', 'NOW()');

$mf->CheckBeforeInserFunction = 'SendOnlineMail';
if ($mf->SendForm('index.php?mod=mail&action=newmail', 'mail_messages', 'mailID', '')) {
}
?>