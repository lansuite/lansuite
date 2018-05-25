<?php

/**
 * @param int $userid
 * @return string
 */
function GetTournamentUserIcon($userid)
{
    global $db,$dsp;
    $user = $db->qry_first("SELECT userid, username FROM %prefix%user WHERE userid = %int%", $userid);
    if ($userid == 0) {
        return '-';
    } else {
        return $dsp->FetchUserIcon($user['userid'], $user['username']);
    }
}
