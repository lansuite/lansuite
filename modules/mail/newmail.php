<?php

$dsp->NewContent(t('Neue Mail verfassen'), '');
$dsp->AddContent();

function SendOnlineMail() {
  global $db, $config, $mail;

  // System-Mail: Insert will be done, by MF
  if ($_POST['fromUserID'] and $_POST['type'] == 0) return true;

  // Inet-Mail
  else {
    $row = $db->query_first("SELECT name, firstname, email FROM {$config['tables']['user']} WHERE userid = ". (int)$_POST['toUserID']);
    if ($_POST['fromUserID']) {
      $row2 = $db->query_first("SELECT email FROM {$config['tables']['user']} WHERE userid = ". (int)$_POST['fromUserID']);
      $_POST['SenderMail'] = $row2['email'];
    }

    $mail->create_inet_mail($row['firstname'].' '.$row['name'], $row['email'], $_POST['Subject'], $_POST['msgbody'], $_POST['SenderMail']);
    return false;
  }
}

include_once('inc/classes/class_masterform.php');
$mf = new masterform();

$selections = array();
$res = $db->query("SELECT userid, username FROM {$config['tables']['user']} WHERE type > 0");
while ($row = $db->fetch_array($res)) $selections[$row['userid']] = $row['username'];
$db->free_result($res);
$mf->AddField(t('Empfänger'), 'toUserID', IS_SELECTION, $selections, FIELD_OPTIONAL);

if ($auth['userid']) {
  $selections = array();
  $selections[0] = t('Als System-Mail');
  $selections[1] = t('An die Email-Adresse');
  $mf->AddField(t('Mail-Typ'), 'type', IS_SELECTION, $selections, FIELD_OPTIONAL);
} else {
  $mf->AddField('', 'captcha', IS_CAPTCHA);
	$mf->AddField(t('Absender'), 'SenderMail');
}

$mf->AddField(t('Betreff'), 'Subject');
$mf->AddField(t('Nachricht'), 'msgbody');

$mf->AddFix('mail_status', 'active');
$mf->AddFix('src_status', 'send');
$mf->AddFix('des_status', 'new');
$mf->AddFix('fromUserID', $auth['userid']);
$mf->AddFix('priority', 'normal');
$mf->AddFix('tx_date', time());

$mf->CheckBeforeInserFunction = 'SendOnlineMail';
if ($mf->SendForm('index.php?mod=mail&action=newmail', 'mail_messages', 'mailID', '')) {
}
?>