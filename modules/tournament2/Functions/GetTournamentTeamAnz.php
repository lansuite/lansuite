<?php

/**
 * @param int $maxteams
 * @return string
 */
function GetTournamentTeamAnz($maxteams)
{
    global $line;
    return $line['teamanz'] .'/'. $maxteams;
}
