<?php

/**
 * @param int $userid
 * @return string
 */
function GetTournamentUserIcon($userid)
{
    global $database, $dsp;
    $user = $database->queryWithOnlyFirstRow("SELECT userid, username FROM %prefix%user WHERE userid = ?", [$userid]);
    if ($userid == 0) {
        return '-';
    } else {
        return $dsp->FetchUserIcon($user['userid'], $user['username']);
    }
}
