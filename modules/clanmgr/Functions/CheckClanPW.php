<?php

use LanSuite\PasswordHash;

/**
 * @param string $clanpw
 * @return bool
 */
function CheckClanPW($clanpw)
{
    global $database;

    $clan = $database->queryWithOnlyFirstRow("SELECT password FROM %prefix%clan WHERE clanid = ?", [$_GET['clanid']]);
    if ($clan['password'] and PasswordHash::verify($clanpw, $clan['password'])) {
        return true;
    }
    return false;
}
