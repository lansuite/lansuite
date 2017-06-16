<?php

use LanSuite\PasswordHash;

/**
 * @param string $clanpw
 * @return bool|string
 */
function CheckClanPWUsrMgr($clanpw)
{
    global $db, $auth;

    if (!$_POST['new_clan_select'] and $auth['type'] <= 1 and $auth['clanid'] != $_POST['clan']) {
        $clan = $db->qry_first("SELECT password FROM %prefix%clan WHERE clanid = %int%", $_POST['clan']);
        if ($clan['password'] and !PasswordHash::verify($clanpw, $clan['password'])) {
            return t('Passwort falsch!');
        }
    }
    return false;
}
