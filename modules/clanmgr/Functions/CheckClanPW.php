<?php

/**
 * @param string $clanpw
 * @return bool
 */
function CheckClanPW($clanpw)
{
    global $db;

    $clan = $db->qry_first("SELECT password FROM %prefix%clan WHERE clanid = %int%", $_GET['clanid']);
    if ($clan['password'] and $clan['password'] == md5($clanpw)) {
        return true;
    }
    return false;
}
