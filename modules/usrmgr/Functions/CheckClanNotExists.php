<?php

/**
 * @param string $ClanName
 * @return bool|string
 */
function CheckClanNotExists($ClanName)
{
    global $db;

    $clan = $db->qry_first("SELECT 1 AS found FROM %prefix%clan WHERE name = %string%", $ClanName);
    if ($clan['found']) {
        return t('Dieser Clan existiert bereits!') .HTML_NEWLINE. t(' Wenn du diesem beitreten möchten, wähle ihn oberhalb aus dem Dropdownmenü aus.');
    }

    if (preg_match("/([.^\"\'`´]+)/", $ClanName)) {
        return t('Du verwendest nicht zugelassene Sonderzeichen in deinem Clannamen.');
    }

    return false;
}
