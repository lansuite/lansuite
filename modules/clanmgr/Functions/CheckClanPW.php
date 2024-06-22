<?php

/**
 * @param string $clanpw
 * @return bool
 */
function CheckClanPW($clanpw)
{
    global $database;

    $clan = $database->queryWithOnlyFirstRow("SELECT password FROM %prefix%clan WHERE clanid = ?", [$_GET['clanid']]);
    if ($clan['password'] and $clan['password'] == md5($clanpw)) {
        return true;
    }
    return false;
}
