<?php

/**
 * @param int $quantity
 * @return string
 */
function RentCount($quantity)
{
    global $line, $db;

    $row = $db->qry_first('SELECT COUNT(*) AS back FROM %prefix%rentuser WHERE stuffid = %int% AND back_orgaid > 0', $line['stuffid']);
    return ($line['rented'] - $row['back']) .' / '. $quantity;
}
