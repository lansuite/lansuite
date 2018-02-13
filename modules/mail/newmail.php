<?php

$reply_message = '';

$dsp->NewContent(t('Neue Mail verfassen'), '');
$dsp->AddContent();

function SendOnlineMail()
{
    global $db, $func, $__POST, $auth;

    include_once("modules/mail/class_mail.php");
    $mail = new mail();

    if ($_POST['toUserID'] == -1) {
        $_SESSION['tmpmsgbody'] = $_POST['msgbody'];
        $_SESSION['tmpmsgsubject'] = $_POST['Subject'];
        $func->information(t("Bitte gib einen Empfänger für deine Mail an"), "index.php?mod=mail&action=".$_GET['action']."&step=2&replyto=".$_GET['replyto']."&back=1");

  // To additional recipients from cfg
    } elseif (substr($_POST['toUserID'], 1, 7) == '-mail-') {
        $to = substr($_POST['toUserID'], 8, strlen($_POST['toUserID']));
        $mail->create_inet_mail('', $to, $__POST['Subject'], $__POST['msgbody'], $_POST['SenderMail']);
        $func->confirmation('Die Mail wurde and '. $to .' versendet', '');
        unset($_SESSION['tmpmsgbody']);
        unset($_SESSION['tmpmsgsubject']);

  // System-Mail: Insert will be done, by MF
    } elseif ($auth['userid'] and $_POST['type'] == 0) {
        // Send Info-Mail to receiver
        if ($cfg['sys_internet']) {
            $row = $db->qry_first('SELECT u.username, u.email, u.lsmail_alert FROM %prefix%user AS u WHERE u.userid = %int%', $_POST['toUserID']);
            if ($row['lsmail_alert']) {
                $mail->create_inet_mail($row['username'], $row['email'], t('Benachrichtigung: Neue LS-Mail'), t('Du hast eine neue Lansuite-Mail erhalten. Diese Benachrichtigung kannst du im System unter "Meine Einstellungen" deaktivieren'));
            }
        }
        return true;

  // Inet-Mail
    } else {
        $row = $db->qry_first("SELECT name, firstname, email FROM %prefix%user WHERE userid = %int%", $_POST['toUserID']);
        if ($auth['userid']) {
            $row2 = $db->qry_first("SELECT email FROM %prefix%user WHERE userid = %int%", $auth['userid']);
            $_POST['SenderMail'] = $row2['email'];
        }

        $mail->create_inet_mail($row['firstname'].' '.$row['name'], $row['email'], $__POST['Subject'], $__POST['msgbody'], $_POST['SenderMail']);
        $func->confirmation('Die Mail wurde versendet', '');
        unset($_SESSION['tmpmsgbody']);
        unset($_SESSION['tmpmsgsubject']);
    
        return false;
    }
}

$mf = new masterform();

if ($_GET['userID']) {
    $_POST['toUserID'] = $_GET['userID'];
}
if ($_GET['replyto']) {
    $row = $db->qry_first("SELECT m.mailID, m.Subject, m.msgbody, UNIX_TIMESTAMP(m.tx_date) AS tx_date, u.username FROM %prefix%mail_messages AS m
    LEFT JOIN %prefix%user AS u ON m.fromUserID = u.userid
    WHERE m.mailID = %int%", $_GET['replyto']);
    $reply_message = $row[mailID];
    if (!$_POST['toUserID'] and $_GET['replyto']) {
        $_POST['Subject'] = 'WG: '.$row['Subject'];
    } elseif (substr($row['Subject'], 0, 4) == 'Re: ') {
        $_POST['Subject'] = $row['Subject'];
    } else {
        $_POST['Subject'] = 'Re: '.$row['Subject'];
    }
    $_POST['msgbody'] = '


-----Ursprüngliche Nachricht-----
Von: '. $row['username'] .' ('. $func->unixstamp2date($row['tx_date'], 'datetime') .' Uhr)
Betreff: '. $row['Subject'] .'

'.$row['msgbody'];
}
if ($_SESSION['tmpmsgbody'] and $_GET['back']) {
    $_POST['msgbody'] = $_SESSION['tmpmsgbody'];
}
if ($_SESSION['tmpmsgsubject'] and $_GET['back']) {
    $_POST['Subject'] = $_SESSION['tmpmsgsubject'];
}

$selections = array();
if ($cfg['sys_internet'] and $cfg['mail_additional_mails']) {
    $AdditionalMails = explode("\n", $cfg['mail_additional_mails']);
    $z = 0;
    $selections['-OptGroup-0'] = t('Info-Adressen');
    foreach ($AdditionalMails as $AdditionalMail) {
        if ($AdditionalMail) {
            $selections["1-mail-$AdditionalMail"] = $AdditionalMail;
            $z++;
        }
    }
}

$AdminFound = 0;
$UserFound = 0;
if ($auth['userid']) {
    $WhereMinType = 1;
} else {
    $WhereMinType = 2;
}
$res = $db->qry("SELECT type, userid, username, firstname, name FROM %prefix%user WHERE type >= %string% ORDER BY type DESC, username", $WhereMinType);
if (!$_POST['toUserID']) {
    $selections[-1] = "- Bitte wählen -";
}
while ($row = $db->fetch_array($res)) {
    if (!$AdminFound and $row['type'] > 1) {
        $selections['-OptGroup-1'] = t('Admins');
        $AdminFound = 1;
    }
    if (!$UserFound and $row['type'] <= 1) {
        $selections['-OptGroup-2'] = t('Benutzer');
        $UserFound = 1;
    }

    if ($auth['type'] >= 2 or !$cfg['sys_internet'] or $cfg['guestlist_shownames']) {
        $selections[$row['userid']] = $row['username'] .' ('. $row['firstname'] .' '. $row['name'] .')';
    } else {
        $selections[$row['userid']] = $row['username'];
    }
}
$db->free_result($res);

$mf->AddField(t('Empfänger'), 'toUserID', IS_SELECTION, $selections, FIELD_OPTIONAL);

if ($auth['userid']) {
    $selections = array();
    $selections[0] = t('Als System-Mail');
    if ($cfg['sys_internet']) {
        $selections[1] = t('An die Email-Adresse. Hinweis: Kein LS-Code möglich!');
    }
    $mf->AddField(t('Mail-Typ'), 'type', IS_SELECTION, $selections, FIELD_OPTIONAL);
} else {
    $mf->AddField('', 'captcha', IS_CAPTCHA);
    $mf->AddField(t('Absender E-Mail'), 'SenderMail', '', '', '', CheckValidEmail);
}

$mf->AddField(t('Betreff'), 'Subject');
$mf->AddField(t('Nachricht'), 'msgbody', '', LSCODE_BIG);

$mf->AddFix('mail_status', 'active');
$mf->AddFix('des_status', 'new');
$mf->AddFix('fromUserID', $auth['userid']);
$mf->AddFix('tx_date', 'NOW()');
$mf->SendButtonText = t('Mail abschicken');

$mf->CheckBeforeInserFunction = 'SendOnlineMail';
if ($mf->SendForm('index.php?mod=mail&action=newmail&reply_message', 'mail_messages', 'mailID', '')) {
    $url_parts = parse_url($profile_url);
    $reply_to = strrchr($url_parts['query'], 'replyto');
    if ($reply_to) {
        $reply_to_id = substr(strrchr($reply_to, '='), 1);
        $setreply = $db->qry("UPDATE %prefix%mail_messages SET des_status = 'reply' WHERE mailID = %int% ", $reply_to_id);
    }
}
