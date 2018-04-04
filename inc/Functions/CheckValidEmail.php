<?php

/**
 * CheckValidEmail is an error callback function.
 *
 * @param string    $email
 * @return bool|mixed|string
 */
function CheckValidEmail($email)
{
    global $cfg, $db;

    if ($email == '') {
        return t('Bitte gib deine Email ein');

    } elseif (substr_count($email, '@') != 1) {
        return t('Die Adresse muss genau ein @-Zeichen enthalten');

    } else {
        list($userName, $hostName) = explode('@', $email);
        if (!preg_match("/^[a-z0-9\_\-\.\%]+$/i", $userName)) {
            return t('Diese Email ist ungültig (Falscher Benutzer-Teil)');
        }

        if (!preg_match("/^([a-z0-9]+[\-\.]{0,1})+\.[a-z]+$/i", $hostName)) {
            return t('Diese Email ist ungültig (Falscher Host-Teil)');
        }
        //  Compare first and second email entry. As only first entry is passed to this function, we have to get the second one from $_POST directly
        if (!isset($_POST['email2']) || $email != $_POST['email2']) {
            return t('E-Mail Addressen stimmen nicht überein. Bitte überprüfe deine Eingabe');
        }

        $TrashMailDomains = explode("\n", $cfg['mf_forbidden_trashmail_domains']);
        foreach ($TrashMailDomains as $key => $val) {
            $TrashMailDomains[$key] = trim($val);
        }

        if (in_array($hostName, $TrashMailDomains)) {
            return t('Die Mail-Domain %1 ist nicht erlaubt, da sie Anbieter von "Wegwerf-Mails" ist', $hostName);
        }
    }

    return false;
}