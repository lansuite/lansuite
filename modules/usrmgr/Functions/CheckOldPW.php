<?php

use LanSuite\PasswordHash;

/**
 * @param string $old_password
 * @return bool|string
 */
function CheckOldPW($old_password)
{
    global $db, $auth;

    $get_dbpwd = $db->qry_first("SELECT password FROM %prefix%user WHERE userid = %int%", $auth["userid"]);
    if (!PasswordHash::verify($old_password, $get_dbpwd["password"])) {
        return t('Passwort inkorrekt');
    }

    return false;
}
