<?php

$seat2 = new \LanSuite\Module\Seating\Seat2();
$mail = new \LanSuite\Module\Mail\Mail();

$tteam = new \LanSuite\Module\Tournament2\Team($mail, $seat2);

$tournamentid = $_GET["tournamentid"];

$tournament = $db->qry_first("
  SELECT
    name,
    teamplayer,
    over18,
    status,
    groupid,
    coins,
    wwcl_gameid,
    ngl_gamename,
    lgz_gamename,
    maxteams
  FROM %prefix%tournament_tournaments
  WHERE
    tournamentid = %int%", $tournamentid);

if ($auth["userid"] == "") {
    $auth["userid"] = 0;
}

$user = $db->qry_first("
  SELECT
    wwclid,
    wwclclanid,
    nglid,
    nglclanid,
    lgzid,
    lgzclanid
  FROM %prefix%user
  WHERE
    userid = %int%", $auth["userid"]);

if ($tteam->SignonCheck($tournamentid)) {
    switch ($_GET["step"]) {
        case 3:
            if (!$sec->locked("t_join")) {
                $error = array();

                // If joining an existing team
                if (($_POST['existing_team_name'] != "") and ($tournament['teamplayer'] > 1)) {
                    $success = $tteam->join($_POST["existing_team_name"], $auth["userid"], $_POST["password"]);

                // If creating a new team
                } else {
                    if ($tournament['teamplayer'] == 1) {
                        $_POST['team_name'] = "";
                    }

                    if ($_POST["set_password"] and $_POST["set_password"] != $_POST["set_password2"]) {
                        $error["set_password2"] = t('Die Passworteingaben stimmen nicht überein');
                    }
                    if ($_POST['team_name'] == "" and $tournament['teamplayer'] > 1) {
                        $error["team_name"] = t('Bitte gib einen Teamnamen ein, oder wähle ein vorhandenes Team aus');
                    }
                    if (count($error) == 0) {
                        $success = $tteam->create($_GET["tournamentid"], $auth["userid"], $_POST['team_name'], $_POST["set_password"], $_POST['team_comment'], "team_banner");
                    }
                }

                if (count($error) == 0 and $success) {
                    // Update-League-IDs
                    $tteam->UpdateLeagueIDs($auth["userid"], $_POST["wwclid"], $_POST["wwclclanid"], $_POST["nglid"], $_POST["nglclanid"], $_POST["lgzid"], $_POST["lgzclanid"]);
                    $func->confirmation(t('Du wurdest zum Turnier %1 erfolgreich hinzugefügt', $tournament["name"]), "index.php?mod=tournament2&action=details&tournamentid=$tournamentid");
                }
                $sec->lock("t_join");
            }

            if (count($error) > 0) {
                $_GET['step']--;
            }
            break;
    }

    switch ($_GET["step"]) {
        case 2:
            $sec->unlock("t_join");

            $dsp->NewContent(t('Zum Turnier %1 anmelden', $tournament['name']), t('Mit Hilfe des folgenden Formulars kannst du ein Team zu einem Turnier anmelden.'));
            $dsp->SetForm("index.php?mod=tournament2&action=join&step=3&tournamentid=$tournamentid", "", "", "multipart/form-data");

            if ($tournament['teamplayer'] == 1 or $tournament['blind_draw'] == 1) {
                $dsp->AddDoubleRow(t('Teamname'), $auth["username"]);
            } else {
                $dsp->AddSingleRow("<b>". t('Vorhandenem Team beitreten') ."</b>");

                // Vorhandene Teams
                $t_array = array("<option $selected value=\"\">-".t('Neues Team erstellen')."-</option>");
                $teams = $db->qry("SELECT teamid, name FROM %prefix%t2_teams WHERE tournamentid = %int%", $tournamentid);
                while ($team = $db->fetch_array($teams)) {
                    if ($_POST["existing_team_name"] == $team['teamid']) {
                        $selected = "selected";
                    }
                    array_push($t_array, "<option $selected value=\"{$team['teamid']}\">{$team['name']}</option>");
                }
                $db->free_result($teams);
                $dsp->AddDropDownFieldRow("existing_team_name", t('Team beitreten'), $t_array, "");
                $dsp->AddPasswordRow("password", t('Team-Passwort'), $_POST["password"], $error["password"]);

                // Neues Team
                $dsp->AddSingleRow("<b>". t('ODER: Neues Team anlegen') ."</b>");
                $dsp->AddTextFieldRow("team_name", t('Teamname'), $_POST["team_name"], $error["team_name"]);
                $dsp->AddPasswordRow("set_password", t('Team-Passwort festlegen'), $_POST["set_password"], $error["set_password"]);
                $dsp->AddPasswordRow("set_password2", t('Team-Passwort wiederholen'), $_POST["set_password2"], $error["set_password2"]);
            }

            $dsp->AddTextAreaPlusRow("team_comment", t('Bemerkung'), $_POST["team_comment"], "", "", "", 1);
            $dsp->AddFileSelectRow("team_banner", t('Team-Logo (max. 1MB)'), "", "", 1000000, 1);

            if ($tournament['wwcl_gameid'] > 0) {
                $dsp->AddTextFieldRow("wwclid", t('WWCL ID'), $user['wwclid'], "");
                if ($tournament['teamplayer'] > 1) {
                    $dsp->AddTextFieldRow("wwclclanid", t('WWCL Clan'), $user['wwclclanid'], "");
                }
            }
            if ($tournament['ngl_gamename'] != "") {
                $dsp->AddTextFieldRow("nglid", t('NGL ID'), $user['nglid'], "");
                if ($tournament['teamplayer'] > 1) {
                    $dsp->AddTextFieldRow("nglclanid", t('NGL Clan ID'), $user['nglclanid'], "");
                }
            }
            if ($tournament['lgz_gamename'] != "") {
                $dsp->AddDoubleRow(t('LGZ ID'), t('Falls temoräre ID gewünscht, bitte <b>0</b> eingeben und nach der Party die Verifizierungsmail bestätigen. Ein leeres Feld bedeutet, dass man außer Konkurenz teilnimt (John Doe)'));
                $dsp->AddTextFieldRow("lgzid", "", $user['lgzid'], "");
                if ($tournament['teamplayer'] > 1) {
                    $dsp->AddTextFieldRow("lgzclanid", t('LGZ Clan ID'), $user['lgzclanid'], "");
                }
            }

            $dsp->AddFormSubmitRow(t('Beitreten'));
            $dsp->AddBackButton("index.php?mod=tournament2&action=details&tournamentid=$tournamentid", "tournament2/join");
            break;
    }
}
