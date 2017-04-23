<?php

$dsp->NewContent(t('Passwort vergessen'), t('Mit diesem Modul kannst du dir ein neues Passwort generieren lassen'));

if (!$cfg['sys_internet']) {
    $func->information(t('Diese Funktion ist nur im Internetmodus verfügbar'));
} else {
    switch ($_GET['step']) {
        case 2: // Email prüfen, Freischaltecode generieren, Email senden
            $user_data = $db->qry_first("SELECT username FROM %prefix%user WHERE email = %string%", $_POST['pwr_mail']);
            if ($user_data['username'] == "LS_SYSTEM") {
                $func->information(t('Für den System-Account darf kein neues Passwort generiert werden'), "index.php?mod=usrmgr&action=pwrecover&step=1");
            } elseif ($user_data['username']) {
                $fcode = '';
                for ($x=0; $x<=24; $x++) {
                    $fcode.=chr(mt_rand(65, 90));
                }

                $db->qry("UPDATE %prefix%user SET fcode='$fcode' WHERE email = %string%", $_POST['pwr_mail']);

                $path = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], "index.php"));

                include_once("modules/mail/class_mail.php");
                $mail = new mail();
                $mail->create_inet_mail($user_data['username'], $_POST['pwr_mail'], $cfg['usrmgr_pwrecovery_subject'], str_replace("%USERNAME%", $user_data['username'], str_replace("%PATH%", "http://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}{$path}index.php?mod=usrmgr&action=pwrecover&step=3&fcode=$fcode", $cfg['usrmgr_pwrecovery_text'])));

                $func->confirmation(t('Dir wurde nun eine Freischalte-URL an die angegebene Emailadresse gesendet. Mit dem Aufruf dieser URL wird dir neues Passwort generiert werden.'), "index.php");
            } else {
                $func->information(t('Die von dir eigegebene Email existiert nicht in der Datenbank'), "index.php?mod=usrmgr&action=pwrecover&step=1");
            }
            break;

        case 3: // Freischaltecode prüfen, Passwort generieren, Freischaltcode zurücksetzen
            $user_data = $db->qry_first("SELECT fcode FROM %prefix%user WHERE fcode = %string%", $_GET['fcode']);
            if (($user_data['fcode']) && ($_GET['fcode'] != '')) {
                $new_pwd = "";
                for ($x=0; $x<=8; $x++) {
                    $new_pwd .= chr(mt_rand(65, 90));
                }
        
                $db->qry("UPDATE %prefix%user SET password = %string%, fcode = '' WHERE fcode = %string%", md5($new_pwd), $_GET['fcode']);

                $func->confirmation(t('Das neue Kennwort wurde erfolgreich generiert.<br>Es lautet:') ."\"<b>$new_pwd</b>\"", "index.php");
            } else {
                $func->error(t('Der von dir übermittelte Freischaltecode ist inkorrekt! Es wurde kein neues Kennwort generiert. Bitte prüfe, ob du die URL komplett aus der Benachrichtigungs-Mail kopiert hast.'), "index.php?mod=usrmgr&action=pwrecover&step=1");
            }
            break;

        default:
            $dsp->SetForm("index.php?mod=usrmgr&action=pwrecover&step=2");
            $dsp->AddSingleRow(t('Bitte gib die Email-Adresse ein, mit der du dich am System angemeldet hast'));
            $dsp->AddTextFieldRow("pwr_mail", t('Deine Email'), $_POST['pwr_mail'], $mail_error);
            $dsp->AddFormSubmitRow(t('Abschicken'));
            $dsp->AddBackButton("index.php", "usrmgr/pwremind");
            break;
    }
}

$dsp->AddContent();
