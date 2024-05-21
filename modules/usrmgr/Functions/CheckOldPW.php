<?php

use LanSuite\PasswordHash;

/**
 * @param string $old_password
 */
function CheckOldPW($old_password): bool|string
{
    global $database, $auth;

    $get_dbpwd = $database->queryWithOnlyFirstRow("SELECT password FROM %prefix%user WHERE userid = ?", [$auth["userid"]]);
    if (!PasswordHash::verify($old_password, $get_dbpwd["password"])) {
        return t('Passwort inkorrekt');
    }

    return false;
}
