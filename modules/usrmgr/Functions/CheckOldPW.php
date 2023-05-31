<?php

/**
 * @param string $old_password
 */
function CheckOldPW($old_password): bool|string
{
    global $db, $auth;

    $get_dbpwd = $db->qry_first("SELECT password FROM %prefix%user WHERE userid = %int%", $auth["userid"]);
    if ($get_dbpwd["password"] != md5($old_password)) {
        return t('Passwort inkorrekt');
    }

    return false;
}
