<?php

/**
 * @param string $old_password
 */
function CheckOldPW($old_password): bool|string
{
    global $db, $database, $auth;

    $get_dbpwd = $database->queryWithOnlyFirstRow("SELECT password FROM %prefix%user WHERE userid = ?", [$auth["userid"]]);
    if ($get_dbpwd["password"] != md5($old_password)) {
        return t('Passwort inkorrekt');
    }

    return false;
}
