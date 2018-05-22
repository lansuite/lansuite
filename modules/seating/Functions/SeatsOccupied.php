<?php

/**
 * Get number of occupied seats in block
 *
 * @param int $blockid
 * @return mixed
 */
function SeatsOccupied($blockid)
{
    global $db;

    $row = $db->qry_first("SELECT COUNT(*) AS SeatsOccupied FROM %prefix%seat_seats WHERE blockid=%int% AND status = 2", $blockid);
    return $row['SeatsOccupied'];
}
