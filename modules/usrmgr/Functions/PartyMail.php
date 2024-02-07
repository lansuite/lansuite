<?php

/**
 * @return bool
 */
function PartyMail()
{
    global $usrmgr, $func, $mail, $auth;

    $partyObj = new \LanSuite\Module\Party\Party();
    $partyObj->WriteStatFiles();

    if ((array_key_exists('sendmail', $_POST) && $_POST['sendmail']) || $auth['type'] < \LS_AUTH_TYPE_ADMIN) {
        if ($usrmgr->SendSignonMail(1)) {
            $func->confirmation(t('Eine Bestätigung der Anmeldung wurde an deine E-Mail-Adresse gesendet.'), NO_LINK);
        } else {
            $func->error(t('Es ist ein Fehler beim Versand der Informations-E-Mail aufgetreten.'). $mail->error, NO_LINK);
        }
    }

    return true;
}
