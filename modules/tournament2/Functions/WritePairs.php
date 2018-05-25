<?php

/**
 * @param string $bracket
 * @param int $max_pos
 * @return void
 */
function WritePairs($bracket, $max_pos)
{
    global $db, $tournamentid, $tfunc, $akt_round, $i, $game;

    WriteRoundHeadline("$bracket-Bracket - ", $akt_round);

    $i = 0;

    for ($akt_pos = 0; $akt_pos <= $max_pos-1; $akt_pos++) {
        $game = $db->qry_first("
          SELECT
            teams.name,
            teams.teamid,
            games.leaderid,
            games.gameid,
            games.score
          FROM %prefix%t2_games AS games
          LEFT JOIN %prefix%t2_teams AS teams ON
            (games.tournamentid = teams.tournamentid)
            AND (games.leaderid = teams.leaderid)
          WHERE
            (games.tournamentid = %int%)
            AND (games.group_nr = 0)
            AND (games.round = %string%)
            AND (games.position = %string%)
          GROUP BY games.gameid", $tournamentid, $akt_round, $akt_pos);

        // Set Playernames
        if ($game == 0) {
            $game['name'] = "<i>".t('Noch Unbekannt')."</i>";
        } elseif ($game['leaderid'] == 0) {
            $game['name'] = "<i><font color=\"#000088\">".t('Freilos')."</font></i>";
        } else {
            $game['name'] .= $tfunc->button_team_details($game['teamid'], $tournamentid);
        }

        WriteGame();
    }
}
