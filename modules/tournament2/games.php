<?php

$tournamentid = (int)$_GET["tournamentid"];

$mail = new \LanSuite\Module\Mail\Mail();
$seat2 = new \LanSuite\Module\Seating\Seat2();

$tfunc = new \LanSuite\Module\Tournament2\TournamentFunction($mail, $seat2);

if (!$tournamentid) {
    $func->error(t('Du hast kein Turnier ausgewählt!'));
} else {
    switch ($_GET["step"]) {
        case 1:
            include_once('modules/tournament2/search.inc.php');
            break;
  
        default:
                // Check if roundtime has exceeded and set awaiting scores randomly
                $tfunc->CheckTimeExceed($tournamentid);
                $tournament = $db->qry_first("SELECT *, UNIX_TIMESTAMP(starttime) AS starttime FROM %prefix%tournament_tournaments WHERE tournamentid = %int%", $tournamentid);
  
            // Get Maparray
            $map = explode("\r\n", $tournament["mapcycle"]);
            if ($map[0] == "") {
                $map[0] = t('unbekannt');
            }
  
            // Check Errors
            if ($tournament["mode"] == "open") {
                  $func->error(t('Dieses Turnier wurde noch nicht generiert. Die Paarungen sind noch nicht bekannt.'), "index.php?mod=tournament2&action=games&step=1");
                  break;
            }
  
            // Set Modename
            if ($tournament['mode'] == "single") {
                  $modus = t('Single-Elimination');
            }
            if ($tournament['mode'] == "double") {
                  $modus = t('Double-Elimination');
            }
            if ($tournament['mode'] == "liga") {
                  $modus = t('Liga');
            }
            if ($tournament['mode'] == "groups") {
                  $modus = t('Gruppenspiele + KO');
            }
            if ($tournament['mode'] == "all") {
                  $modus = t('Alle in einem');
            }
  
            // Start Output
            $dsp->NewContent(t('Turnier %1 (%2) - Paarungen', $tournament['name'], $modus), t('Hier siehst du eine Liste aller Paarungen dieses Turniers'));
  
            switch ($tournament['mode']) {
                case 'all':
                      // Update score, if submitted
                    if ($_GET['step'] == 10) {
                        foreach ($_POST['team_score'] as $gameid => $team_score) {
                            if ($gameid) {
                                if (!is_numeric($team_score)) {
                                    $team_score_error[$gameid] = t('Bitte gib eine Zahl ein');
                                } else {
                                    $db->qry("
                                      UPDATE %prefix%t2_games
                                      SET
                                        score = %string%
                                      WHERE
                                        gameid = %int%", $team_score, $gameid);
                                }
                            }
                        }
                    }
  
                    // Finish tournament
                    if ($_GET['step'] == 11) {
                        $db->qry("UPDATE %prefix%tournament_tournaments SET status='closed' WHERE tournamentid = %int%", $tournamentid);
                        $func->confirmation(t('Turnier wurde beendet'), '');
                    }
                    // Unfinish tournament
                    if ($_GET['step'] == 12) {
                        $db->qry("UPDATE %prefix%tournament_tournaments SET status='process' WHERE tournamentid = %int%", $tournamentid);
                        $func->confirmation(t('Turnier wurde wiedereröffnet'), '');
                    }
        
                      // Show players and scores
                      $games = $db->qry("
                        SELECT
                          teams.name,
                          teams.teamid,
                          games.leaderid,
                          games.score,
                          games.gameid
                        FROM %prefix%t2_games AS games
                        LEFT JOIN %prefix%t2_teams AS teams ON
                          (games.tournamentid = teams.tournamentid)
                          AND (games.leaderid = teams.leaderid)
                        WHERE
                          games.tournamentid = %int%
                        ORDER BY games.position", $tournamentid);
                    $dsp->SetForm("index.php?mod=tournament2&action=games&step=10&tournamentid=$tournamentid");
                    while ($game = $db->fetch_array($games)) {
                        $dsp->AddTextFieldRow('team_score['. $game['gameid'] .']', $game['name'] .' '. $tfunc->button_team_details($game['teamid'], $tournamentid), $game['score'], $team_score_error[$game['gameid']]);
                    }
                    $db->free_result($games);
                    $dsp->AddFormSubmitRow(t('Speichern'));
                    if ($tournament['status'] == 'process') {
                        $dsp->AddDoubleRow('', $dsp->FetchSpanButton(t('Beenden'), "index.php?mod=tournament2&action=games&step=11&tournamentid=$tournamentid"));
                    } elseif ($tournament['status'] == 'closed') {
                        $dsp->AddDoubleRow('', $dsp->FetchSpanButton(t('Beenden rückgängig'), "index.php?mod=tournament2&action=games&step=12&tournamentid=$tournamentid"));
                    }
                    break;
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
                    $last_round = -1;
                    while ($game = $db->fetch_array($games)) {
                              // Write Round Headline
                        if ($last_round != $game['round']) {
                            ($tournament['mode'] == "groups")? $group_out = t('Gruppe')." {$game['group_nr']},"
                                : $group_out = '';
  
                            WriteRoundHeadline($group_out, $game['round']);
  
                            $spieler1 = "";
                            $i = 0;
                        }
                        $last_round = $game['round'];
  
                        // Set Playernames
                        if ($game['leaderid'] == 0) {
                            $game['name'] = "<i>".t('Spielfrei')."</i>";
                        } else {
                            $game['name'] .= $tfunc->button_team_details($game['teamid'], $tournamentid);
                        }
  
                        WriteGame();
                    }
                    $db->free_result($games);
                    break;
            }
            
            switch ($tournament['mode']) {
                case "single":
                case "double":
                case "groups":
                    // Get $team_anz
                    if ($tournament['mode'] == "groups") {
                        $games = $db->qry("
                          SELECT gameid
                          FROM %prefix%t2_games
                          WHERE
                            (tournamentid = %int%)
                            AND (group_nr > 0)
                          GROUP BY group_nr", $tournamentid);
                        $team_anz = 2 * $db->num_rows($games);
                        $db->free_result($games);
                    } else {
                        $games = $db->qry_first("
                          SELECT
                            COUNT(*) AS anz
                          FROM %prefix%t2_games
                          WHERE
                            (tournamentid = %int%)
                            AND (round = 0)
                            AND (group_nr = 0)
                          GROUP BY round", $tournamentid);
                        $team_anz = $games["anz"];
                    }
  
                    $akt_round = 0;
                    WritePairs("Winner", $team_anz);

                    $akt_round = 1;
                    if ($tournament['mode'] == "double") {
                        $limit_round = 2;
                    } else {
                        $limit_round = 4;
                    }
                    for ($z = $team_anz/2; $z >= $limit_round; $z/=2) {
                        WritePairs("Winner", $z);
                        if ($tournament['mode'] == "double") {
                            $akt_round*=-1;
                            $akt_round+=0.5;
                            WritePairs("Loser", $z);
                            $akt_round-=0.5;
                            WritePairs("Loser", $z);
                            $akt_round*=-1;
                        }
                        $akt_round++;
                    }

                    if ($tournament['mode'] == "single") {
                        WritePairs("Winner", 4);
                    } else {
                        WritePairs("Winner", 2);
                    }
  
                    // Write Winner
                    $akt_round++;
                    $dsp->AddSingleRow("<b>". t('Turnier-Sieger') ."</b>");
                    $game = $db->qry_first("
                      SELECT
                        teams.name,
                        teams.teamid
                      FROM %prefix%t2_games AS games
                      LEFT JOIN %prefix%t2_teams AS teams ON
                        (games.tournamentid = teams.tournamentid)
                        AND (games.leaderid = teams.leaderid)
                      WHERE
                        (games.tournamentid = %int%)
                        AND (games.round = %string%)
                        AND (games.position = 0)
                        AND (games.group_nr = 0)
                      GROUP BY games.gameid", $tournamentid, $akt_round);
                    if ($game == 0) {
                        $game['name'] = "<i>".t('Noch Unbekannt')."</i>";
                    } else {
                        $game['name'] .= $tfunc->button_team_details($game['teamid'], $tournamentid);
                    }
                    $dsp->AddDoubleRow(t('Sieger'), $game['name']);
                    break;
            }
            
            // Finalize Output
            if ($func->internal_referer) {
                $dsp->AddBackButton($func->internal_referer, "tournament2/games");
            } else {
                $dsp->AddBackButton("index.php?mod=tournament2&action=games&step=1", "tournament2/games");
            }
            break;
    }
}
