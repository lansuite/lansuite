<?php

/**
 * Get the number of available seats in block
 *
 * @param int $blockid
 * @return mixed
 */
function SeatsAvailable($blockid)
{
    global $db;

    $row = $db->qry_first("SELECT COUNT(*) AS SeatsAvailable FROM %prefix%seat_seats WHERE blockid=%int% AND status > 0 AND status < 7", $blockid);
    return $row['SeatsAvailable'];
}
