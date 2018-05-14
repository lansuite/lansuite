<?php

/**
 * @return bool
 */
function SendOnlineMail()
{
    global $db, $cfg, $func, $__POST, $auth;

    $mail = new \LanSuite\Module\Mail\Mail();

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
