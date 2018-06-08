<?php

/**
 * @param string $headline
 * @param int $akt_round
 * @return void
 */
function WriteRoundHeadline($headline, $akt_round)
{
    global $tournament, $dsp, $func, $map, $tfunc;

    $round_start = $func->unixstamp2date($tfunc->GetGameStart($tournament, $akt_round), "time");
    $round_end = $func->unixstamp2date($tfunc->GetGameEnd($tournament, $akt_round), "time");

    $dsp->AddSingleRow("<b>$headline ".t('Runde')." ". abs($akt_round) ."</b>"
        .HTML_NEWLINE. t('Zeit') .": ". $round_start ." - ". $round_end
        .HTML_NEWLINE. t('Map') .": ". $map[(abs(floor($akt_round)) % count($map))]);
}
