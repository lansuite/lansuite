<?php

/**
 * @return int
 */
function IsWWCLT()
{
    global $db, $party;

    if ($_GET['mod'] != 'tournament2') {
        return 0;
    }

    $row = $db->qry_first("SELECT 1 AS found FROM %prefix%tournament_tournaments WHERE wwcl_gameid > 0 AND party_id = %int%", $party->party_id);
    if ($row['found']) {
        return 1;
    } else {
        return 0;
    }
}
