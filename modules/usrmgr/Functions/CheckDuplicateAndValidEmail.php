<?php

/**
 * CheckDuplicateAndValidEmail is a callback function to check a given email address for duplicates and
 * to perform generic validations via CheckValidEmail.
 *
 * @param string    $email
 * @return bool|mixed|string
 */
function CheckDuplicateAndValidEmail($email)
{
    global $cfg,$db;

    $email = trim($email);

    $useremail = null;
    if (isset($_GET['userid'])) {
        $userrow = $db->qry_first('SELECT email FROM %prefix%user WHERE userid=%int%', $_GET['userid']);
        $useremail = $userrow['email'];
    }

    if ($useremail === null || ($useremail!==null && $email != $useremail)) {
        // Compare first and second email entry.
        // As only first entry is passed to this function, we have to get the second one from $_POST directly
        if (!isset($_POST['email2']) || $email !== $_POST['email2']) {
            return t('E-Mail-Adressen stimmen nicht überein. Bitte überprüfe deine Eingabe');
        }
    
        // Check if we already have a user with that email address
        $row = $db->qry_first('SELECT * FROM %prefix%user WHERE email = %string%', $email);
        if ($row) {
            return t('Diese E-Mail-Adresse ist bereits in Verwendung. Bitte verwende die "Passwort zurücksetzen"-Funktion, um dein Passwort zurück zu setzen');
        }
    }

    // All checks succeeded; call global email validation routine
    return CheckValidEmail($email);
}
