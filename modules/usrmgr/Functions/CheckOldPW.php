<?php

/**
 * @param string $old_password
 * @return bool|string
 * @throws Exception
 */
function CheckOldPW($old_password)
{
    global $db, $auth;

    $get_dbpwd = $db->qry_first("SELECT password FROM %prefix%user WHERE userid = %int%", $auth["userid"]);
    if (!\LanSuite\PasswordHash::verify($old_password, $get_dbpwd["password"])) {
        return t('Passwort inkorrekt');
    }

    return false;
}
