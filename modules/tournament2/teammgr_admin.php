<?php

include("modules/tournament2/class_team.php");
$tteam = new team;

($_GET['tournamentid'])? $tournamentid = $_GET['tournamentid'] : $tournamentid = $_POST['tournamentid'];
($_GET['userid'])? $userid = $_GET['userid'] : $userid = $_POST['userid'];
($_GET['teamid'])? $teamid = $_GET['teamid'] : $teamid = $_POST['teamid'];
($_GET['member_user'])? $member_user = $_GET['member_user'] : $member_user = $_POST['member_user'];


switch ($_GET["step"]) {
    // Team löschen
    case 10:
        if ($tteam->delete($_POST["teamid"])) {
            $func->confirmation(t('Das Team wurde erfolgreich gelöscht'), "index.php?mod=tournament2&action=teammgr_admin");
        }
        break;

    // Spieler einem Team hinzufügen - Suchen
    case 20:
        include_once('modules/usrmgr/search_main.inc.php');

        $ms2->query['where'] .= "p.party_id={$party->party_id} AND (p.paid)";
        if ($auth['type'] >= 2) {
            $ms2->AddIconField('assign', 'index.php?mod=tournament2&action=teammgr_admin&step=21&teamid='. $teamid .'&userid=', 'Assign');
        }

        $ms2->PrintSearch('index.php?mod=tournament2&action=teammgr_admin&step=20&teamid='. $teamid, 'u.userid');
        break;

    // Spieler einem Team hinzufügen - Ausführen
    case 21:
        if ($tteam->join($_GET["teamid"], $_GET["userid"])) {
            $func->confirmation(t('Der Spieler wurde dem Team hinzugefügt'), "index.php?mod=tournament2&action=teammgr_admin");
        }
        break;

    // Member aus Team löschen
    case 30:
        list($team_id, $user_id) = explode("-", $_POST["member_user"], 2);
        if ($tteam->kick($team_id, $user_id)) {
            $func->confirmation(t('Der Spieler wurde erfolgreich aus dem Team entfernt'), "index.php?mod=tournament2&action=teammgr_admin");
        }
        break;

    // Neues Team eröffnen - Teamleiter auswählen
    case 40:
        if ($tteam->SignonCheck($tournamentid)) {
            include_once('modules/usrmgr/search_main.inc.php');

            $ms2->query['where'] .= "p.party_id={$party->party_id} AND (p.paid)";
            if ($auth['type'] >= 2) {
                $ms2->AddIconField('assign', 'index.php?mod=tournament2&action=teammgr_admin&step=41&tournamentid='. $tournamentid .'&userid=', 'Assign');
            }

            $ms2->PrintSearch('index.php?mod=tournament2&action=teammgr_admin&step=40&tournamentid='. $tournamentid, 'u.userid');
        }
        break;

    // Neues Team eröffnen - Teamname eingeben
    case 41:
        $sec->unlock("t_admteam_create");

        $t = $db->qry_first("SELECT teamplayer FROM %prefix%tournament_tournaments WHERE tournamentid = %int%", $tournamentid);
        if ($t['teamplayer'] == 1) {
            $leader = $db->qry_first("SELECT username FROM %prefix%user WHERE userid = %int%", $_GET['userid']);
            $_POST["team_name"] = $leader["username"];
        }

        if ($tteam->SignonCheckUser($_GET["tournamentid"], $_GET["userid"])) {
            $dsp->SetForm("index.php?mod=tournament2&action=teammgr_admin&step=42&tournamentid=$tournamentid&userid=$userid");
            $dsp->AddTextFieldRow("team_name", t('Teamname'), $_POST["team_name"], "");
            $dsp->AddPasswordRow("set_password", t('Team-Passwort festlegen'), $_POST["set_password"], $error["set_password"]);
            $dsp->AddPasswordRow("set_password2", t('Team-Passwort wiederholen'), $_POST["set_password2"], $error["set_password2"]);
            $dsp->AddTextAreaPlusRow("team_comment", t('Bemerkung'), $team_comment, "", "", "", 1);
            $dsp->AddFileSelectRow("team_banner", t('Team-Logo (max. 1MB)'), "", "", 1000000, 1);
            $dsp->AddFormSubmitRow(t('Hinzufügen'));
            $dsp->AddBackButton("index.php?mod=tournament2&action=teammgr_admin", "");
        }
        break;

    // Neues Team eröffnen - In DB schreiben
    case 42:
        if ($_POST["set_password"] and $_POST["set_password"] != $_POST["set_password2"]) {
            $func->information("Die Passworteingaben stimmen nicht überein", "index.php?mod=tournament2&action=teammgr_admin&step=41&tournamentid=$tournamentid&userid=$userid");
        } elseif ($_POST['team_name'] == "") {
            $func->information(t('Bitte gib einen Teamnamen ein, oder wähle ein vorhandenes Team aus'), "index.php?mod=tournament2&action=teammgr_admin&step=41&tournamentid=$tournamentid&userid=$userid");
        } elseif (!$sec->locked("t_admteam_create")) {
            $t = $db->qry_first("SELECT name FROM %prefix%tournament_tournaments WHERE tournamentid = %int%", $_GET["tournamentid"]);

            if ($tteam->create($_GET["tournamentid"], $_GET["userid"], $_POST["team_name"], $_POST["set_password"], $_POST["team_comment"], "team_banner")) {
                $func->confirmation(t('Der Spieler / Das Team wurden zum Turnier %1 erfolgreich angemeldet', $t["name"]), "index.php?mod=tournament2&action=teammgr_admin");
            }

            $sec->lock("t_admteam_create");
        }
        break;


    default:
        $dsp->NewContent(t('Admin-Teammanager'), t('Hier kannst du Teams löschen oder ihnen weitere Spieler zuweisen.'));

        // Neues Team anmelden
        $tourneys = $db->qry("SELECT tournamentid, name FROM %prefix%tournament_tournaments WHERE (status = 'open')  AND party_id=%int% ORDER BY name", $party->party_id);
        if ($db->num_rows($tourneys) == 0) {
            $dsp->AddDoubleRow(t('Neues Team (Spieler) anmelden<br />(Nur in Anmeldephase möglich)'), t('Es sind keine Turniere vorhanden, welche sich noch in der Anmeldephase befinden'));
        } else {
            $t_array = array("<option value=\"\">".t('Bitte Turnier auswählen')."</option>");
            while ($tourney = $db->fetch_array($tourneys)) {
                array_push($t_array, "<option value=\"{$tourney['tournamentid']}\">{$tourney['name']}</option>");
            }
            $dsp->SetForm("index.php?mod=tournament2&action=teammgr_admin&step=40");
            $dsp->AddDropDownFieldRow("tournamentid", t('Neues Team (Spieler) anmelden<br />(Nur in Anmeldephase möglich)'), $t_array, "");
            $dsp->AddFormSubmitRow(t('Abschicken'));
        }
        $db->free_result($teams);

        // Team löschen Auswahl
        $teams = $db->qry("SELECT teams.teamid, teams.name, t.name AS tname, t.teamplayer
   FROM %prefix%t2_teams AS teams
   LEFT JOIN %prefix%tournament_tournaments AS t ON t.tournamentid = teams.tournamentid
   WHERE t.status = 'open' AND t.party_id = %int%
   ORDER BY t.name, teams.name
   ", $party->party_id);
        if ($db->num_rows($teams) == 0) {
            $dsp->AddDoubleRow(t('Komplettes Team löschen<br />(Nur in Anmeldephase möglich)'), t('Es haben sich noch keine Spieler zu Turnieren angemeldet, welche noch nicht bereits laufen'));
        } else {
            $t_array = array("<option value=\"\">".t('Bitte Team auswählen')."</option>");
            while ($team = $db->fetch_array($teams)) {
                array_push($t_array, "<option value=\"{$team['teamid']}\">{$team['tname']} - {$team['name']}</option>");
            }
            $dsp->SetForm("index.php?mod=tournament2&action=teammgr_admin&step=10");
            $dsp->AddDropDownFieldRow("teamid", t('Komplettes Team löschen<br />(Nur in Anmeldephase möglich)'), $t_array, "");
            $dsp->AddFormSubmitRow(t('Löschen'));
        }
        $db->free_result($teams);

        // Spieler hinzufügen Auswahl
        $teams = $db->qry("SELECT teams.teamid, teams.name, t.name AS tname, t.teamplayer
   FROM %prefix%t2_teams AS teams
   LEFT JOIN %prefix%tournament_tournaments AS t ON t.tournamentid = teams.tournamentid
   WHERE t.teamplayer > 1 AND t.status != 'closed' AND t.party_id = %int%
   ORDER BY t.name, teams.name
   ", $party->party_id);
        if ($db->num_rows($teams) == 0) {
            $dsp->AddDoubleRow(t('Spieler einem Team hinzufügen'), t('Es existieren keine Teams, die noch auf weitere Spieler warten'));
        } else {
            $t_array = array("<option value=\"\">".t('Bitte Team auswählen')."</option>");
            while ($team = $db->fetch_array($teams)) {
                $member = $db->qry_first("SELECT COUNT(*) AS members FROM %prefix%t2_teammembers WHERE teamid = %int% GROUP BY teamid", $team['teamid']);

                $freie_platze = $team['teamplayer'] - ($member['members'] + 1);
                if ($freie_platze > 0) {
                    array_push($t_array, "<option value=\"{$team['teamid']}\">{$team['tname']} - {$team['name']} (". t('Noch %1 frei', $freie_platze) .")</option>");
                }
            }
            $dsp->SetForm("index.php?mod=tournament2&action=teammgr_admin&step=20");
            $dsp->AddDropDownFieldRow("teamid", t('Spieler einem Team hinzufügen'), $t_array, "");
            $dsp->AddFormSubmitRow(t('Abschicken'));
        }
        $db->free_result($teams);

        // Member aus Team löschen - Auswahl
        $teams = $db->qry("SELECT teams.teamid, teams.name, t.name AS tname, users.username AS mname, t.teamplayer, members.userid AS userid
   FROM %prefix%t2_teams AS teams
   LEFT JOIN %prefix%t2_teammembers AS members ON teams.teamid = members.teamid
   LEFT JOIN %prefix%user AS users ON members.userid = users.userid
   LEFT JOIN %prefix%tournament_tournaments AS t ON t.tournamentid = teams.tournamentid
   WHERE t.teamplayer > 1 AND t.party_id = %int%
   ORDER BY t.name, teams.name
   ", $party->party_id);
        if ($db->num_rows($teams) == 0) {
            $dsp->AddDoubleRow(t('Spieler aus einem Team löschen'), t('Es haben sich noch keine Mitglieder zu Teams angemeldet'));
        } else {
            $t_array = array("<option value=\"\">".t('Bitte Team auswählen')."</option>");
            while ($team = $db->fetch_array($teams)) {
                array_push($t_array, "<option value=\"{$team['teamid']}-{$team['userid']}\">{$team['tname']} - {$team['name']} - {$team['mname']}</option>");
            }
            $dsp->SetForm("index.php?mod=tournament2&action=teammgr_admin&step=30");
            $dsp->AddDropDownFieldRow("member_user", t('Spieler aus einem Team löschen'), $t_array, "");
            $dsp->AddFormSubmitRow(t('Löschen'));
        }
        $db->free_result($teams);

        $dsp->AddBackButton("index.php?mod=tournament2", "tournament2/teammgr_admin");
        break;
}

$dsp->AddContent();
