<?php

/**
 * @param string $clanpw
 * @return bool
 * @throws Exception
 */
function CheckClanPW($clanpw)
{
    global $db;

    $clan = $db->qry_first("SELECT password FROM %prefix%clan WHERE clanid = %int%", $_GET['clanid']);
    return $clan['password'] && \LanSuite\PasswordHash::verify($clanpw, $clan['password']);
}
