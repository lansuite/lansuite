<?php

/**
 * @param string $clanpw
 */
function CheckClanPWUsrMgr($clanpw): bool|string
{
    global $db, $auth;

    $newClanSelect = $_POST['new_clan_select'] ?? 0;
    if (!$newClanSelect && $auth['type'] <= \LS_AUTH_TYPE_USER && $auth['clanid'] != $_POST['clan']) {
        $clan = $db->qry_first("SELECT password FROM %prefix%clan WHERE clanid = %int%", $_POST['clan']);
        if ($clan['password'] && $clan['password'] != md5($clanpw)) {
            return t('Passwort falsch!');
        }
    }
    return false;
}
