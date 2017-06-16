<?php

use LanSuite\PasswordHash;

/**
 * @param string $clanpw
 * @return bool
 */
function CheckClanPW($clanpw)
{
    global $db;

    $clan = $db->qry_first("SELECT password FROM %prefix%clan WHERE clanid = %int%", $_GET['clanid']);
    if ($clan['password'] and PasswordHash::verify($clanpw, $clan['password'])) {
        return true;
    }
    return false;
}
