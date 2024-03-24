<?php

/**
 * @param string $name
 * @return string
 */
function GetTournamentName($name)
{
    global $line, $auth;

    $return = '';
    // Game Icon
    if ($line['icon'] and $line['icon'] != 'none' and file_exists("ext_inc/tournament_icons/{$line['icon']}")) {
        $return .= "<img src=\"ext_inc/tournament_icons/{$line['icon']}\" title=\"Icon\" border=\"0\" /> ";
    }
    // Name
    $return .= $name;
    // LGZ Icon
    if ($line['lgz_gamename']) {
        $return .= ' <img src="ext_inc/tournament_icons/leagues/lgz.png" title="LGZ Game" border="0" />';
    }
    // Over 18 Icon
    if ($line['over18']) {
        $return .= " <img src='design/".$auth["design"]."/images/fsk_18.gif' title=\"".t('cb_t_over18')."\" border=\"0\" />";
    }

    return $return;
}
