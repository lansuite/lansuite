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
    ngl_gamename,
    lgz_gamename,
    maxteams,
    blind_draw
  FROM %prefix%tournament_tournaments
  WHERE
    tournamentid = %int%", $tournamentid);

if ($auth["userid"] == "") {
    $auth["userid"] = 0;
}

$user = $db->qry_first("
  SELECT
    nglid,
    nglclanid,
    lgzid,
    lgzclanid
  FROM %prefix%user
  WHERE
    userid = %int%", $auth["userid"]);

if ($tteam->SignonCheck($tournamentid)) {
    $stepParameter = $_GET["step"] ?? 0;
    switch ($stepParameter) {
        case 3:
            $error = [];
            if (!$sec->locked("t_join")) {
                $error = array();

                // If joining an existing team
                $existingTeamNameParameter = $_POST['existing_team_name'] ?? '';
                if (($existingTeamNameParameter != "") && ($tournament['teamplayer'] > 1)) {
                    $success = $tteam->join($existingTeamNameParameter, $auth["userid"], $_POST["password"]);

                // If creating a new team
                } else {
                    if ($tournament['teamplayer'] == 1) {
                        $_POST['team_name'] = "";
                    }

                    $setPasswordParameter = $_POST["set_password"] ?? '';
                    $setPassword2Parameter = $_POST["set_password2"] ?? '';
                    if ($setPasswordParameter && $setPasswordParameter != $setPassword2Parameter) {
                        $error["set_password2"] = t('Die Passworteingaben stimmen nicht überein');
                    }
                    if ($_POST['team_name'] == "" and $tournament['teamplayer'] > 1) {
                        $error["team_name"] = t('Bitte gib einen Teamnamen ein, oder wähle ein vorhandenes Team aus');
                    }
                    if (count($error) == 0) {
                        $success = $tteam->create($_GET["tournamentid"], $auth["userid"], $_POST['team_name'], $setPasswordParameter, $_POST['team_comment'], "team_banner");
                    }
                }

                if (count($error) == 0 and $success) {
                    // Update-League-IDs
                    $nglidParameter = $_POST["nglid"] ?? '';
                    $nglclanidParameter = $_POST["nglclanid"] ?? '';
                    $lgzidParameter = $_POST["lgzid"] ?? '';
                    $lgzclanidParameter = $_POST["lgzclanid"] ?? '';
                    $tteam->UpdateLeagueIDs($auth["userid"], $nglidParameter, $nglclanidParameter, $lgzidParameter , $_POST["lgzclanid"]);
                    $func->confirmation(t('Du wurdest zum Turnier %1 erfolgreich hinzugefügt', $tournament["name"]), "index.php?mod=tournament2&action=details&tournamentid=$tournamentid");
                }
                $sec->lock("t_join");
            }

            if ((is_countable($error) ? count($error) : 0) > 0) {
                $_GET['step']--;
            }
            break;
    }

    $stepParameter = $_GET["step"] ?? 0;
    switch ($stepParameter) {
        case 2:
            $sec->unlock("t_join");

            $dsp->NewContent(t('Zum Turnier %1 anmelden', $tournament['name']), t('Mit Hilfe des folgenden Formulars kannst du ein Team zu einem Turnier anmelden.'));
            $dsp->SetForm("index.php?mod=tournament2&action=join&step=3&tournamentid=$tournamentid", "", "", "multipart/form-data");

            if ($tournament['teamplayer'] == 1 or $tournament['blind_draw'] == 1) {
                $dsp->AddDoubleRow(t('Teamname'), $auth["username"]);
            } else {
                $dsp->AddSingleRow("<b>". t('Vorhandenem Team beitreten') ."</b>");

                $existingTeamNameParameter = $_POST['existing_team_name'] ?? '';

                // Vorhandene Teams
                $t_array = array("<option $selected value=\"\">-".t('Neues Team erstellen')."-</option>");
                $teams = $db->qry("SELECT teamid, name FROM %prefix%t2_teams WHERE tournamentid = %int%", $tournamentid);
                while ($team = $db->fetch_array($teams)) {
                    if ($existingTeamNameParameter == $team['teamid']) {
                        $selected = "selected";
                    }
                    $t_array[] = "<option $selected value=\"{$team['teamid']}\">{$team['name']}</option>";
                }
                $db->free_result($teams);
                $dsp->AddDropDownFieldRow("existing_team_name", t('Team beitreten'), $t_array, "");

                $passwordParameter = $_POST["password"] ?? '';
                $passwordError = $error["password"] ?? '';
                $dsp->AddPasswordRow("password", t('Team-Passwort'), $passwordParameter, $passwordError);

                // Neues Team
                $dsp->AddSingleRow("<b>". t('ODER: Neues Team anlegen') ."</b>");

                $teamNameParameter = $_POST["team_name"] ?? '';
                $teamNameError = $error["team_name"] ?? '';
                $dsp->AddTextFieldRow("team_name", t('Teamname'), $teamNameParameter, $teamNameError);

                $setPasswordParameter = $_POST["set_password"] ?? '';
                $setPasswordError = $error["set_password"] ?? '';
                $dsp->AddPasswordRow("set_password", t('Team-Passwort festlegen'), $setPasswordParameter , $setPasswordError);

                $setPassword2Parameter = $_POST["set_password2"] ?? '';
                $setPassword2Error = $error["set_password2"] ?? '';
                $dsp->AddPasswordRow("set_password2", t('Team-Passwort wiederholen'), $setPassword2Parameter, $setPassword2Error);
            }

            $teamCommentParameter = $_POST["team_comment"] ?? '';
            $dsp->AddTextAreaPlusRow("team_comment", t('Bemerkung'), $teamCommentParameter, "", "", "", 1);
            $dsp->AddFileSelectRow("team_banner", t('Team-Logo (max. 1MB)'), "", "", 1_000_000, 1);

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
