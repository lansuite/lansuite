<?php

include_once("modules/mail/class_mail.php");
$mail = new mail();

$teams = $db->qry("SELECT teamid, leaderid, seeding_mark FROM %prefix%t2_teams WHERE (tournamentid = %int%) ORDER BY RAND()", $_GET["tournamentid"]);
$team_anz = $db->num_rows($teams);

$tournament = $db->qry_first("SELECT status, teamplayer, name, mode, blind_draw, mapcycle FROM %prefix%tournament_tournaments WHERE tournamentid = %int%", $_GET["tournamentid"]);

$seeded = $db->qry_first("SELECT COUNT(*) AS anz FROM %prefix%t2_teams WHERE (tournamentid = %int%) AND (seeding_mark = '1') GROUP BY tournamentid", $_GET["tournamentid"]);

if ($_GET["step"] < 2 and $tournament["blind_draw"]) {
    $team_anz = floor($team_anz / $tournament["teamplayer"]);
}


########## Fehler prüfen
## Mind. 4 Teams im Turnier
if ($team_anz < 4) {
    $func->information(t('Es müssen mindestens 4 Teams angemeldet sein!'), "index.php?mod=tournament2&action=details&tournamentid={$_GET["tournamentid"]}&headermenuitem=2");

## Bei Gruppen-Modus: Mind. 6 Teams im Turnier
} elseif ($tournament['mode'] == "groups" and $team_anz < 6) {
    $func->information(t('Es müssen mindestens 6 Teams angemeldet sein!'), "index.php?mod=tournament2&action=details&tournamentid={$_GET["tournamentid"]}&headermenuitem=2");

## Status noch Offen
} elseif ($tournament['status'] != "open") {
    $func->information(t('Dieses Turnier wurde bereits gestartet!'), "index.php?mod=tournament2&action=details&tournamentid={$_GET["tournamentid"]}&headermenuitem=1");

## Nicht mehr als die Hälft geseeded
} elseif (($seeded['anz']) > ($team_anz / 2)) {
    $func->information(t('Es wurde bereits die Hälfte der fest angemeldeten Teams markiert! Demarkiere zuerst ein Team, bevor du ein weiteres markieren'), "index.php?mod=tournament2&action=details&tournamentid={$_GET["tournamentid"]}&headermenuitem=2");


########## Keine Fehler gefunden
} else {
    if ($_GET["step"] == 2) {
        ## Blind-Draw Teams zulosen
        if ($tournament["blind_draw"]) {
            $bd_teams = $db->qry("SELECT * FROM %prefix%t2_teams WHERE (tournamentid = %int%) ORDER BY RAND()", $_GET["tournamentid"]);
            $z = 0;
            while ($bd_team = $db->fetch_array($bd_teams)) {
                if ($z % $tournament["teamplayer"] == 0) {
                    $teamid = $bd_team["teamid"];
                } else {
                    $db->qry("INSERT INTO %prefix%t2_teammembers
      SET tournamentid = %int%,
      userid = %int%,
      teamid = %int%
      ", $_GET["tournamentid"], $bd_team["leaderid"], $teamid);
                    $db->qry("DELETE FROM %prefix%t2_teams WHERE teamid = %int%", $bd_team["teamid"]);
                }
                $z++;
            }

            // Recalculate team-anz
            $teams = $db->qry("SELECT teamid, leaderid, seeding_mark FROM %prefix%t2_teams WHERE (tournamentid = %int%) ORDER BY RAND()", $_GET["tournamentid"]);
            $team_anz = $db->num_rows($teams);
        }

        ## Prüfen auf unvollständige Teams
        ## Unvollständige Teams zählen
        $waiting_teams = 0;
        $teams2 = $db->qry("SELECT name, teamid FROM %prefix%t2_teams WHERE (tournamentid = %int%)", $_GET["tournamentid"]);
        while ($team2 = $db->fetch_array($teams2)) {
            $members = $db->qry_first("SELECT COUNT(*) AS members
    FROM %prefix%t2_teammembers
    WHERE (teamid = %int%)
    GROUP BY teamid
    ", $team2['teamid']);
            if (($members["members"] + 1) < $tournament['teamplayer']) {
                $waiting_teams ++;
            }
        }
        $db->free_result($teams2);

        ## Wenn unvollständige Teams vorhanden: Fragen, ob löschen
        if (($tournament['teamplayer'] == 1) || ($waiting_teams == 0)) {
            $_GET["step"] = 3;
        } else {
            $func->question(t('Zu diesem Turnier haben sich Teams angemeldet, welche noch nicht komplett sind. Möchtest du diese beim Generieren aus dem Turnier entfernen?'), "index.php?mod=tournament2&action=generate_pairs&step=4&tournamentid={$_GET["tournamentid"]}", "index.php?mod=tournament2&action=generate_pairs&step=3&tournamentid={$_GET["tournamentid"]}");
        }
    }

    ## Unvollständige Teams löschen
    if ($_GET["step"] == 4) {
        $teams2 = $db->qry("SELECT teamid, leaderid FROM %prefix%t2_teams WHERE (tournamentid = %int%)", $_GET["tournamentid"]);
        while ($team2 = $db->fetch_array($teams2)) {
            $members = $db->qry_first("SELECT COUNT(*) AS members
    FROM %prefix%t2_teammembers
    WHERE (teamid = %int%)
    GROUP BY teamid
    ", $team2['teamid']);
            if (($members["members"] + 1) < $tournament['teamplayer']) {
                $db->qry("DELETE FROM %prefix%t2_teams WHERE (teamid = %int%) AND (tournamentid = %int%)", $team2["teamid"], $_GET["tournamentid"]);
                $db->qry("DELETE FROM %prefix%t2_teammembers WHERE (teamid = %int%) AND (tournamentid = %int%)", $team2["teamid"], $_GET["tournamentid"]);

                $mail->create_sys_mail($team2['leaderid'], t_no_html('Dein Team wurde vom Turnier %1 abgemeldet', $tournament['name']), t_no_html('Der Turnieradmin hat soeben die Paarungen für das Turnier %1 generiert. Da Dein Team zu diesem Zeitpunkt leider noch nicht vollständig war, wurde es, wie vom Turnieradmin gewünscht, vom Turnier abgemeldet.', $tournament['name']));
                $func->log_event(t('Alle unvollständigen Teams im Turnier %1 wurden entfernt', $tournament['name']), 1, t('Turnier Teamverwaltung'));
            }
        }
        $db->free_result($teams2);

        $func->question(t('Alle unvollständigen Teams im Turnier %1 wurden erfolgreich gelöscht. Möchtest du das Turnier nun generieren?', $tournament["name"]), "index.php?mod=tournament2&action=generate_pairs&step=3&tournamentid={$_GET["tournamentid"]}", "index.php?mod=tournament2&action=details&tournamentid={$_GET["tournamentid"]}&headermenuitem=2");
    }


    ## Generieren

    //random mapcycle
    $rand_map = explode("\r\n", $tournament["mapcycle"]);
    shuffle($rand_map);
    $db->qry("UPDATE %prefix%tournament_tournaments SET mapcycle = %string% WHERE tournamentid = %int%", implode("\r\n", $rand_map), $_GET["tournamentid"]);

    if ($_GET["step"] == 3) {
        switch ($tournament['mode']) {
            case "single":
            case "double":
                ########## Anzahl an benötigten Freilosen bestimmen
                $exp = 0;
                for ($z = $team_anz; $z > 1; $z /= 2) {
                    $exp++;
                }
                $needed_freilose = pow(2, $exp) - $team_anz;

                ########## Seeding durchführen
                ## Teams werden in 2 Array geteilt: Geseedet und Nicht-geseedet
                $seed_team_liste = array();
                $noseed_team_liste = array();

                $teams_num = 0;
                while ($team = $db->fetch_array($teams)) {
                    $teams_num++;
                    if ($team["seeding_mark"]) {
                        array_push($seed_team_liste, $team["leaderid"]);
                    } else {
                        array_push($noseed_team_liste, $team["leaderid"]);
                    }

                    $mail->create_sys_mail($team['leaderid'], t_no_html('Turnier %1 generiert', $tournament['name']), t_no_html('Die Rundes des Turniers %1 wurden soeben generiert. Wir bitte dich, direkt mit dem ersten Spiel anzufangen, damit es keine unnötge Verzögerung im Turnier gibt. Viel Erfolg!', $tournament['name']));
                }
                $seeded_teams_num = count($seed_team_liste);

                ## Jedes wie vielte Element soll geseedet werden?
                ($seeded_teams_num) ? $seed_this = ceil($teams_num / $seeded_teams_num) : $seed_this = 0;

                ## Die beiden Arrays wieder sortiert zu einem zusammenfügen
                $team_liste = array();
                for ($akt = 1; $akt <= $teams_num; $akt++) {
                    $error = 0;
                    if (($seed_this) && (($akt % $seed_this) == 1)) {
                        if (!($akt_leaderid = array_shift($seed_team_liste))) {
                            $error = 1;
                        }
                    } else {
                        if (!($akt_leaderid = array_shift($noseed_team_liste))) {
                            $error = 1;
                        }
                    }
                    if (!$error) {
                        array_push($team_liste, $akt_leaderid);
                    }
                    if ($error) {
                        echo "FEHLER beim Seeding!";
                    }
                }


                ########## Teams in die Paarungen-Tabelle schreiben
                $pos_round0 = 0;
                $pos_round1 = 0;
                $pos_round05 = 0;
                $pos_roundm1 = 1;

                while ($akt_leaderid = array_shift($team_liste)) {
                    $db->qry("INSERT INTO %prefix%t2_games SET
       tournamentid = %int%,
       leaderid = %int%,
       round = 0,
       position = %string%,
       score = 0
       ", $_GET["tournamentid"], $akt_leaderid, $pos_round0);
                    $pos_round0++;

                    // Freilose einfügen
                    if ($needed_freilose > 0) {
                        $needed_freilose--;
                        $db->qry("INSERT INTO %prefix%t2_games SET
       tournamentid = %int%,
       leaderid = 0,
       round = 0,
       position = %string%,
       score = 0
       ", $_GET["tournamentid"], $pos_round0);
                        $pos_round0++;
                        // Spieler gegen Freilose in nächste Runde schieben
                        $db->qry("INSERT INTO %prefix%t2_games SET
       tournamentid = %int%,
       leaderid = %int%,
       round = 1,
       position = %string%,
       score = 0
       ", $_GET["tournamentid"], $akt_leaderid, $pos_round1);
                        $pos_round1++;
                        // Freilose ins Loser-Bracket schieben
                        $db->qry("INSERT INTO %prefix%t2_games SET
       tournamentid = %int%,
       leaderid = 0,
       round = -0.5,
       position = %string%,
       score = 0
       ", $_GET["tournamentid"], $pos_round05);
                        $pos_round05++;
                        // Freilose vs Freilose im Loser-Bracket Runde -0.5 auswerten und nach Runde -1 verschieben
                        if (($needed_freilose % 2) == 1) {
                            $db->qry("INSERT INTO %prefix%t2_games SET
        tournamentid = %int%,
        leaderid = 0,
        round = -1,
        position = %string%,
        score = 0
        ", $_GET["tournamentid"], $pos_roundm1);
                            $pos_roundm1+=2;
                        }
                    }
                }
                break;

            case "liga":
            case "groups":
                // Calculate size and number of groups
                $group_anz = 1;
                if ($tournament['mode'] == "groups") {
                    $res = 10;
                    while ($res >= 3) {
                        $group_anz *= 2;
                        $res = floor($team_anz / $group_anz);
                    }
                    $group_anz /= 2;
                }
                $num_over_size = $team_anz % $group_anz;

                // for each group, round, position
                for ($group = 1; $group <= $group_anz; $group++) {
                    $group_size = floor($team_anz / $group_anz);

                    // If there are still teams with oversize, increase group size for this group
                    $team_liste = array();
                    if ($num_over_size > 0) {
                        $num_over_size--;
                        $group_size++;
                    }

                    // Get teams for this round
                    $i = 0;
                    while (($i < $group_size) && ($team = $db->fetch_array($teams))) {
                        $i++;
                        array_push($team_liste, $team["leaderid"]);
                    }
                    // If odd, insert faketeam "Geamefree"
                    if (floor($group_size / 2) != ($group_size / 2)) {
                        array_push($team_liste, "0");
                        $group_size++;
                    }

                    for ($round = 0; $round < ($group_size-1); $round++) {
                        $team_liste_2 = $team_liste;

                        // Write games to db
                        for ($position = 0; $position < $group_size; $position++) {
                            $akt_leader_id = array_shift($team_liste);
                            $db->qry("INSERT INTO %prefix%t2_games SET
         tournamentid = %int%,
         leaderid = %int%,
         round = %string%,
         position = %string%,
         group_nr = %string%,
         score = 0
         ", $_GET["tournamentid"], $akt_leader_id, $round, $position, $group);
                        }

                        // Rotate position for next round
                        array_push($team_liste, $team_liste_2[0]);
                        array_push($team_liste, $team_liste_2[2]);
                        for ($position = 2; $position <= ($group_size-4); $position+=2) {
                            array_push($team_liste, $team_liste_2[$position+2]);
                            array_push($team_liste, $team_liste_2[$position-1]);
                        }
                        array_push($team_liste, $team_liste_2[$group_size-1]);
                        array_push($team_liste, $team_liste_2[$group_size-3]);
                    }
                }
                break;

            case "all":
                $z = 0;
                while ($team = $db->fetch_array($teams)) {
                    $db->qry("INSERT INTO %prefix%t2_games SET
       tournamentid = %int%,
       leaderid = %int%,
       round = 0,
       position = %int%,
       score = 0
       ", $_GET["tournamentid"], $team['leaderid'], $z);
                    $z++;
                }
                break;
        } // Switch Tournament-Mode
        $db->free_result($teams);

        ########## Turnierstatus auf "process" setzen
        $db->qry("UPDATE %prefix%tournament_tournaments SET status='process' WHERE tournamentid = %int%", $_GET["tournamentid"]);

        $func->confirmation(t('Das Turnier %1 wurde generiert.<br>Die Begegnungen können nun gespielt werden.', $tournament["name"]), "index.php?mod=tournament2&action=details&tournamentid={$_GET["tournamentid"]}");
        $func->log_event(t('Das Turnier %1 wurde generiert', $tournament["name"]), 1, t('Turnier Verwaltung'));
/*
        $cronjob->load_job("cron_tmod");
        if($tournament['mode'] == "groups"){
            for ($i = 0; $i <= $group_anz; $i++){
                $cronjob->loaded_class->add_job($_GET["tournamentid"],$i);
            }
        }else{
            $cronjob->loaded_class->add_job($_GET["tournamentid"],"");
        }
*/
    } // Step = 3
}
