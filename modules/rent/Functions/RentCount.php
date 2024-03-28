<?php

/**
 * @param int $quantity
 * @return string
 */
function RentCount($quantity)
{
    global $line, $database;

    $row = $database->queryWithOnlyFirstRow('SELECT COUNT(*) AS back FROM %prefix%rentuser WHERE stuffid = ? AND back_orgaid > 0', [$line['stuffid']]);
    return ($line['rented'] - $row['back']) .' / '. $quantity;
}
