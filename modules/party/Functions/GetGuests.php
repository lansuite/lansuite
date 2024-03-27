<?php

/**
 * @param int $max_guest
 * @return string
 */
function GetGuests($max_guest)
{
    global $database, $func, $line;

    $row = $database->queryWithOnlyFirstRow('SELECT COUNT(*) AS anz FROM %prefix%party_user WHERE party_id = ?', [$line['party_id']]);
    $row2 = $database->queryWithOnlyFirstRow('SELECT COUNT(*) AS anz FROM %prefix%party_user WHERE paid > 0 AND party_id = ?', [$line['party_id']]);
    return $func->CreateSignonBar($row['anz'], $row2['anz'], $max_guest);
}
