<?php
/*  // Disquallifiy droped, due to errors
$teamid = $_GET["teamid"];

include_once("modules/mail/class_mail.php");
$mail = new mail();

$team = $db->qry_first("SELECT teams.name, t.name AS t_name, teams.leaderid, teams.tournamentid
  FROM %prefix%t2_teams AS teams
  LEFT JOIN %prefix%tournament_tournaments AS t ON t.tournamentid = teams.tournamentid
  WHERE (teams.teamid = %int%)
  ", $teamid);

if (!$team['tournamentid']) $func->error(t('Das ausgewählte Turnier existiert nicht'));
else switch ($_GET["step"]){
    // Disqualify-Question
    default:
        $func->question(str_replace("%NAME%", $team['name'], str_replace("%T%", $team['t_name'], t('Soll das Team \'%NAME%\' wirklich im Turnier \'%T%\' disqualifiziert werden?HTML_NEWLINEDiese Aktion kann nicht mehr rückgängig gemacht werden!'))), "index.php?mod=tournament2&action=disqualify&step=2&teamid=$teamid");
    break;

    // Disqualify
    case 2:
        $db->qry("UPDATE %prefix%t2_teams SET disqualified='1' WHERE (teamid = %int%)", $teamid);

        include_once("modules/tournament2/class_tournament.php");
        $tfunc = new tfunc;

        $team2['teamid'] = 1;
        while ($team2['teamid']){
            $team2 = $db->qry_first("SELECT games1.gameid AS gid1, games2.gameid AS gid2, teams1.teamid
    FROM %prefix%t2_games AS games1
    INNER JOIN %prefix%t2_games AS games2 ON (games1.round = games2.round) AND ((games1.position + 1) = games2.position) AND (games1.tournamentid = games2.tournamentid)
    LEFT JOIN %prefix%t2_teams AS teams1 ON (games1.leaderid = teams1.leaderid) AND (games1.tournamentid = teams1.tournamentid)
    LEFT JOIN %prefix%t2_teams AS teams2 ON (games2.leaderid = teams2.leaderid) AND (games2.tournamentid = teams2.tournamentid)
    WHERE ((games1.position / 2) = FLOOR(games1.position / 2))
    AND (games1.score = 0) AND (games2.score = 0)
    AND ((teams1.teamid = %int%) OR (teams2.teamid = %int%))
    ", $teamid, $teamid);

            if ($team2['teamid']){
                // Set score to default win for opponent
                if ($cfg["t_default_win"] == 0) $cfg["t_default_win"] = 2;
                if ($team2['teamid'] == $teamid) {
                    $score1 = 0;
                    $score2 = $cfg["t_default_win"];
                } else {
                    $score1 = $cfg["t_default_win"];
                    $score2 = 0;
                }
                $tfunc->SubmitResult($team['tournamentid'], $team2['gid1'], $team2['gid2'], $score1, $score2, addslashes(str_replace("%NAME%", $team['name'], t('Defaultwin. Team \'%NAME%\' wurde Disqualifiziert.'))));
            }
        }

        $func->log_event(str_replace("%NAME%", $team['name'], str_replace("%T%", $team['t_name'], t('Das Team \'%NAME%\' wurde im Turnier \'%T%\' disqualifiziert'))), 1, t('Turnier Teamverwaltung'));

        $mail->create_sys_mail($team['leaderid'], str_replace("%NAME%", $team['name'], str_replace("%T%", $team['t_name'], t_no_html('Dein Team \'%NAME%\' wurde im Turnier \'%T%\' disqualifiziert'))), str_replace("%NAME%", $team['name'], str_replace("%T%", $team['t_name'], t_no_html('Ein Admin hat soeben dein Team \'%NAME%\' im Turnier \'%T%\' disqualifiziert. Damit nimmst du nicht mehr teil.'))));

        $func->confirmation(str_replace("%NAME%", $team['name'], str_replace("%T%", $team['t_name'], t('Das Team \'%NAME%\' wurde erfolgreich im Turnier \'%T%\' disqualifiziert'))), "index.php?mod=tournament2");
    break;


    // Un-Disqualify
    case 10:
        $db->qry("UPDATE %prefix%t2_teams SET disqualified='0' WHERE (teamid = %int%)", $teamid);

        $func->log_event(str_replace("%NAME%", $team['name'], str_replace("%T%", $team['t_name'], t('Die Disqualifikation des Teams \'%NAME%\' im Turnier \'%T%\' wurde zurückgenommen'))), 1, t('Turnier Teamverwaltung'));

        $mail->create_sys_mail($team['leaderid'], str_replace("%NAME%", $team['name'], str_replace("%T%", $team['t_name'], t_no_html('Die Disqualifikation deines Teams \'%NAME%\' wurde zurückgenommen'))), str_replace("%NAME%", $team['name'], str_replace("%T%", $team['t_name'], t_no_html('Ein Admin hat gerade die Disqualifikation deines Teams \'%NAME%\' im Turnier \'%T%\' zurückgenommen. Kannst du wieder am Turnier teilnehmen.'))));

        $func->confirmation(str_replace("%NAME%", $team['name'], str_replace("%T%", $team['t_name'], t('Die Disqualifikation des Teams \'%NAME%\' im Turnier \'%T%\' wurde erfolgreich zurückgenommen. Achte darauf, dass alle Ergebnise, die durch die Disqualifizierung automatisch eingetragen wurden, immernoch eingetragen sind. Um diese zu korrigieren, kannst du die betreffenden Spiele einfach mit neuen Ergebnisen überschreiben.'))), "index.php?mod=tournament2");
    break;
}
*/
