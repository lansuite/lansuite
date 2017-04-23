<?php

include_once("modules/seating/class_seat.php");
$seat2 = new seat2();

include_once("modules/mail/class_mail.php");
$mail = new mail();

class team
{

    // To Check if one can still signon to a tournament
    public function SignonCheck($tid)
    {
        global $db, $func, $lang;

        if ($tid == "") {
            $func->error(t('Du musst zuerst ein Turnier auswählen!'));
            return false;
        }

        $t = $db->qry_first("SELECT status, teamplayer, maxteams, blind_draw FROM %prefix%tournament_tournaments WHERE tournamentid = %int%", $tid);

        if ($t["teamplayer"] == 1 or $t["blind_draw"]) {
            $c_teams = $db->qry_first("SELECT COUNT(*) AS teams FROM %prefix%t2_teams WHERE tournamentid = %int%", $tid);
            $completed_teams = $c_teams["teams"];
            $waiting_teams = 0;
        } else {
            $waiting_teams = 0;
            $completed_teams = 0;
            $c_teams = $db->qry("SELECT teamid FROM %prefix%t2_teams WHERE (tournamentid = %int%)", $tid);
            while ($c_team = $db->fetch_array($c_teams)) {
                $c_member = $db->qry_first("SELECT COUNT(*) AS members
     FROM %prefix%t2_teammembers
     WHERE (teamid = %int%)
     GROUP BY teamid
     ", $c_team["teamid"]);

                if (($c_member["members"] + 1) < $t["teamplayer"]) {
                    $waiting_teams++;
                } else {
                    $completed_teams++;
                }
            }
        }
        if ($t["blind_draw"]) {
            $completed_teams = floor($completed_teams / $t["teamplayer"]);
        }

        // Is the tournament finished?
        if ($t["status"] != "open") {
            $func->information(t('Dieses Turnier befindet sich momentan nicht in der Anmeldephase!'));
        } // Is the tournament allready full?
        elseif ($completed_teams >= $t["maxteams"]) {
            $func->information(t('Es haben sich bereits %1 von %2 Teams zu diesem Turnier angemeldet. Das Turnier ist damit ausgebucht.', $completed_teams, $t["maxteams"]));
        } // Everything fine
        else {
            return true;
        }

        return false;
    }


    // To Check if a user may signon to a tournament
    public function SignonCheckUser($tid, $userid)
    {
        global $db, $lang, $func, $party, $cfg, $seat2;

        $t = $db->qry_first("SELECT groupid, maxteams, over18, status, coins FROM %prefix%tournament_tournaments WHERE tournamentid = %int%", $tid);
        $user = $db->qry_first("SELECT p.paid, u.username
   FROM %prefix%user AS u
   LEFT JOIN %prefix%party_user AS p ON u.userid = p.user_id
   WHERE u.userid = %int% AND party_id=%int%
   ", $userid, $party->party_id);

        $team = $db->qry_first("SELECT 1 AS found FROM %prefix%t2_teams WHERE (tournamentid = %int%) AND (leaderid = %int%)", $tid, $userid);
        $teammember = $db->qry_first("SELECT 1 AS found FROM %prefix%t2_teammembers WHERE (tournamentid = %int%) AND (userid = %int%)", $tid, $userid);

        $in_group = $db->qry_first("SELECT 1 AS found
   FROM %prefix%t2_teams AS team
   LEFT JOIN %prefix%tournament_tournaments AS t ON (t.tournamentid = team.tournamentid)
   WHERE (t.groupid = %int%) AND (t.groupid != 0)  AND (t.party_id=%int%) AND (team.leaderid = %int%)
   ", $t["groupid"], $party->party_id, $userid);
        
        $memb_in_group = $db->qry_first("SELECT 1 AS found
   FROM %prefix%t2_teammembers AS members
   LEFT JOIN %prefix%tournament_tournaments AS t ON (t.tournamentid = members.tournamentid)
   WHERE (t.groupid = %int%) AND (t.groupid != 0)  AND (t.party_id=%int%) AND (userid = %int%)
   ", $t["groupid"], $party->party_id, $userid);

        $over_18_error = 0;
        if ($t["over18"] == 1 and $seat2->U18Block($userid, "u")) {
            $over_18_error = 1;
        }

        $team_coin = $db->qry_first("SELECT SUM(t.coins) AS t_coins
   FROM %prefix%tournament_tournaments AS t
   LEFT JOIN %prefix%t2_teams AS teams ON t.tournamentid = teams.tournamentid
   WHERE teams.leaderid = %int% AND t.party_id = %int%
   GROUP BY teams.leaderid
   ", $userid, $party->party_id);

        $member_coin = $db->qry_first("SELECT SUM(t.coins) AS t_coins
   FROM %prefix%tournament_tournaments AS t
   LEFT JOIN %prefix%t2_teammembers AS members ON t.tournamentid = members.tournamentid
   WHERE members.userid = %int% AND t.party_id = %int%
   GROUP BY members.userid
   ", $userid, $party->party_id);


        // Is the user allready signed on to this tournament?
        if ($team["found"]) {
            $func->information(t('%1 ist bereits zu diesem Turnier angemeldet!', $user["username"]));
        } // Is the user member of a team, allready signed on to this tournament?
        elseif ($teammember["found"] != "") {
            $func->information(t('%1 ist bereits Mitglied eines Teams, dass sich zu diesem Turnier angemeldet hat!', $user["username"]));
        } // Is the user allready signed on to a tournament in the same group as this tournament?
        elseif ($in_group["found"] != "") {
            $func->information(t('%1 ist bereits zu einem Turnier angemeldet, welches der gleichen Gruppe angehört!', $user["username"]));
        } // Is the user member of a team, allready signed on to a tournament in the same group as this tournament?
        elseif ($memb_in_group["found"] != "") {
            $func->information(t('%1 ist bereits Mitglied eines Teams, dass sich zu einem Turnier der gleichen Gruppe angemeldet hat!', $user["username"]));
        } // Has the user paid?
        elseif (!$user["paid"]) {
            $func->information(t('%1 muss erst für diese Party bezahlen, um sich an einem Turnier anmelden zu können!', $user["username"]));
        } // Is the user 18 (only for 18+ tournaments)?
        elseif ($over_18_error) {
            $func->information(t('%1 kann diesem Turnier nicht beitreten. In diesem Turnier dürfen nur Benutzer mitspielen, die <b>nicht</b> in einem Unter-18-Block sitzen', $user["username"]));
        } // Are enough coins left to afford this tournament
        elseif (($cfg["t_coins"] - $team_coin["t_coins"] - $member_coin["t_coins"] - $t["coins"]) < 0) {
            $func->information(t('%1 besitzt nicht genügend Coins um an diesem Turnier teilnehmen zu können!', $user["username"]));
        } // Everything fine
        else {
            return true;
        }

        return false;
    }


    // To join an existing Team
    public function join($teamid, $userid, $password = null)
    {
        global $db, $auth, $lang, $func, $mail;

        if ($teamid == "") {
            $func->error(t('Du hast kein Team ausgeählt!'));
            return false;
        } elseif ($userid == "") {
            $func->error(t('Du hast keinen Benutzer ausgewählt!'));
            return false;
        } else {
            $team = $db->qry_first("SELECT team.tournamentid, team.password, team.leaderid, team.name AS teamname, t.name AS tname, t.teamplayer
    FROM %prefix%t2_teams AS team
    LEFT JOIN %prefix%tournament_tournaments AS t ON team.tournamentid = t.tournamentid
    WHERE team.teamid = %int%
    ", $teamid);

      // Check password, if set and if acction is not performed, by teamadmin or ls-admin
            if (($auth['userid'] != $team['leaderid']) and ($auth['type'] <= 1) and ($team['password'] != '') and (md5($password) != $team['password'])) {
                $func->information(t('Das eingegebene Kennwort ist nicht korrekt'));
                return false;

                  // May one still signon for this tournament?
            } elseif ($this->SignonCheckUser($team["tournamentid"], $userid)) {
                $member_anz = $db->qry_first("SELECT COUNT(*) AS members FROM %prefix%t2_teammembers WHERE teamid = %int% GROUP BY teamid", $teamid);

                // Isn't the team full yet?
                if ($team["teamplayer"] <= ($member_anz["members"] + 1)) {
                    $func->information(t('Das gewählte Team ist bereits voll!'));
                    return false;

                // Everything Okay! -> Insert!
                } else {
                    $db->qry("INSERT INTO %prefix%t2_teammembers 
      SET tournamentid = %int%,
      userid = %int%,
      teamid = %int%
      ", $team["tournamentid"], $userid, $teamid);

                    $mail->create_sys_mail($userid, t_no_html('Du wurdest dem Team %1 im Turnier %2 hinzugefügt', $team["teamname"], $team["tname"]), t_no_html('Der Ersteller des Teams <b>%1</b> hat dich in sein Team im Turnier <b>%2</b> aufgenommen.', $team["teamname"], $team["tname"]));

                    $func->log_event(t('Der Benutzer %1 ist dem Team %2 im Turnier %3 beigetreten', $auth["username"], $team["teamname"], $team["tname"]), 1, t('Turnier Teamverwaltung'));
                }
            } else {
                return false;
            }
        }
        return true;
    }


    // To create a new Team
    public function create($tournamentid, $leaderid, $name = null, $password = null, $comment = null, $banner = null)
    {
        global $db, $auth, $lang, $func;

        if ($this->SignonCheck($tournamentid) and $this->SignonCheckUser($tournamentid, $leaderid)) {
            $t = $db->qry_first("SELECT name, teamplayer, maxteams FROM %prefix%tournament_tournaments WHERE tournamentid = %int%", $tournamentid);

            if ($t["teamplayer"] == 1 or $t["blind_draw"]) {
                $c_teams = $db->qry_first("SELECT COUNT(*) AS teams FROM %prefix%t2_teams WHERE (tournamentid = %int%) GROUP BY teamid", $tournamentid);
                $completed_teams = $c_teams["teams"];
                $waiting_teams = 0;
            } else {
                $waiting_teams = 0;
                $completed_teams = 0;
                $c_teams = $db->qry("SELECT teamid FROM %prefix%t2_teams WHERE (tournamentid = %int%)", $tournamentid);
                while ($c_team = $db->fetch_array($c_teams)) {
                    $c_member = $db->qry_first("SELECT COUNT(*) AS members
      FROM %prefix%t2_teammembers
      WHERE (teamid = %int%)
      GROUP BY teamid
      ", $c_team["teamid"]);

                    if (($c_member["members"] + 1) < $t["teamplayer"]) {
                        $waiting_teams++;
                    } else {
                        $completed_teams++;
                    }
                }
            }
            if ($t["blind_draw"]) {
                $completed_teams = floor($completed_teams / $t["teamplayer"]);
            }

            if (($completed_teams + $waiting_teams) >= $t["maxteams"]) {
                $func->error(t('Es haben sich bereits %1 von %2 Teams zu diesem Turnier angemeldet. Es gibt jedoch noch in %3 der angemeldeten Teams freie Plätze, dazu bitte eines der Teams mit den freien Plätzen auswählen und beitreten', $completed_teams + $waiting_teams, $t["maxteams"], $waiting_teams));
                return false;
            } else {
                if ($_FILES[$banner]["tmp_name"] != "") {
                    $func->FileUpload("team_banner", "ext_inc/team_banners/");
                }

                if (!$name) {
                    $user = $db->qry_first("SELECT username FROM %prefix%user WHERE userid = %int%", $leaderid);
                    $name = $func->escape_sql($user["username"]);
                }
                $query = $db->qry("INSERT INTO %prefix%t2_teams 
     SET tournamentid = %int%,
     name = %string%,
     leaderid = %int%,
     comment = %string%,
     banner = %string%,
     password = %string%
     ", $tournamentid, $name, $leaderid, $comment, $_FILES[$banner]["name"], md5($password));

                $func->log_event(t('Der Benutzer %1 hat sich zum Turnier %2 angemeldet', $auth["username"], $t["name"]), 1, t('Turnier Teamverwaltung'));
            }
        } else {
            return false;
        }

        return true;
    }


    // Edits the details of a team
    public function edit($teamid, $name = null, $password = null, $comment = null, $banner = null)
    {
        global $db, $auth, $lang, $func;

        $t = $db->qry_first("SELECT t.name, t.teamplayer FROM %prefix%tournament_tournaments AS t
   LEFT JOIN %prefix%t2_teams AS team ON t.tournamentid = team.tournamentid
   WHERE team.teamid = %int%
   ", $teamid);

        // Upload Banner
        if ($_FILES[$banner]["tmp_name"] != "") {
            $func->FileUpload("team_banner", "ext_inc/team_banners/");
            $db->qry("UPDATE %prefix%t2_teams SET banner = %string% WHERE teamid = %int%", $_FILES["team_banner"]["name"], $_GET["teamid"]);
        }

        if ($t['teamplayer'] == 1) {
            $name = $auth["username"];
        }

        // Set Password
        if ($password != "") {
            $db->qry("UPDATE %prefix%t2_teams SET password = %string% WHERE teamid = %int%", md5($password), $_GET["teamid"]);
        }

        $db->qry("UPDATE %prefix%t2_teams 
   SET name = %string%,
   comment = %string%
   WHERE teamid = %int%
   ", $name, $comment, $_GET["teamid"]);
        $func->log_event(t('Das Team %1 im Turnier %2 hat seine Daten editiert', $_POST['team_name'], $t["name"]), 1, t('Turnier Teamverwaltung'));

        $this->UpdateLeagueIDs($auth["userid"], $_POST["wwclid"], $_POST["wwclclanid"], $_POST["nglid"], $_POST["nglclannid"], $_POST["lgzid"], $_POST["lgzclannid"]);

        return true;
    }


    // Deletes a whole team
    public function delete($teamid)
    {
        global $db, $lang, $func, $mail;

        if ($teamid == "") {
            $func->error(t('Du hast kein Team ausgeählt!'));
            return false;
        }

        $team = $db->qry_first("SELECT t.status, t.name AS tname, team.name AS teamname, team.leaderid
   FROM %prefix%tournament_tournaments AS t
   LEFT JOIN %prefix%t2_teams AS team ON (t.tournamentid = team.tournamentid)
   WHERE (teamid = %int%)
   ", $teamid);

        // No delete if tournament is generated
        if ($team['status'] != "open") {
            $func->information(t('Dieses Turnier wird bereits gespielt![br]Ein Abmelden ist daher nicht mehr möglich.'));
            return false;
        }

        // Send Mail to Teammebers
        $members = $db->qry("SELECT userid FROM %prefix%t2_teammembers WHERE teamid = %int%", $teamid);
        while ($member = $db->fetch_array($members)) {
            $mail->create_sys_mail($member['userid'], t_no_html('Dein Team im Turnier %1 wurde aufgelöst', $team['tname']), t_no_html('Der Ersteller des Teams hat soeben sein Team aufgelöst. Dies bedeutet, dass du nun nicht mehr zu dem Turnier %1 angemeldet bist.', $team['tname']));
        }
        $db->free_result($members);

        // Perform Action
        $db->qry("DELETE FROM %prefix%t2_teams WHERE teamid = %int%", $teamid);
        $db->qry("DELETE FROM %prefix%t2_teammembers WHERE teamid = %int%", $teamid);

        $func->log_event(t('Das Team %1 wurde aufgelöst', $team['teamname']), 1, t('Turnier Teamverwaltung'));

        return true;
    }


    // Kicks one player out of the team
    public function kick($teamid, $userid)
    {
        global $db, $lang, $func, $mail;

        if ($teamid == "") {
            $func->error(t('Du hast kein Team ausgeählt!'));
            return false;
        }
        if ($userid == "") {
            $func->error(t('Du hast keinen Benutzer ausgewählt!'));
            return false;
        }

        // Select Output information
        $t = $db->qry_first("SELECT t.name
   FROM %prefix%tournament_tournaments AS t
   LEFT JOIN %prefix%t2_teammembers AS m ON t.tournamentid = m.tournamentid
   WHERE (m.userid = %int%) AND (m.teamid = %int%)
   ", $userid, $teamid);
        $user = $db->qry_first("SELECT username FROM %prefix%user WHERE userid = %int%", $userid);
        $team = $db->qry_first("SELECT name FROM %prefix%t2_teams WHERE teamid = %int%", $teamid);

        // Is the tournament finished?
        if ($t["status"] == "closed") {
            $func->information(t('Dieses Turnier läuft bereits!'));
            return false;
        }
        // Perform Action
        $db->qry("DELETE FROM %prefix%t2_teammembers WHERE (userid = %int%) AND (teamid = %int%)", $userid, $teamid);

        // Create Outputs
        $mail->create_sys_mail($userid, t_no_html('Du wurdest im Turnier %1 aus deinem Team geworfen', $t["name"]), str_replace("%NAME%", $t["name"], t_no_html('Der Ersteller dieses Teams hat dich soeben aus seinem Team entfernt. Dies bedeutet, dass du nun nicht mehr zu dem Turnier \'%NAME%\' angemeldet bist.')));
        $func->log_event(t('Der Benutzer %1 wurde vom Teamadmin aus dem Team %2 geworfen', $user["username"], $team['name']), 1, t('Turnier Teamverwaltung'));

        return true;
    }


    // To set new League IDs
    public function UpdateLeagueIDs($userid, $wwclid = null, $wwclclanid = null, $nglid = null, $nglclanid = null, $lgzid = null, $lgzclanid = null)
    {
        global $db, $auth;

        if ($wwclid != "") {
            $db->qry('UPDATE %prefix%user SET wwclid = %string% WHERE userid = %int%', $wwclid, $userid);
        }
        if ($wwclclanid != "") {
            $db->qry('UPDATE %prefix%user SET wwclclanid = %string% WHERE userid = %int%', $wwclclanid, $userid);
        }
        if ($nglid != "") {
            $db->qry('UPDATE %prefix%user SET nglid = %string% WHERE userid = %int%', $nglid, $userid);
        }
        if ($nglclanid != "") {
            $db->qry('UPDATE %prefix%user SET nglclanid = %string% WHERE userid = %int%', $nglclanid, $userid);
        }
        if ($lgzid != "") {
            $db->qry('UPDATE %prefix%user SET lgzid = %string% WHERE userid = %int%', $lgzid, $userid);
        }
        if ($lgzclanid != "") {
            $db->qry('UPDATE %prefix%user SET lgzclanid = %string% WHERE userid = %int%', $lgzclanid, $userid);
        }
    }
}
