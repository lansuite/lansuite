<?php

/**
 * @param int $max_guest
 * @return string
 */
function GetGuests($max_guest)
{
    global $db, $func, $line;

    $row = $db->qry_first('SELECT COUNT(*) AS anz FROM %prefix%party_user WHERE party_id = %int%', $line['party_id']);
    $row2 = $db->qry_first('SELECT COUNT(*) AS anz FROM %prefix%party_user WHERE paid > 0 AND party_id = %int%', $line['party_id']);
    return $func->CreateSignonBar($row['anz'], $row2['anz'], $max_guest);
}
