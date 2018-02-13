<?php

$gd = new gd();

include_once("modules/tournament2/class_tournament.php");
$tfunc = new tfunc;

$qacc        = $_GET["qacc"];
$tournamentid    = $_GET["tournamentid"];
$gameid1        = $_GET["gameid1"];
$gameid2        = $_GET["gameid2"];
$score_team1    = $_POST["score_team1"];
$score_team2    = $_POST["score_team2"];
$score_comment    = $_POST["score_comment"];

## Ueberschreibungsabfrage
if ($_GET["qacc"] == 1) {
    $score_team1 = $_GET["score_team1"];
    $score_team2 = $_GET["score_team2"];
    $score_comment = $_GET["score_comment"];
}


########## Infos holen
$tournament = $db->qry_first("SELECT name, teamplayer, over18, status, mode, mapcycle, UNIX_TIMESTAMP(starttime) AS starttime, max_games, game_duration, break_duration, tournamentid FROM %prefix%tournament_tournaments WHERE tournamentid = %int%", $tournamentid);
$map = explode("\n", $tournament["mapcycle"]);
if ($map[0] == "") {
    $map[0] = t('unbekannt');
}

$games = $db->qry_first("SELECT COUNT(*) AS anz FROM %prefix%t2_games WHERE (tournamentid = %int%) AND (round=0) GROUP BY round", $tournamentid);
$team_anz = $games["anz"];

$team1 = $db->qry_first("SELECT games.group_nr, games.round, games.position, games.score, games.comment, games.server_id, teams.name, teams.teamid, teams.disqualified, user.userid, user.username
  FROM %prefix%t2_games AS games
  LEFT JOIN %prefix%t2_teams AS teams ON games.leaderid = teams.leaderid
  LEFT JOIN %prefix%user AS user ON user.userid = teams.leaderid
  WHERE (teams.tournamentid = %int%) AND (games.gameid = %int%)
  ", $tournamentid, $gameid1);

$team2 = $db->qry_first("SELECT games.round, games.position, games.score, games.comment, games.server_id, teams.name, teams.teamid, teams.disqualified, user.userid, user.username
  FROM %prefix%t2_games AS games
  LEFT JOIN %prefix%t2_teams AS teams ON games.leaderid = teams.leaderid
  LEFT JOIN %prefix%user AS user ON user.userid = teams.leaderid
  WHERE (teams.tournamentid = %int%) AND (games.gameid = %int%)
  ", $tournamentid, $gameid2);


########## Einschränkungen prüfen
if ($tournament["name"] == "") {
    $func->error(t('Du musst zuerst ein Turnier auswählen!'), "index.php?mod=tournament2&action=details&tournamentid=$tournamentid");
} elseif (abs($team1['position'] - $team2['position']) != 1) {
    $func->error(t('Diese Spielkonstellation existiert nicht!'. $team1['position']. $team2['position']));

########## Keine Einschränkungen gefunden
} else {
    switch ($_GET["step"]) {
        default:
            include_once("modules/seating/class_seat.php");
            $seat2 = new seat2();

            unset($_SESSION['tournament_submit_result_blocker']);
            
            if ($func->isModActive('server')) {
                //Server auslesen
                $selections = array();
                $selections['0'] = t('Kein Server zugewiesen');
                $res = $db->qry("SELECT * FROM %prefix%server WHERE party_id = %int%", $party->party_id);
                while ($row = $db->fetch_array($res)) {
                    $selections[$row['serverid']] = $row['caption'];
                }
                $db->free_result($res);
            }
      
            $dsp->NewContent(t('Details der Partie %1 vs %2', $team1['name'], $team2['name']), t('Hier siehst du Details zu dieser Partie und kannst das Ergebnis eintragen.'));
            // Write Start and Enddate for each round
            $round_start = $tfunc->GetGameStart($tournament, $team1['round'], $team1['group_nr']);
            $round_end = $tfunc->GetGameEnd($tournament, $team1['round'], $team1['group_nr']);
            $dsp->AddDoubleRow(t('Spielzeit'), $func->unixstamp2date($round_start, "datetime") ." - ". $func->unixstamp2date($round_end, "datetime"));
            $dsp->AddDoubleRow(t('Map'), $map[(abs(floor($team1['round'])) % count($map))]);
            if ($func->isModActive('server')) {
                $dsp->AddDoubleRow(t('Server'), '<a href="index.php?mod=server&action=show_details&serverid='.$team1['server_id'].'">'.$selections[$team1['server_id']].'</a>');
            }
            
            if ($func->isModActive('server') and $auth['type'] >= 2) {
                $mf = new masterform();
                $mf->AddField(t('Server Zuweisen'), 'server_id', IS_SELECTION, $selections, FIELD_OPTIONAL);
                if ($mf->SendForm("index.php?mod=tournament2&action=submit_result&step=1&tournamentid=".$tournamentid."&gameid1=".$gameid1."&gameid2=".$gameid2, 't2_games', 'gameid', $gameid1)) {
                    $db->qry("UPDATE %prefix%t2_games SET server_id = %int% WHERE gameid = %int%", $_POST['server_id'], $gameid2);
                }
            }

            $dsp->AddHRuleRow();
            $dsp->AddSingleRow("<b>".t('Ergebnis melden')."</b>");
            $dsp->SetForm("index.php?mod=tournament2&action=submit_result&step=2&tournamentid=$tournamentid&gameid1=$gameid1&gameid2=$gameid2", '', '', 'multipart/form-data');

            // Write Team 1
            $disqualify_link = "";
/*  // Disquallifiy droped, due to errors
            if ($auth["type"] > 1 and $tournament['status'] == "process") {
                if ($team1['disqualified']) $disqualify_link = "<font color=\"#ff0000\">".t('Disqualifiziert')."</font> ". $dsp->FetchSpanButton(t('Disqualifizieren rückgängig'), "index.php?mod=tournament2&action=disqualify&teamid={$team1['teamid']}&step=10");
                else $disqualify_link = $dsp->FetchSpanButton(t('Disqualifizieren'), "index.php?mod=tournament2&action=disqualify&teamid={$team1['teamid']}");
            }
*/
            $dsp->AddFieldSetStart(t('Team'). ' 1'. $tfunc->button_team_details($team1['teamid'], $tournamentid) . " ". $disqualify_link);
            $dsp->AddDoubleRow(t('Teamleiter'), $dsp->FetchUserIcon($team1['userid'], $team1['username']) . " (".t('Platz').": ". $seat2->SeatNameLink($team1['userid'], '', '') .")");
            $dsp->AddTextFieldRow("score_team1", t('Punktzahl'), (int) $team1["score"], "");
            $dsp->AddFieldSetEnd();

            // Write Team 2
            $disqualify_link = "";
/*  // Disquallifiy droped, due to errors
            if ($auth["type"] > 1 and $tournament['status'] == "process") {
                if ($team2['disqualified']) $disqualify_link = "<font color=\"#ff0000\">".t('Disqualifiziert')."</font> ". $dsp->FetchSpanButton(t('Disqualifizieren rückgängig'), "index.php?mod=tournament2&action=disqualify&teamid={$team2['teamid']}&step=10");
                else $disqualify_link = $dsp->FetchSpanButton(t('Disqualifizieren'), "index.php?mod=tournament2&action=disqualify&teamid={$team2['teamid']}");
            }
*/
            $dsp->AddFieldSetStart(t('Team'). ' 2'. $tfunc->button_team_details($team2['teamid'], $tournamentid) . " ". $disqualify_link);
            $dsp->AddDoubleRow(t('Teamleiter'), $dsp->FetchUserIcon($team2['userid'], $team2['username']) . " (".t('Platz').": ". $seat2->SeatNameLink($team2['userid'], '', '') .")");
            $dsp->AddTextFieldRow("score_team2", t('Punktzahl'), (int) $team2["score"], "");
            $dsp->AddFieldSetEnd();

            // Write Comment
            $dsp->AddFieldSetStart(t('Anmerkungen'));
            $dsp->AddFileSelectRow('screenshot', t('Screenshot anhängen'), '', '', '', 1);
            if (file_exists('ext_inc/tournament_screenshots/'. $_GET['gameid1'] .'.png')) {
                $dsp->AddDoubleRow(t('Aktuelles Bild'), '<img src="ext_inc/tournament_screenshots/'. $_GET['gameid1'] .'.png" />');
            }

            if ($team1['comment'] != "") {
                $score_comment = $team1['comment'];
            }
            $dsp->AddTextAreaPlusRow("score_comment", t('Bemerkung'), $score_comment, "", "", "", 1);
            $dsp->AddFieldSetEnd();
            $dsp->AddFormSubmitRow(t('Ergebnis'));

            $dsp->AddFieldsetStart('Log');
            include_once('modules/mastersearch2/class_mastersearch2.php');
            $ms2 = new mastersearch2('t2_games');

            $ms2->query['from'] = "%prefix%log AS l LEFT JOIN %prefix%user AS u ON l.userid = u.userid";
            $ms2->query['where'] = "(sort_tag = 'Turnier Ergebnise' AND target_id = ". (int)$_GET['gameid1'] .')';

            $ms2->AddResultField('', 'l.description');
            $ms2->AddSelect('u.userid');
            $ms2->AddResultField('', 'u.username', 'UserNameAndIcon');
            $ms2->AddResultField('', 'UNIX_TIMESTAMP(l.date) AS date', 'MS2GetDate');
            $ms2->PrintSearch('index.php?mod=tournament2&action=submit_result&step=1&tournamentid='. $_GET['tournamentid'] .'&gameid1='. $_GET['gameid1'] .'&gameid2='. $_GET['gameid2'], 'logid');
            $dsp->AddFieldsetEnd();

            $buttons = "";
            $buttons .= $dsp->FetchSpanButton(t('Paarungen'), "index.php?mod=tournament2&action=games&step=2&tournamentid=$tournamentid");
            $buttons .= " ". $dsp->FetchSpanButton(t('Spielbaum'), "index.php?mod=tournament2&action=tree&step=2&tournamentid=$tournamentid");
            $dsp->AddDoubleRow("", $buttons);
            break;

        // Formular in Datenbank eintragen
        case 2:
            ## Berechtigungsprüfung
            $berechtigt = 0;
            if ($auth["type"] > 1) {
                $berechtigt = 1;
            } // Admin always
            if ($cfg["t_only_loser_submit"]) {
                // Check only Looser
                if (($team1['userid'] == $auth["userid"]) && ($score_team1 < $score_team2)) {
                    $berechtigt = 1;
                }
                if (($team2['userid'] == $auth["userid"]) && ($score_team1 > $score_team2)) {
                    $berechtigt = 1;
                }
            } else {
                // Only Playing Team
                if ($team1['userid'] == $auth["userid"]) {
                    $berechtigt = 1;
                }
                if ($team2['userid'] == $auth["userid"]) {
                    $berechtigt = 1;
                }
            }

            ## Wurde Ergebnis schon eingetragen?
            $not_new = 0;
            if (($tournament["mode"] == "single") || ($tournament["mode"] == "double")) {
                $score = $db->qry_first("SELECT score FROM %prefix%t2_games WHERE (gameid = %int% OR gameid = %int%) AND score != 0", $gameid1, $gameid2);
                if ($score['score']) {
                    $not_new = 1;
                }
            }

            if ($_SESSION['tournament_submit_result_blocker']) {
                $func->error("NO_REFRESH", "index.php?mod=tournament2&action=submit_result&step=1&tournamentid=$tournamentid&gameid1=$gameid1&gameid2=$gameid2");
            } elseif ($tournament["status"] != "process") {
                $func->information(t('Dieses Turnier ist bereits beendet, oder noch nicht gestartet!'), "index.php?mod=tournament2&action=submit_result&step=1&tournamentid=$tournamentid&gameid1=$gameid1&gameid2=$gameid2");
            } elseif (($score_team1 == "") && ($score_team2 == "")) {
                $func->information(t('Bitte gib ein Ergebnis ein'), "index.php?mod=tournament2&action=submit_result&step=1&tournamentid=$tournamentid&gameid1=$gameid1&gameid2=$gameid2");
            } elseif (($score_team1 < 0) || ($score_team2 < 0)) {
                $func->information(t('Das Ergebnis muss eine possitive Zahl sein'), "index.php?mod=tournament2&action=submit_result&step=1&tournamentid=$tournamentid&gameid1=$gameid1&gameid2=$gameid2");
            } elseif (($score_team1 == $score_team2) && (
                ($tournament["mode"] == "single") || ($tournament["mode"] == "double")
                || (($tournament["mode"] == "groups") && ($team1["group_nr"] == 0))
                )) {
                $func->information(t('Ein Spiel darf nicht unentschieden enden! Es muss ein Sieger ausgemacht werden.'), "index.php?mod=tournament2&action=submit_result&step=1&tournamentid=$tournamentid&gameid1=$gameid1&gameid2=$gameid2");
            } elseif (($score_team1 == $score_team2) && ($tournament["mode"] == "liga") && ($score_team1 == 0)) {
                $func->information(t('Ein Spiel darf nicht 0:0 enden! Das würde bedeuten, es wäre nicht gespielt worden. Für Unentschieden bitte mindestens 1:1 eintragen.'), "index.php?mod=tournament2&action=submit_result&step=1&tournamentid=$tournamentid&gameid1=$gameid1&gameid2=$gameid2");
            } elseif (!$berechtigt) {
                if ($cfg["t_only_loser_submit"]) {
                    $func->information(t('Nur der Teamleiter des Verliererteams und Turnieradmins dürfen ein Ergebnis eintragen'), "index.php?mod=tournament2&action=submit_result&step=1&tournamentid=$tournamentid&gameid1=$gameid1&gameid2=$gameid2");
                } else {
                    $func->information(t('Nur Teilnehmer des Aktuellen Spiels und Turnieradmins dürfen ein Ergebnis eintragen'), "index.php?mod=tournament2&action=submit_result&step=1&tournamentid=$tournamentid&gameid1=$gameid1&gameid2=$gameid2");
                }
            } elseif (($not_new) && ($auth["type"] <= 1)) {
                $func->information(t('Es wurde bereits ein Ergebnis für diese Partie eingetragen. Das Ergebnis kann nur noch von Turnieradmins editiert werden. Melden dich daher für Änderungen bei diesen.'), "index.php?mod=tournament2&action=submit_result&step=1&tournamentid=$tournamentid&gameid1=$gameid1&gameid2=$gameid2");
            } else {
                // Upload Screenshot
                $old_file = $func->FileUpload('screenshot', 'ext_inc/tournament_screenshots/');
                if ($old_file) {
                    unlink('ext_inc/tournament_screenshots/'. $_GET['gameid1'] .'.png');
                    $gd->CreateThumb($old_file, 'ext_inc/tournament_screenshots/'. $_GET['gameid1'] .'.png', 800, 600);
                }
        
                if (($not_new) && ($qacc != 1)) {
                    $func->question(t('ACHTUNG: Zu diesem Turnier wurde bereits ein Ergebnis eingetragen. Wurde noch keine der Folgepartien dieses Spieles gespielt, so kann ohne Probleme fortgefahren werden. Wurden diese hingegen bereits gespielt, so solltest du dir im Klaren darüber sein, dass die beiden Folgepartien dadurch teilweise überschrieben werden und das Ergebnis dort auf 0 (noch nicht gespielt) gesetzt wird, sodass du alle aus dieser Partie resultierenden Partien erneut eintragen musst!'), "index.php?mod=tournament2&action=submit_result&step=2&gameid1=$gameid1&gameid2=$gameid2&tournamentid=$tournamentid&qacc=1&score_team1=$score_team1&score_team2=$score_team2&score_comment=$score_comment", "index.php?mod=tournament2&action=submit_result&step=1&gameid1=$gameid1&gameid2=$gameid2&tournamentid=$tournamentid");
                } else {
                    $_SESSION["tournament_submit_result_blocker"] = true;
                    
                    $tfunc->SubmitResult($tournamentid, $gameid1, $gameid2, $score_team1, $score_team2, $score_comment);

                    $func->confirmation(t('Danke! Das Ergebnis wurde erfolgreich gemeldet.'), "index.php?mod=tournament2&action=submit_result&step=1&tournamentid=$tournamentid&gameid1=$gameid1&gameid2=$gameid2");
/*
                    $cronjob->load_job("cron_tmod");
                    if($tournament['mode'] == "groups"){
                        $cronjob->loaded_class->add_job($_GET["tournamentid"],$team1["group_nr"]);
                    }else{
                        $cronjob->loaded_class->add_job($_GET["tournamentid"],"");
                    }
*/
                }
            }
            break;
        
        case 3:
            break;
    } // Switch
}
