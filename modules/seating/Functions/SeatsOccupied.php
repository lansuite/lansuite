<?php

/**
 * Get number of occupied seats in block
 *
 * @param int $blockid
 * @return mixed
 */
function SeatsOccupied($blockid)
{
    global $database;

    $row = $database->queryWithOnlyFirstRow("SELECT COUNT(*) AS SeatsOccupied FROM %prefix%seat_seats WHERE blockid = ? AND status = 2", [$blockid]);
    return $row['SeatsOccupied'];
}
