<?php

/**
 * Get the number of available seats in block
 *
 * @param int $blockid
 * @return mixed
 */
function SeatsAvailable($blockid)
{
    global $database;

    $row = $database->queryWithOnlyFirstRow("SELECT COUNT(*) AS SeatsAvailable FROM %prefix%seat_seats WHERE blockid = ? AND status > 0 AND status < 7", [$blockid]);
    return $row['SeatsAvailable'];
}
