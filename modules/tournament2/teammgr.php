<?php

$seat2 = new \LanSuite\Module\Seating\Seat2();
$mail = new \LanSuite\Module\Mail\Mail();

$tteam = new \LanSuite\Module\Tournament2\Team($mail, $seat2);

($_GET['tournamentid'])? $tournamentid = $_GET['tournamentid'] : $tournamentid = $_POST['tournamentid'];
($_GET['userid'])? $userid = $_GET['userid'] : $userid = $_POST['userid'];

switch ($_GET['step']) {
    // Team verlassen
    case 10:
        if ($tteam->kick($_GET["teamid"], $auth["userid"])) {
            $func->confirmation(t('Du wurdest aus dem Team entfernt'), "index.php?mod=tournament2&action=teammgr");
        }
        break;

    // Spieler aus Team entfernen
    case 20:
        if ($tteam->kick($_GET["teamid"], $userid)) {
            $func->confirmation(t('Der Spieler wurde aus deinem Team entfernt'), "index.php?mod=tournament2&action=teammgr");
        }
        break;

    // Team abmelden (löschen) / Mich abmelden
    case 30:
        if ($tteam->delete($_GET["teamid"])) {
            $func->confirmation(t('Dein Team wurde vom Turnier abgemeldet'), "index.php?mod=tournament2&action=teammgr");
        }
        break;

    // Spieler zum eigenen Team hinzufügen - Suchen
    case 40:
        include_once('modules/usrmgr/search_main.inc.php');

        $ms2->query['where'] .= "p.party_id={$party->party_id} AND (p.paid)";
        $ms2->AddIconField('assign', 'index.php?mod=tournament2&action=teammgr&step=41&teamid='. $_GET["teamid"] .'&tournamentid='. $tournamentid .'&userid=', 'Assign');

        $ms2->PrintSearch('index.php?mod=tournament2&action=teammgr&step=40&teamid='. $_GET["teamid"] .'&tournamentid='. $tournamentid, 'u.userid');
        break;

    // Spieler zum eigenen Team hinzufügen - In DB schreiben
    case 41:
        if ($tteam->join($_GET["teamid"], $userid)) {
            $func->confirmation(t('Der Spieler wurde deinem Team hinzugefügt'), "index.php?mod=tournament2&action=teammgr");
        }
        break;

    // Edit Teamdetails (Form)
    case 50:
        $sec->unlock("t_team_edit");

        $tournament = $db->qry_first("
          SELECT
            teamplayer,
            wwcl_gameid,
            ngl_gamename
          FROM %prefix%tournament_tournaments
          WHERE
            tournamentid = %int%", $tournamentid);

        $team = $db->qry_first("
          SELECT
            team.name,
            team.comment,
            team.banner,
            user.nglid,
            user.nglclanid,
            user.wwclid,
            user.wwclclanid
        FROM %prefix%t2_teams AS team
        LEFT JOIN %prefix%user AS user ON user.userid = team.leaderid
        WHERE
          teamid = %int%", $_GET["teamid"]);

        $dsp->NewContent(t('Teammanager'), t('Hier kannst du deinem Teams verwalten'));
        $dsp->SetForm("index.php?mod=tournament2&action=teammgr&step=51&teamid={$_GET["teamid"]}&tournamentid=$tournamentid", "", "", "multipart/form-data");

        if ($tournament['teamplayer'] == 1) {
            $dsp->AddDoubleRow(t('Teamname'), $auth["username"]);
            $team['name'] = $auth["username"];
        } else {
            $dsp->AddTextFieldRow("team_name", t('Teamname'), $team['name'], "");
        }

        $dsp->AddPasswordRow("set_password", t('Team-Passwort festlegen'), $_POST["set_password"], $error["set_password"]);
        $dsp->AddPasswordRow("set_password2", t('Team-Passwort wiederholen'), $_POST["set_password2"], $error["set_password2"]);

        $dsp->AddTextAreaPlusRow("team_comment", t('Bemerkung'), $team['comment'], "", "", "", 1);
        if ($team["banner"]) {
            $dsp->AddSingleRow("<img src=\"ext_inc/team_banners/{$team['banner']}\" alt=\"{$team['banner']}\">");
        }
        $dsp->AddFileSelectRow("team_banner", t('Team-Logo (max. 1MB)'), "", "", 1000000, 1);

        if ($tournament['wwcl_gameid'] > 0) {
            $dsp->AddTextFieldRow("wwclid", t('WWCL ID'), $team['wwclid'], "");
            if ($tournament['teamplayer'] > 1) {
                $dsp->AddTextFieldRow("wwclclanid", t('WWCL Clan'), $team['wwclclanid'], "");
            }
        }
        if ($tournament['ngl_gamename'] != "") {
            $dsp->AddTextFieldRow("nglid", t('NGL ID'), $team['nglid'], "");
            if ($tournament['teamplayer'] > 1) {
                $dsp->AddTextFieldRow("nglclanid", t('NGL Clan ID'), $team['nglclanid'], "");
            }
        }

        $dsp->AddFormSubmitRow(t('Editieren'));
        $dsp->AddBackButton("index.php?mod=tournament2&action=teammgr", "tournament2/teammgr");
        break;

    // Edit Teamdetails (Action)
    case 51:
        if (!$sec->locked("t_team_edit")) {
            $tournament = $db->qry_first("SELECT name FROM %prefix%tournament_tournaments WHERE tournamentid = %int%", $tournamentid);

            if ($_POST['team_name'] == "" and $tournament['teamplayer'] > 1) {
                $func->information(t('Bitte gib einen Teamnamen ein, oder wähle ein vorhandenes Team aus'), "index.php?mod=tournament2&action=teammgr&tournamentid=$tournamentid&teamid={$_GET["teamid"]}&step=50");
                break;
            }

            if ($_POST["set_password"] and $_POST["set_password"] != $_POST["set_password2"]) {
                $error["set_password2"] = "Die Passworteingaben stimmen nicht überein";
            }

            if ($tteam->edit($_GET["teamid"], $_POST['team_name'], $_POST["set_password"], $_POST['team_comment'], "team_banner")) {
                $func->confirmation(t('Die Daten wurden erfolgreich editiert'), "index.php?mod=tournament2&action=teammgr");
            }

            $sec->lock("t_team_edit");
        }
        break;


    default:
        $dsp->NewContent(t('Teammanager'), t('Hier kannst du deine Teams verwalten'));

        $dsp->AddSingleRow(t('Einzelspieler-Turniere, an denen du teilnimmst'));
        // Teamname und Turniername auslesen
        $i=0;
        $teams = $db->qry("
          SELECT
            teams.teamid,
            teams.name,
            t.name AS tname,
            t.teamplayer,
            t.tournamentid
          FROM %prefix%t2_teams AS teams
          LEFT JOIN %prefix%tournament_tournaments AS t ON (t.tournamentid = teams.tournamentid)
          WHERE
            (teams.leaderid = %int%)
            AND (t.teamplayer = 1)
            AND t.party_id=%int%", $auth["userid"], $party->party_id);
        if ($db->num_rows($teams) == 0) {
            $dsp->AddDoubleRow(t('Turnier'), t('Du hast dich zu noch keinem Einzelspieler-Turnier angemeldet'));
        } else {
            while ($team = $db->fetch_array($teams)) {
                $i++;
                $dsp->AddDoubleRow(t('Turnier') ." ". $i, "{$team["tname"]}" . HTML_NEWLINE . "<a href=\"index.php?mod=tournament2&action=teammgr&step=50&teamid={$team['teamid']}&tournamentid={$team['tournamentid']}\">".t('Teamdetails editieren')."</a>" . HTML_NEWLINE . "<a href=\"index.php?mod=tournament2&action=teammgr&step=30&teamid={$team['teamid']}\">".t('Mich abmelden')."</a>");
            }
        }
        $db->free_result($teams);

        $dsp->AddSingleRow(t('Teams, die du erstellt hast'));
        // Teamname und Turniername auslesen
        $i=0;
        $teams = $db->qry("
          SELECT
            teams.teamid,
            teams.name,
            t.name AS tname,
            t.tournamentid, t.teamplayer
          FROM %prefix%t2_teams AS teams
          LEFT JOIN %prefix%tournament_tournaments AS t ON t.tournamentid = teams.tournamentid
          WHERE
            (teams.leaderid = %int%)
            AND (t.teamplayer > 1)
            AND t.party_id=%int%", $auth["userid"], $party->party_id);
        if ($db->num_rows($teams) == 0) {
            $dsp->AddDoubleRow(t('Team'), t('Du hast noch keine Teams erstellt'));
        } else {
            while ($team = $db->fetch_array($teams)) {
                $i++;

                // Mitgliedernamen auslesen
                $members = $db->qry("
                  SELECT
                    users.username,
                    members.userid,
                    members.teamid
                  FROM %prefix%t2_teammembers AS members
                  LEFT JOIN %prefix%t2_teams AS teams ON members.teamid = teams.teamid
                  LEFT JOIN %prefix%user AS users ON members.userid = users.userid
                  WHERE
                    (teams.teamid = %int%)", $team['teamid']);

                $member_liste = "";
                $anz_memb = 0;
                while ($member = $db->fetch_array($members)) {
                    $anz_memb++;
                    $member_liste .= HTML_NEWLINE . "- ". $dsp->FetchUserIcon($member['userid'], $member["username"]) .' '. $dsp->FetchSpanButton(t('Rauswerfen'), "index.php?mod=tournament2&action=teammgr&step=20&teamid={$member['teamid']}&userid={$member['userid']}");
                }
                $db->free_result($members);
            
                $dsp->AddDoubleRow(t('Team') ." ". $i, "{$team["name"]} ({$team["tname"]}) (".t('Teamgröße').": ". ($anz_memb+1) ."/{$team["teamplayer"]}) $member_liste" . HTML_NEWLINE . "<a href=\"index.php?mod=tournament2&action=teammgr&step=40&teamid={$team['teamid']}&tournamentid={$team['tournamentid']}\">".t('Spieler hinzufügen')."</a>" . HTML_NEWLINE . "<a href=\"index.php?mod=tournament2&action=teammgr&step=50&teamid={$team['teamid']}&tournamentid={$team['tournamentid']}\">".t('Teamdetails editieren')."</a>" . HTML_NEWLINE . "<a href=\"index.php?mod=tournament2&action=teammgr&step=30&teamid={$team['teamid']}\">".t('Team abmelden')."</a>");
            }
        }
        $db->free_result($teams);


        $dsp->AddSingleRow(t('Teams, in denen du Mitglied bist'));
        $members = $db->qry("
          SELECT
            users.username,
            users.userid,
            teams.name,
            teams.teamid,
            t.name AS tname,
            t.teamplayer
          FROM %prefix%t2_teammembers AS members
          LEFT JOIN %prefix%t2_teams AS teams ON members.teamid = teams.teamid
          LEFT JOIN %prefix%user AS users ON teams.leaderid = users.userid
          LEFT JOIN %prefix%tournament_tournaments AS t ON teams.tournamentid = t.tournamentid
          WHERE
            (members.userid = %int%)
            AND t.party_id=%int%", $auth["userid"], $party->party_id);

        $member_liste = "";
        $anz_memb = 0;
        $i = 0;
        if ($db->num_rows($members) == 0) {
            $dsp->AddDoubleRow(t('Team'), t('Du bist noch in keinem Team Mitglied'));
        } else {
            while ($member = $db->fetch_array($members)) {
                $i++;

                // Mitgliedernamen auslesen
                $members2 = $db->qry("
                  SELECT
                    users.username,
                    members.userid,
                    members.teamid
                  FROM %prefix%t2_teammembers AS members
                  LEFT JOIN %prefix%t2_teams AS teams ON members.teamid = teams.teamid
                  LEFT JOIN %prefix%user AS users ON members.userid = users.userid
                  WHERE
                    (teams.teamid = %int%)", $member['teamid']);

                $member_liste = "";
                $anz_memb = 0;
                while ($member2 = $db->fetch_array($members2)) {
                    $anz_memb++;
                    $member_liste .= HTML_NEWLINE . "- ". $dsp->FetchUserIcon($member2['userid'], $member2["username"]);
                }
                $db->free_result($members2);

                $dsp->AddDoubleRow(t('Team') ." ". $i, "{$member["name"]} ({$member["tname"]}) (".t('Teamgröße').": ". ($anz_memb+1) ."/{$member["teamplayer"]})" . HTML_NEWLINE . t('Leiter').": ". $dsp->FetchUserIcon($member['userid'], $member["username"]) ." $member_liste" . HTML_NEWLINE . "<a href=\"index.php?mod=tournament2&action=teammgr&step=10&teamid={$member['teamid']}\">".t('Team verlassen')."</a>");
            }
        }
        $db->free_result($members);
        $dsp->AddSingleRow(t('Um ein neues Team zu erstellen / Dich zu einem Turnier anzumelden, wähle bitte in der Turnierübersicht das entsprechende Turnier aus und klicke am Ende der erscheinenden Detailansicht auf den Anmelde-Button.'));
        $dsp->AddBackButton("index.php?mod=tournament2", "tournament2/teammgr");
        break;
}
