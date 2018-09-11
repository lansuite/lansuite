<?php

namespace LanSuite\Module\Tournament2;

class TournamentFunction
{

    /**
     * @var \LanSuite\Module\Mail\Mail
     */
    private $mail = null;

    /**
     * @var \LanSuite\Module\Seating\Seat2
     */
    private $seating = null;

    public function __construct(\LanSuite\Module\Mail\Mail $mail, \LanSuite\Module\Seating\Seat2 $seating)
    {
        $this->mail = $mail;
        $this->seating = $seating;
    }

    /**
     * Generates a string to output a memberlist of one team
     *
     * @param int $teamid
     * @return string
     */
    public function GetMemberList($teamid)
    {
        global $db, $dsp, $seat2;

        $member_liste = "";
        $team_memb = $db->qry("
          SELECT
            user.username,
            user.userid
          FROM %prefix%t2_teammembers AS teammember
          LEFT JOIN %prefix%user AS user ON teammember.userid = user.userid
          WHERE
            teammember.teamid = %int%", $teamid);
        while ($member = $db->fetch_array($team_memb)) {
            $member_liste .= $dsp->FetchUserIcon($member['userid'], $member['username']) . " (Platz: ". $seat2->SeatNameLink($member['userid'], '', '') .")" . HTML_NEWLINE;
        }
        $db->free_result($team_memb);

        if ($member_liste == "") {
            return "<i>".t('Keine')."</i>";
        } else {
            return $member_liste;
        }
    }

    /**
     * Get the number of teams in this tournament
     *
     * @param int $tid
     * @param string $mode
     * @param int $group
     * @return float|int
     */
    public function GetTeamAnz($tid, $mode, $group = 0)
    {
        global $db;

        if (($mode == "groups") && ($group == 0)) {
            $game = $db->qry("
              SELECT gameid
              FROM %prefix%t2_games
              WHERE
                (tournamentid = %int%)
                AND (group_nr > 0)
              GROUP BY group_nr", $tid);
            $team_anz = 2 * $db->num_rows($game);
            $db->free_result($game);
            return $team_anz;
        } else {
            if ($mode != "groups") {
                $group = 0;
            }
            if ($mode == "liga") {
                $group = 1;
            }

            // In liga-mode dye's do not count as team, in KO-modes they do
            ($mode == "liga" or $mode == "groups")? $add_where = "AND (leaderid != 0)" : $add_where = "";

            $games = $db->qry_first("
              SELECT
                COUNT(*) AS anz
              FROM %prefix%t2_games
              WHERE
                (tournamentid = %int%)
                AND (round = 0)
                AND (group_nr = %string%) %plain%
              GROUP BY round", $tid, $group, $add_where);
            return $games['anz'];
        }
    }

    /**
     * Returns the time, when the given round in this tournament starts
     *
     * @param array $tournament
     * @param int $round
     * @param int $group_nr
     * @return float|int
     */
    public function GetGameStart($tournament, $round, $group_nr = 0)
    {
        global $db;
        
        $break_duration = $tournament["break_duration"] * 60;
        $round_duration = $tournament["max_games"] * $tournament["game_duration"] * 60 + $break_duration;
        ($tournament['mode'] == "double")? $faktor = 2 : $faktor = 1;
        
        // If final games of a group-tournament add time for group-games
        if (($tournament["mode"] == "groups") and ($group_nr == 0)) {
            // Count numer of teams of the first group
            $get_team_anz = $db->qry_first("
              SELECT
                COUNT(*) AS anz
              FROM %prefix%t2_games
              WHERE
                (tournamentid = %int%)
                AND (round = 0)
                AND (group_nr = 1)
              GROUP BY group_nr", $tournament["tournamentid"]);
            $team_anz = $get_team_anz["anz"];
            
            $tournament["starttime"] += $round_duration * ($team_anz - 1) * $faktor;
        }
        
        if ($tournament["mode"] == 'single') {
            $time = $tournament["starttime"] + $round_duration * (abs($round));
        } else {
            if ($round > 0) {
                $time = $tournament["starttime"] + $round_duration * ($round - 0.5) * $faktor;
            } else {
                $time = $tournament["starttime"] + $round_duration * abs($round) * $faktor;
            }
        }
        
        $res = $db->qry('SELECT start, duration FROM %prefix%t2_breaks WHERE tournamentid = %int%', $tournament["tournamentid"]);
        while ($row = $db->fetch_array($res)) {
            if ($row['start'] > $tournament["starttime"] and $row['start'] < $time) {
                $time += $row['duration'] * 60;
            }
        }
        return $time;
    }

    /**
     * Returns the time, when the given round in this tournament ends
     *
     * @param array $tournament
     * @param int $round
     * @param int $group_nr
     * @return float|int
     */
    public function GetGameEnd($tournament, $round, $group_nr = 0)
    {
        global $db;
        
        $break_duration = $tournament["break_duration"] * 60;
        $round_duration = $tournament["max_games"] * $tournament["game_duration"] * 60 + $break_duration;
        ($tournament['mode'] == "double")? $faktor = 2 : $faktor = 1;
        
        // If final games of a group-tournament add time for group-games
        if (($tournament["mode"] == "groups") and ($group_nr == 0)) {
            // Count numer of teams of the first group
            $get_team_anz = $db->qry_first("
              SELECT
                COUNT(*) AS anz
              FROM %prefix%t2_games
              WHERE
                (tournamentid = %int%)
                AND (round = 0)
                AND (group_nr = 1)
              GROUP BY group_nr", $tournament["tournamentid"]);
            $team_anz = $get_team_anz["anz"];
            
            $tournament["starttime"] += $round_duration * ($team_anz - 1) * $faktor;
        }
        
        if ($tournament["mode"] == 'single') {
            $time = $tournament["starttime"] + $round_duration * (abs($round + 1)) - $break_duration;
        } else {
            if ($round > 0) {
                $time = $tournament["starttime"] + $round_duration * ($round + 1 - 0.5) * $faktor - $break_duration;
            } else {
                $time = $tournament["starttime"] + $round_duration * (abs($round) + 0.5) * $faktor  - $break_duration;
            }
        }
        
        $res = $db->qry('SELECT start, duration FROM %prefix%t2_breaks WHERE tournamentid = %int%', $tournament["tournamentid"]);
        while ($row = $db->fetch_array($res)) {
            if ($row['start'] > $tournament["starttime"] and $row['start'] < $time) {
                $time += $row['duration'] * 60;
            }
        }
        return $time;
    }

    /**
     * @param int $tournamentid
     * @param int $group_nr
     * @return \LanSuite\Module\Tournament2\RankingData
     */
    public function get_ranking($tournamentid, $group_nr = null)
    {
        global $db, $akt_round, $num, $cfg, $array_id;

        $ranking_data = new \LanSuite\Module\Tournament2\RankingData();

        $tournament = $db->qry_first("
          SELECT
            mode
          FROM %prefix%tournament_tournaments
          WHERE tournamentid = %int%", $tournamentid);

        $games = $db->qry("SELECT gameid FROM %prefix%t2_games WHERE (tournamentid = %int%) AND (round=0)", $tournamentid);
        $team_anz = $db->num_rows($games);
        $db->free_result($games);

        $akt_round = 0;
        $num = 1;
        $array_id = 0;


        // Je nach Modus ergibt sich ein anderes Ranking
        if ($tournament['mode'] == 'all') {
            $teams = $db->qry("
              SELECT
                teams.name,
                teams.teamid,
                teams.disqualified,
                games.leaderid,
                games.score,
                games.gameid
              FROM %prefix%t2_games AS games
              LEFT JOIN %prefix%t2_teams AS teams ON
                (games.tournamentid = teams.tournamentid)
                AND (games.leaderid = teams.leaderid)
              WHERE games.tournamentid = %int%
              ORDER BY
                teams.disqualified ASC,
                games.score DESC,
                games.position ASC", $tournamentid);
            while ($team = $db->fetch_array($teams)) {
                $array_id++;
                array_push($ranking_data->id, $array_id);
                array_push($ranking_data->tid, $team['teamid']);
                array_push($ranking_data->name, $team['name']);
                array_push($ranking_data->pos, $num++);
                array_push($ranking_data->disqualified, $team['disqualified']);
            }
            $db->free_result($teams);
        } elseif ($tournament['mode'] == 'single' or $tournament['mode'] == 'double'
        or ($tournament['mode'] == 'groups' and $group_nr == 0)) {
            // Array für Teams auslesen
            $teams = $db->qry("
              SELECT
                teams.teamid,
                teams.name,
                teams.disqualified,
                MAX(games.round) AS rounds
              FROM %prefix%t2_games AS games
              LEFT JOIN %prefix%t2_teams AS teams ON
                (teams.leaderid = games.leaderid)
                AND (teams.tournamentid = games.tournamentid)
              WHERE
                games.tournamentid = %int%
                AND games.group_nr = 0
                AND NOT ISNULL( teams.name )
              GROUP BY teams.teamid
              ORDER BY
                teams.disqualified ASC,
                rounds DESC,
                games.score DESC", $tournamentid);

            // Bei Doublemodus die ersten 2 Plätze auslesen und Array neu auslesen
            if ($tournament['mode'] == "double") {
                for ($i = 0; $i < 2; $i++) {
                    $team = $db->fetch_array($teams);
                    if ($team['teamid']) {
                        $array_id++;
                        array_push($ranking_data->id, $array_id);
                        array_push($ranking_data->tid, $team['teamid']);
                        array_push($ranking_data->name, $team['name']);
                        array_push($ranking_data->pos, $num++);
                        array_push($ranking_data->disqualified, $team['disqualified']);
                    }
                }
                $db->free_result($teams);

                // Teams auslesen und in Array schreiben
                $teams = $db->qry("
                  SELECT
                    teams.teamid,
                    teams.name,
                    teams.disqualified,
                    MIN(games.round) AS rounds
                  FROM %prefix%t2_games AS games
                  LEFT JOIN %prefix%t2_teams AS teams ON
                    (teams.leaderid = games.leaderid)
                    AND (teams.tournamentid = games.tournamentid)
                  WHERE
                    games.tournamentid = %int%
                    AND games.group_nr = 0
                  GROUP BY teams.teamid
                  ORDER BY
                    teams.disqualified ASC,
                    rounds ASC,
                    games.score DESC", $tournamentid);
            }

            while ($team = $db->fetch_array($teams)) {
                if ($team['teamid'] && !in_array($team['teamid'], $ranking_data->tid)) {
                    $array_id++;
                    array_push($ranking_data->id, $array_id);
                    array_push($ranking_data->tid, $team['teamid']);
                    array_push($ranking_data->name, $team['name']);
                    array_push($ranking_data->pos, $num++);
                    array_push($ranking_data->disqualified, $team['disqualified']);
                }
            }
            $db->free_result($teams);
        } elseif ($tournament['mode'] == 'liga'
        or ($tournament['mode'] == 'groups' and $group_nr > 0)) {
            if ($group_nr == '') {
                $group_nr = 1;
            }
                
                // Beteiligte Teams in Array einlesen
                $teams = $db->qry("
                  SELECT
                    teamid,
                    name,
                    disqualified
                  FROM %prefix%t2_teams
                  WHERE
                    (tournamentid = %int%)
                  GROUP BY teamid
                  ORDER BY teamid", $tournamentid);

            $i = 0;
            while ($team = $db->fetch_array($teams)) {
                $i++;
                array_push($ranking_data->pos, $i);
                array_push($ranking_data->tid, $team["teamid"]);
                array_push($ranking_data->name, $team['name']);
                array_push($ranking_data->disqualified, $team['disqualified']);
                array_push($ranking_data->reached_finales, 0);
                array_push($ranking_data->win, 0);
                array_push($ranking_data->score, 0);
                array_push($ranking_data->score_en, 0);
                array_push($ranking_data->games, 0);
            }

            $scores = $db->qry("
              SELECT
                teams1.teamid AS tid1,
                teams2.teamid AS tid2,
                games1.score AS s1,
                games2.score AS s2,
                games1.group_nr
              FROM %prefix%t2_games AS games1
              LEFT JOIN %prefix%t2_games AS games2 ON
                (games1.tournamentid = games2.tournamentid)
                AND (games1.round = games2.round)
                AND (games1.group_nr = games2.group_nr)
              LEFT JOIN %prefix%t2_teams AS teams1 ON
                (games1.leaderid = teams1.leaderid)
                AND (games1.tournamentid = teams1.tournamentid)
              LEFT JOIN %prefix%t2_teams AS teams2 ON
                (games2.leaderid = teams2.leaderid)
                AND (games2.tournamentid = teams2.tournamentid)
              WHERE
                (games1.tournamentid = %int%)
                AND ((games1.position + 1) = games2.position)
                AND ((games1.position / 2) = FLOOR(games1.position / 2))
                AND (
                  (games1.score != 0)
                  OR (games2.score != 0)
                  )
                AND games1.group_nr = %string%", $tournamentid, $group_nr);

            while ($score = $db->fetch_array($scores)) {
                if ($tournament['mode'] == "groups" and $group_nr == 0) {
                    $ranking_data->reached_finales[array_search($score['tid1'], $ranking_data->tid)] = 1;
                    $ranking_data->reached_finales[array_search($score['tid2'], $ranking_data->tid)] = 1;
                }
                $ranking_data->score[array_search($score['tid1'], $ranking_data->tid)] += $score['s1'];
                $ranking_data->score[array_search($score['tid2'], $ranking_data->tid)] += $score['s2'];
                $ranking_data->score_en[array_search($score['tid1'], $ranking_data->tid)] += $score['s2'];
                $ranking_data->score_en[array_search($score['tid2'], $ranking_data->tid)] += $score['s1'];

                $ranking_data->games[array_search($score['tid1'], $ranking_data->tid)] ++;
                $ranking_data->games[array_search($score['tid2'], $ranking_data->tid)] ++;

                if ($score['s1'] == $score['s2']) {
                    $ranking_data->win[array_search($score['tid1'], $ranking_data->tid)] += 1;
                    $ranking_data->win[array_search($score['tid2'], $ranking_data->tid)] += 1;
                } elseif ($score['s1'] > $score['s2']) {
                    $ranking_data->win[array_search($score['tid1'], $ranking_data->tid)] += $cfg["t_league_points"];
                } elseif ($score['s1'] < $score['s2']) {
                    $ranking_data->win[array_search($score['tid2'], $ranking_data->tid)] += $cfg["t_league_points"];
                }
            }
            $db->free_result($teams);

            $teams_array_tmp = $ranking_data->tid;
            $i = 0;
            while (array_shift($teams_array_tmp)) {
                array_push($ranking_data->score_dif, ($ranking_data->score[$i] - $ranking_data->score_en[$i]));
                $i++;
            }
            array_multisort(
                $ranking_data->disqualified,
                SORT_ASC,
                SORT_NUMERIC,
                $ranking_data->reached_finales,
                SORT_DESC,
                SORT_NUMERIC,
                $ranking_data->win,
                SORT_DESC,
                SORT_NUMERIC,
                $ranking_data->score_dif,
                SORT_DESC,
                SORT_NUMERIC,
                $ranking_data->score,
                SORT_DESC,
                SORT_NUMERIC,
                $ranking_data->score_en,
                SORT_ASC,
                SORT_NUMERIC,
                $ranking_data->tid,
                SORT_ASC,
                SORT_NUMERIC,
                $ranking_data->name,
                SORT_ASC,
                SORT_STRING,
                $ranking_data->games,
                SORT_ASC,
                SORT_NUMERIC
            );
        }

        return $ranking_data;
    }

    /**
     * @param int $teamid
     * @param int $tournamentid
     * @return string
     */
    public function button_team_details($teamid, $tournamentid)
    {
        global $auth;

        if ($teamid) {
            $link = " <a href=\"index.php?mod=tournament2&action=tdetails&tournamentid=$tournamentid&teamid=$teamid\"><img src=\"design/". $auth["design"] ."/images/arrows_search.gif\" width=\"12\" height=\"13\" border=\"0\"></a>";
            return $link;
        }
    }

    /**
     * Generate the next position in a KO-Tournament, if a score is submitted
     *
     * @param int $player1
     * @param int $player2
     * @return void
     */
    private function GenerateNewPosition($player1, $player2)
    {
        global $db, $round, $pos, $score, $tournamentid, $leaderid, $num_rounds, $team_anz;

        $team_round[$player1] = $round;
        $team_pos[$player1] = $pos[$player1];
        $team_round_before = $team_round[$player1];
        $team_pos_before = $team_pos[$player1];

        $team_pow_anz = $team_anz;
        for ($z = 0; $team_pow_anz > 1; $z++) {
            $team_pow_anz /= 2;
        }
        $team_pow_anz = pow(2, $z);

        $team_round_anz = $team_pow_anz;
        for ($z = 0; $z < abs($round); $z++) {
            $team_round_anz /= 2;
        }

        ($score[$player1] > $score[$player2]) ? $winner = 1
            : $winner = 0;

        ($score[$player1] < $score[$player2]) ? $looser = 1
            : $looser = 0;

        // Runden-Berechnung
        // Gewinnt jemand im Winner-Bracket, wird seine Runde um eins erhöht.
        if ($round >= 0 and $winner) {
            $team_round[$player1]++;

        // Gewinnt jemand im Loser-Bracket, oder verliert das allererste Spiel, so wird seiner Runde 0.5 abgezogen.
        } elseif (($round < 0 and $winner) or ($round == 0 and $looser)) {
            $team_round[$player1] -= 0.5;

        // Verliert jemand im Winner-Bracket, wird seine Runde mit -1 multipliziert.
        } elseif ($round > 0 and $looser) {
            $team_round[$player1] *= (-1);
        }

        // Gewinnt jemand das Loser-Bracket, so wird seine Runde mit -1 multipliziert und anschließend 0.5 addiert.
        if ($round == ($num_rounds * (-1) + 1)) {
            $team_round[$player1] = $team_round[$player1] * (-1) + 0.5;
        }

        // Positions-Berechnung
        // Die Position wird bei Siegern in ganzzahligen Runden und Verlieren der allerersten Runde halbiert
        if (($round == floor($round) and $winner) or ($looser and $round == 0)) {
            $team_pos[$player1] = floor($team_pos[$player1] / 2);
        }

        // Die Position wird bei Siegern in 0.5-Runde und beim Gewinner des LB jeweils bei geraden Zahlen um 1 erhöht
        if (($round != floor($round) and $winner) or ($round == ($num_rounds * (-1) + 1))) {
            $team_pos[$player1] = floor($team_pos[$player1] / 2) * 2 + 1;

        // Bei Verlierern im WB wird bei ungeraden Zahlen (in geraden Runden) 1 abgezogen und
        } elseif (($round > 0) and $looser and (floor($round / 2) == $round / 2)) {
            $team_pos[$player1] = floor($team_pos[$player1] / 2) * 2;

        // Bei Verlierern im WB wird bei ungeraden Zahlen (in ungeraden Runden) 1 abgezogen und das Ergebnis von der Teamanzahl dieser Runde - 2 abgezogen (zum Spiegeln des Baumes)
        } elseif (($round > 0) and $looser and (floor($round / 2) != $round / 2)) {
            $team_pos[$player1] = $team_round_anz - 2 - floor($team_pos[$player1] / 2) * 2;
        }

        // Wenn im LB, oder Finale verloren wurde -> ausgeschieden. Sonst neuer Eintrag
        if ($winner or ($looser and $round >= 0 and $round != $num_rounds)) {
            $db->qry("
              DELETE FROM %prefix%t2_games
              WHERE
                (tournamentid = %int%)
                AND (round = %string%)
                AND (position = %string%)
                AND (group_nr = 0)", $tournamentid, $team_round[$player1], $team_pos[$player1]);

            $db->qry("
              INSERT INTO %prefix%t2_games
              SET
                tournamentid = %int%,
                leaderid = %int%,
                round = %string%,
                position = %string%,
                score = 0", $tournamentid, $leaderid[$player1], $team_round[$player1], $team_pos[$player1]);
        }

        // Verliert jemand das Halb-Finale im SE, gibt es einen zusätzlichen Eintrag im Winnerbracket. (Spiel um Platz 3)
        if ($round == ($num_rounds - 2) and $looser) {
            $db->qry("
              DELETE FROM %prefix%t2_games
              WHERE
                (tournamentid = %int%)
                AND (round = %string%)
                AND (position = %string%)
                AND (group_nr = 0)", $tournamentid, ($team_round_before + 1), (floor($team_pos_before / 2) + 2));

            $db->qry("
              INSERT INTO %prefix%t2_games
              SET
                tournamentid = %int%,
                leaderid = %int%,
                round = %string%,
                position = %string%,
                score = 0", $tournamentid, $leaderid[$player1], ($team_round_before + 1), (floor($team_pos_before / 2) + 2));
        }

        // Freilose in Runde -0.5 und -1
        if ($team_round[$player1] == -0.5) {
            if ($team_pos[$player1] % 2 == 0) {
                $en_position = $team_pos[$player1] + 1;
            } else {
                $en_position = $team_pos[$player1] - 1;
            }

            // Daten des neuen Gegners auslesen
            $en_game = $db->qry_first("
              SELECT
                gameid
              FROM %prefix%t2_games
              WHERE
                (tournamentid = %int%)
                AND (position = %string%)
                AND (round = -0.5)
                AND (leaderid = 0)", $tournamentid, $en_position);

            // Wenn neuer Gegner ein Freilos, Spieler eine Runde weiter schieben
            if ($en_game['gameid'] != 0) {
                $db->qry("
                  DELETE FROM %prefix%t2_games
                  WHERE
                    (tournamentid = %int%)
                    AND (round = -1)
                    AND (position = %int%)
                    AND (group_nr = 0)", $tournamentid, (floor($team_pos[$player1]/2)*2 + 1));

                $db->qry("
                  INSERT INTO %prefix%t2_games
                  SET
                    tournamentid = %int%,
                    leaderid = %int%,
                    round = -1,
                    position = %int%,
                    score = 0", $tournamentid, $leaderid[$player1], (floor($team_pos[$player1]/2)*2 + 1));
            }
        }
        if ($team_round[$player1] == -1) {
            if ($team_pos[$player1] % 2 == 0) {
                $en_position = $team_pos[$player1] + 1;
            } else {
                $en_position = $team_pos[$player1] - 1;
            }

            // Daten des neuen Gegners auslesen
            $en_game = $db->qry_first("
              SELECT
                gameid
              FROM %prefix%t2_games
              WHERE
                (tournamentid = %int%)
                AND (position = %int%)
                AND (round = -1)
                AND (leaderid = 0)", $tournamentid, $en_position);

            // Wenn neuer Gegner ein Freilos, Spieler eine Runde weiter schieben
            if ($en_game['gameid'] != 0) {
                $db->qry("
                  DELETE FROM %prefix%t2_games
                  WHERE
                    (tournamentid = %int%)
                    AND (round = -1.5)
                    AND (position = %int%)
                    AND (group_nr = 0)", $tournamentid, (floor($team_pos[$player1]/2)));

                $db->qry("
                  INSERT INTO %prefix%t2_games
                  SET
                    tournamentid = %int%,
                    leaderid = %int%,
                    round = -1.5,
                    position = %int%,
                    score = 0", $tournamentid, $leaderid[$player1], (floor($team_pos[$player1]/2)));
            }
        }
    }

    /**
     * Sumbit Score $score1:$score2 in the tournament $tournamentid, for the game $gameid1 vs. $gameid2
     *
     * @param int $ttid
     * @param int $gameid1
     * @param int $gameid2
     * @param int $score1
     * @param int $score2
     * @param string $comment
     * @return void
     */
    public function SubmitResult($ttid, $gameid1, $gameid2, $score1, $score2, $comment)
    {
        global $db, $func, $tournamentid, $round, $pos, $score, $leaderid, $num_rounds, $team_anz;
        $tournamentid = $ttid;
        $score[1] = $score1;
        $score[2] = $score2;

        // Read data
        $tournament = $db->qry_first("SELECT name, mode FROM %prefix%tournament_tournaments WHERE tournamentid = %int%", $tournamentid);

        $gr_game = $db->qry_first("
          SELECT
            group_nr
          FROM %prefix%t2_games
          WHERE
            gameid= %int%", $gameid1);

        $team_anz = $this->GetTeamAnz($tournamentid, $tournament['mode'], $gr_game["group_nr"]);

        $team1 = $db->qry_first("
          SELECT
            games.position,
            games.leaderid,
            games.round
          FROM %prefix%t2_games AS games
          WHERE
            (games.tournamentid = %int%)
            AND (games.gameid = %int%)", $tournamentid, $gameid1);
        $round = $team1["round"];
        $pos[1] = $team1["position"];
        $leaderid1 = $team1["leaderid"];
        $leaderid[1] = $leaderid1;

        $team2 = $db->qry_first("
          SELECT
            games.position,
            games.leaderid
          FROM %prefix%t2_games AS games
          WHERE
            (games.tournamentid = %int%)
            AND (games.gameid = %int%)", $tournamentid, $gameid2);
        $pos[2] = $team2["position"];
        $leaderid2 = $team2["leaderid"];
        $leaderid[2] = $leaderid2;

        // Write Score for current game
        $db->qry("
          UPDATE %prefix%t2_games 
          SET
            score = %string%,
            comment = %string%
          WHERE
            gameid = %int%", $score1, $comment, $gameid1);

        $db->qry("
          UPDATE %prefix%t2_games 
          SET
            score = %string%
          WHERE
            gameid = %int%", $score2, $gameid2);
        $func->log_event(t('Das Ergebnis (%1 : %2) des Spieles #%3 vs. #%4 wurde eingetragen.', $score1, $score2, $gameid1, $gameid2), 1, t('Turnier Ergebnise'), $gameid1);

        // TODO Zusätzlich eine Mail an beide Teamleiter senden?

        // Groups + KO
        if ($tournament["mode"] == "groups") {
            $game = $db->qry("
              SELECT
                gameid
              FROM %prefix%t2_games
              WHERE
                tournamentid = %int%
                AND group_nr > 0
              GROUP BY group_nr", $tournamentid);
            $num_groups = $db->num_rows($game);
            $db->free_result($game);

            for ($akt_group = 1; $akt_group <= $num_groups; $akt_group++) {
                // Wenn letztes Ergebnis in einer Gruppe: Erste 2 Teams in den KO-Baum schreiben
                $unfinished_games = $db->qry_first("
                  SELECT
                    games1.gameid
                  FROM %prefix%t2_games AS games1
                  LEFT JOIN %prefix%t2_games AS games2 ON
                    (games1.round = games2.round)
                    AND (games1.group_nr = games2.group_nr)
                    AND (games1.tournamentid = games2.tournamentid)
                  WHERE
                    (games1.tournamentid = %int%)
                    AND ((games1.position + 1) = games2.position)
                    AND ((games1.position / 2) = FLOOR(games1.position / 2))
                    AND (games1.score = 0)
                    AND (games2.score = 0)
                    AND (games1.leaderid != 0)
                    AND (games2.leaderid != 0)
                    AND (games1.group_nr = %string%)", $tournamentid, $akt_group);

                if ($unfinished_games['gameid'] == "") {
                    $ranking_data = $this->get_ranking($tournamentid, $akt_group);

                    // IF not already written
                    $game_written = $db->qry_first("
                      SELECT
                        leaderid
                      FROM %prefix%t2_games
                      WHERE
                        (tournamentid = %int%)
                        AND (round = 0)
                        AND (position = (($akt_group - 1) * 2))
                        AND (group_nr = 0)", $tournamentid);

                    if ($game_written['leaderid'] == "") {
                        // Write Winner
                        $leader = $db->qry_first("
                          SELECT
                            leaderid
                          FROM %prefix%t2_teams
                          WHERE
                            teamid = %int%", $ranking_data->tid[0]);

                        $db->qry("
                          INSERT INTO %prefix%t2_games
                          SET
                            tournamentid = %int%,
                            leaderid = %int%,
                            round = 0,
                            position = ((%int% - 1) * 2),
                            group_nr = 0,
                            score = 0", $tournamentid, $leader['leaderid'], $akt_group);

                        // Write Semi-Winner
                        $leader = $db->qry_first("
                          SELECT
                            leaderid
                          FROM %prefix%t2_teams
                          WHERE
                            teamid = %int%", $ranking_data->tid[1]);

                        $db->qry("
                          INSERT INTO %prefix%t2_games
                          SET
                            tournamentid = %int%,
                            leaderid = %int%,
                            round = 0,
                            position = ((%int% - (%int% - 1)) * 2 - 1),
                            group_nr = 0,
                            score = 0", $tournamentid, $leader['leaderid'], $num_groups, $akt_group);
                    }
                }
            }
        }

        // League
        if ($tournament["mode"] == "liga") {
            // Wenn letztes Ergebnis: Turnierstatus auf "closed" setzen
            $unfinished_games = $db->qry_first("
              SELECT
                games1.gameid
              FROM %prefix%t2_games AS games1
              LEFT JOIN %prefix%t2_games AS games2 ON
                (games1.round = games2.round)
                AND (games1.group_nr = games2.group_nr)
                AND (games1.tournamentid = games2.tournamentid)
              WHERE
                (games1.tournamentid = %int%)
                AND ((games1.position + 1) = games2.position)
                AND ((games1.position / 2) = FLOOR(games1.position / 2))
                AND (games1.score = 0)
                AND (games2.score = 0)
                AND (games1.leaderid != 0)
                AND (games2.leaderid != 0)", $tournamentid);
            if ($unfinished_games['gameid'] == "") {
                $db->qry("UPDATE %prefix%tournament_tournaments SET status='closed' WHERE tournamentid = %int%", $tournamentid);
                $func->log_event(t('Das letzte Ergebnis im Turnier %1 wurde gemeldet. Das Turnier ist damit geschlossen worden.', $tournament["name"]), 1, t('Turnier Verwaltung'));
            }
        }

        // KO-Systems
        if (($tournament["mode"] == "single") or ($tournament["mode"] == "double")
            or (($tournament["mode"] == "groups") and ($gr_game["group_nr"] == 0))) {
            $num_rounds = 1;
            for ($z = $team_anz/2; $z > 1; $z/=2) {
                $num_rounds++;
            }

            // Find unfinished games in last round on SE games
            if (($tournament["mode"] == "single") and $round == ($num_rounds - 1)) {
                $unfinished_games = $db->qry_first("
                  SELECT
                    games1.gameid
                  FROM %prefix%t2_games AS games1
                  LEFT JOIN %prefix%t2_games AS games2 ON
                    (games1.round = games2.round)
                    AND (games1.tournamentid = games2.tournamentid)
                  WHERE
                    (games1.tournamentid = %int%)
                    AND ((games1.position + 1) = games2.position)
                    AND ((games1.position / 2) = FLOOR(games1.position / 2))
                    AND (games1.score = 0)
                    AND (games2.score = 0)
                    AND (games1.leaderid != 0)
                    AND (games2.leaderid != 0)
                    AND (games1.round = %int%)", $tournamentid, $round);
            }
      
            // Wenn Final-Ergebnis: Turnierstatus auf "closed" setzen
            if (($round == $num_rounds)
              or (($tournament["mode"] == "groups") and ($round == $num_rounds - 1))
              or (($tournament["mode"] == "single") and ($round == $num_rounds - 1) and ($unfinished_games['gameid'] == ""))) {
                $db->qry("
                  UPDATE %prefix%tournament_tournaments
                  SET
                    status='closed'
                  WHERE
                    tournamentid = %int%", $tournamentid);

                $func->log_event(t('Das letzte Ergebnis im Turnier %1 wurde gemeldet. Das Turnier ist damit geschlossen worden.', $tournament["name"]), 1, t('Turnier Verwaltung'));
            }

            $this->GenerateNewPosition(1, 2);
            $this->GenerateNewPosition(2, 1);
        }
    }

    /**
     * @param int $max_pos
     * @return void
     */
    private function CheckRound($max_pos)
    {
        global $akt_round, $tournament, $db, $tournamentid, $game, $first;

        $round_end = $this->GetGameEnd($tournament, $akt_round);

        if (time() > $round_end) {
            $first = 1;
            for ($akt_pos = 0; $akt_pos <= $max_pos-1; $akt_pos ++) {
                $game = $db->qry_first("
                  SELECT
                    games.score,
                    games.gameid,
                    teams.name,
                    teams.leaderid
                  FROM %prefix%t2_games AS games
                  LEFT JOIN %prefix%t2_teams AS teams ON
                    (teams.leaderid = games.leaderid)
                    AND (teams.tournamentid = games.tournamentid)
                  WHERE
                    (games.tournamentid = %int%)
                    AND (games.round = %string%)
                    AND (games.position = %string%)
                    AND (games.group_nr = 0)", $tournamentid, $akt_round, $akt_pos);
                $this->WriteResult();
            }
        }
    }

    /**
     * @return void
     */
    private function WriteResult()
    {
        global $game, $first, $score1, $gameid1, $name1, $leaderid1, $tournamentid, $func, $tournament, $mail, $cfg;

        if ($first) {
            $first = 0;
            $score1 = $game['score'];
            $score1 = $score1 + 0;
            $gameid1 = $game['gameid'];
            $name1 = $game['name'];
            $leaderid1 = $game['leaderid'];
        } else {
            $first = 1;
            $score2 = $game['score'];
            $score2 = $score2 + 0;
            $gameid2 = $game['gameid'];
            $name2 = $game['name'];
            $leaderid2 = $game['leaderid'];

            // If no result has been submitted, and both gameids are set and none of the teams is a bye (leaderid = 0)
            if (($score1 == 0) and ($score2 == 0) and ($gameid1 != "") and ($gameid2 != "") and ($leaderid1) and ($leaderid2)) {
                // Choose random winner and set score to default win
                if ($cfg["t_default_win"] == 0) {
                    $cfg["t_default_win"] = 2;
                }
                if (rand(0, 1) == 1) {
                    $score1 = $cfg["t_default_win"];
                    $score2 = 0;
                } else {
                    $score1 = 0;
                    $score2 = $cfg["t_default_win"];
                }

                $this->SubmitResult($tournamentid, $gameid1, $gameid2, $score1, $score2, t('Ergbnis wurde automatisch gelost, da die Zeit überschritten wurde'));

                // Log action and send mail
                $func->log_event(t('Das Ergebnis des Spieles %1 gegen %2 im Turnier %3 wurde automatisch gelost, da die Zeit überschritten wurde', $name1, $name2, $tournament['name']), 1, t('Turnier Ergebnise'));
                $mail->create_sys_mail(
                    $leaderid1,
                    t_no_html('Zeitüberschreitung im Turnier %1', $tournament['name']),
                    t_no_html('Das Ergebnis deines Spieles %1 gegen %2 im Turnier %5 wurde nicht rechtzeitig gemeldet. Um Verzögerungen im Turnier zu vermeiden haben die Organisatoren festgelegt, dass das Ergebnis in diesem Fall gelost werden soll. Das geloste Ergebnis ist: %1 %3 - %2 %4. Falls du denkst diese Entscheidung wurde zu Unrecht getroffen, melden dich bitte schnellstmöglich bei den Organisatoren.', $name1, $name2, $score1, $score2, $tournament['name'])
                );
                $mail->create_sys_mail(
                    $leaderid2,
                    t_no_html('Zeitüberschreitung im Turnier %1', $tournament['name']),
                    t_no_html('Das Ergebnis deines Spieles %1 gegen %2 im Turnier %5 wurde nicht rechtzeitig gemeldet. Um Verzögerungen im Turnier zu vermeiden haben die Organisatoren festgelegt, dass das Ergebnis in diesem Fall gelost werden soll. Das geloste Ergebnis ist: %1 %3 - %2 %4. Falls du denkst diese Entscheidung wurde zu Unrecht getroffen, melden dich bitte schnellstmöglich bei den Organisatoren.', $name1, $name2, $score1, $score2, $tournament['name'])
                );
            }
        }
    }

    /**
     * @param int $tournamentid
     * @return void
     */
    public function CheckTimeExceed($tournamentid)
    {
        global $team_anz, $akt_round, $tournament, $db, $game, $first;

        $tournament = $db->qry_first("
          SELECT
            mode,
            defwin_on_time_exceed,
            name,
            break_duration,
            max_games,
            game_duration,
            UNIX_TIMESTAMP(starttime) AS starttime,
            tournamentid
          FROM %prefix%tournament_tournaments
          WHERE
            tournamentid = %int%", $tournamentid);

        if ($tournament["defwin_on_time_exceed"] == "1") {
            $team_anz = $this->GetTeamAnz($tournamentid, $tournament['mode'], 0);    // Is 0 okay? Maybe group-number is needed

            switch ($tournament['mode']) {
                case "liga":
                case "groups":
                    $games = $db->qry("
                      SELECT
                        teams.name,
                        teams.teamid,
                        games.leaderid,
                        games.gameid,
                        games.score,
                        games.group_nr,
                        games.round,
                        games.position,
                        games.leaderid
                      FROM %prefix%t2_games AS games
                      LEFT JOIN %prefix%t2_teams AS teams ON
                        (games.tournamentid = teams.tournamentid)
                        AND (games.leaderid = teams.leaderid)
                      WHERE
                        (games.tournamentid = %int%)
                        AND (games.group_nr > 0)
                      GROUP BY games.gameid
                      ORDER BY
                        games.group_nr,
                        games.round,
                        games.position", $tournamentid);
                    $first = 1;
                    while ($game = $db->fetch_array($games)) {
                        $round_end = $this->GetGameEnd($tournament, $game['round']);
                        if (time() > $round_end) {
                            $this->WriteResult();
                        }
                    }
                    $db->free_result($games);
                    break;
            }

            switch ($tournament['mode']) {
                case "single":
                case "double":
                case "groups":
                    $akt_round = 0;
                    $this->CheckRound($team_anz['anz']);

                    $akt_round = 1;
                    if ($tournament['mode'] == "double") {
                        $limit_round = 2;
                    } else {
                        $limit_round = 4;
                    }
                    for ($z = $team_anz['anz']/2; $z >= $limit_round; $z/=2) {
                        $this->CheckRound($z);
                        if ($tournament['mode'] == "double") {
                            $akt_round*=-1;
                            $akt_round+=0.5;
                            $this->CheckRound($z);
                            $akt_round-=0.5;
                            $this->CheckRound($z);
                            $akt_round*=-1;
                        }
                        $akt_round++;
                    }
                    $this->CheckRound(2);
                    break;
            }
        }
    }
}
