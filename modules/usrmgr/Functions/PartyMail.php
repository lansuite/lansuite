<?php

/**
 * @return bool
 */
function PartyMail()
{
    global $usrmgr, $func, $mail, $auth;

    $usrmgr->WriteXMLStatFile();

    if ($_POST['sendmail'] or $auth['type'] < 2) {
        if ($usrmgr->SendSignonMail(1)) {
            $func->confirmation(t('Eine Bestätigung der Anmeldung wurde an deine E-Mail-Adresse gesendet.'), NO_LINK);
        } else {
            $func->error(t('Es ist ein Fehler beim Versand der Informations-E-Mail aufgetreten.'). $mail->error, NO_LINK);
        }
    }

    return true;
}
