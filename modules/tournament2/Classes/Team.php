<?php

namespace LanSuite\Module\Tournament2;

use LanSuite\PasswordHash;

class Team
{

    private ?\LanSuite\Module\Mail\Mail $mail = null;

    private ?\LanSuite\Module\Seating\Seat2 $seating = null;

    public function __construct(\LanSuite\Module\Mail\Mail $mail, \LanSuite\Module\Seating\Seat2 $seating)
    {
        $this->mail = $mail;
        $this->seating = $seating;
    }

    /**
     * Check if one can still signon to a tournament
     *
     * @param int $tid
     * @return bool
     */
    public function SignonCheck($tid)
    {
        global $db, $database, $func;

        if ($tid == "") {
            $func->error(t('Du musst zuerst ein Turnier auswählen!'));
            return false;
        }

        $t = $database->queryWithOnlyFirstRow("
          SELECT
            status,
            teamplayer,
            maxteams,
            blind_draw
          FROM %prefix%tournament_tournaments
          WHERE tournamentid = ?", [$tid]);

        if ($t["teamplayer"] == 1 or $t["blind_draw"]) {
            $c_teams = $database->queryWithOnlyFirstRow("SELECT COUNT(*) AS teams FROM %prefix%t2_teams WHERE tournamentid = ?", [$tid]);
            $completed_teams = $c_teams["teams"];
        } else {
            $waiting_teams = 0;
            $completed_teams = 0;
            $c_teams = $db->qry("SELECT teamid FROM %prefix%t2_teams WHERE (tournamentid = %int%)", $tid);
            while ($c_team = $db->fetch_array($c_teams)) {
                $c_member = $database->queryWithOnlyFirstRow("
                  SELECT
                    COUNT(*) AS members
                  FROM %prefix%t2_teammembers
                  WHERE teamid = ?
                  GROUP BY teamid", [$c_team["teamid"]]);
                $memberCount = $c_member["members"] ?? 0;
                if (($memberCount + 1) < $t["teamplayer"]) {
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
        // Is the tournament already full?
        } elseif ($completed_teams >= $t["maxteams"]) {
            $func->information(t('Es haben sich bereits %1 von %2 Teams zu diesem Turnier angemeldet. Das Turnier ist damit ausgebucht.', $completed_teams, $t["maxteams"]));
        // Everything fine
        } else {
            return true;
        }

        return false;
    }

    /**
     * Check if a user may signon to a tournament
     *
     * @param $tid
     * @param $userid
     * @return bool
     */
    public function SignonCheckUser($tid, $userid)
    {
        global $db, $database, $func, $party, $cfg;

        $t = $database->queryWithOnlyFirstRow("
          SELECT
            groupid,
            maxteams,
            over18,
            status,
            coins
          FROM %prefix%tournament_tournaments
          WHERE tournamentid = ?", [$tid]);

        $user = $database->queryWithOnlyFirstRow("
          SELECT
            p.paid,
            u.username
          FROM %prefix%user AS u
          LEFT JOIN %prefix%party_user AS p ON u.userid = p.user_id
          WHERE
            u.userid = ?
            AND party_id = ?", [$userid, $party->party_id]);

        $team = $database->queryWithOnlyFirstRow("
          SELECT
            1 AS found
          FROM %prefix%t2_teams
          WHERE
            tournamentid = ?
            AND leaderid = ?", [$tid, $userid]);

        $teammember = $database->queryWithOnlyFirstRow("
          SELECT
            1 AS found
          FROM %prefix%t2_teammembers
          WHERE
            tournamentid = ?
            AND userid = ?", [$tid, $userid]);

        $in_group = $database->queryWithOnlyFirstRow("
          SELECT
            1 AS found
          FROM %prefix%t2_teams AS team
          LEFT JOIN %prefix%tournament_tournaments AS t ON (t.tournamentid = team.tournamentid)
          WHERE
            t.groupid = ?
            AND t.groupid != 0
            AND t.party_id = ?
            AND team.leaderid = ?", [$t["groupid"], $party->party_id, $userid]);
        
        $memb_in_group = $database->queryWithOnlyFirstRow("
          SELECT 1 AS found
          FROM %prefix%t2_teammembers AS members
          LEFT JOIN %prefix%tournament_tournaments AS t ON (t.tournamentid = members.tournamentid)
          WHERE
            t.groupid = ?
            AND t.groupid != 0
            AND t.party_id = ?
            AND userid = ?", [$t["groupid"], $party->party_id, $userid]);

        $over_18_error = 0;
        if ($t["over18"] == 1 && $this->seating->U18Block($userid, "u")) {
            $over_18_error = 1;
        }

        $team_coin = $database->queryWithOnlyFirstRow("
          SELECT
            SUM(t.coins) AS t_coins
          FROM %prefix%tournament_tournaments AS t
          LEFT JOIN %prefix%t2_teams AS teams ON t.tournamentid = teams.tournamentid
          WHERE
            teams.leaderid = ?
            AND t.party_id = ?
          GROUP BY teams.leaderid", [$userid, $party->party_id]);
        $sumTeamCoins = $team_coin["t_coins"] ?? 0;

        $member_coin = $database->queryWithOnlyFirstRow("
          SELECT
            SUM(t.coins) AS t_coins
          FROM %prefix%tournament_tournaments AS t
          LEFT JOIN %prefix%t2_teammembers AS members ON t.tournamentid = members.tournamentid
          WHERE
            members.userid = ?
            AND t.party_id = ?
          GROUP BY members.userid", [$userid, $party->party_id]);
        $sumMemberCoins = $member_coin["t_coins"] ?? 0;

        // Is the user already signed on to this tournament?
        if (is_array($team) && $team["found"]) {
            $func->information(t('%1 ist bereits zu diesem Turnier angemeldet!', $user["username"]));

        // Is the user member of a team, allready signed on to this tournament?
        } elseif (is_array($teammember) && $teammember["found"] != "") {
            $func->information(t('%1 ist bereits Mitglied eines Teams, dass sich zu diesem Turnier angemeldet hat!', $user["username"]));

        // Is the user allready signed on to a tournament in the same group as this tournament?
        } elseif (is_array($in_group) && $in_group["found"] != "") {
            $func->information(t('%1 ist bereits zu einem Turnier angemeldet, welches der gleichen Gruppe angehört!', $user["username"]));

        // Is the user member of a team, allready signed on to a tournament in the same group as this tournament?
        } elseif (is_array($memb_in_group) && $memb_in_group["found"] != "") {
            $func->information(t('%1 ist bereits Mitglied eines Teams, dass sich zu einem Turnier der gleichen Gruppe angemeldet hat!', $user["username"]));

        // Has the user paid?
        } elseif (!$user["paid"]) {
            $func->information(t('%1 muss erst für diese Party bezahlen, um sich an einem Turnier anmelden zu können!', $user["username"]));

        // Is the user 18 (only for 18+ tournaments)?
        } elseif ($over_18_error) {
            $func->information(t('%1 kann diesem Turnier nicht beitreten. In diesem Turnier dürfen nur Benutzer mitspielen, die <b>nicht</b> in einem Unter-18-Block sitzen', $user["username"]));

        // Are enough coins left to afford this tournament
        } elseif (($cfg["t_coins"] - intval($sumTeamCoins) - intval($sumMemberCoins) - intval($t["coins"])) < 0) {
            $func->information(t('%1 besitzt nicht genügend Coins um an diesem Turnier teilnehmen zu können!', $user["username"]));

        // Everything fine
        } else {
            return true;
        }

        return false;
    }

    /**
     * To join an existing Team
     *
     * @param int $teamid
     * @param int $userid
     * @param string $password
     * @return bool
     */
    public function join($teamid, $userid, $password = null)
    {
        global $db, $database, $auth, $func;

        if ($teamid == "") {
            $func->error(t('Du hast kein Team ausgeählt!'));
            return false;
        } elseif ($userid == "") {
            $func->error(t('Du hast keinen Benutzer ausgewählt!'));
            return false;
        } else {
            $team = $database->queryWithOnlyFirstRow("
              SELECT
                team.tournamentid,
                team.password,
                team.leaderid,
                team.name AS teamname,
                t.name AS tname,
                t.teamplayer
              FROM %prefix%t2_teams AS team
              LEFT JOIN %prefix%tournament_tournaments AS t ON team.tournamentid = t.tournamentid
              WHERE
                team.teamid = ?", [$teamid]);

            // Check password, if set and if acction is not performed, by teamadmin or ls-admin
            if (($auth['userid'] != $team['leaderid']) and ($auth['type'] <= \LS_AUTH_TYPE_USER) and ($team['password'] != '') and !PasswordHash::verify($password, $team['password'])) {
                $func->information(t('Das eingegebene Kennwort ist nicht korrekt'));
                return false;

            // May one still signon for this tournament?
            } elseif ($this->SignonCheckUser($team["tournamentid"], $userid)) {
                $member_anz = $database->queryWithOnlyFirstRow("
                  SELECT
                    COUNT(*) AS members
                  FROM %prefix%t2_teammembers
                  WHERE
                    teamid = ?
                  GROUP BY teamid", [$teamid]);
                $memberCount = $member_anz["members"] ?? 0;

                // Isn't the team full yet?
                if ($team["teamplayer"] <= ($memberCount + 1)) {
                    $func->information(t('Das gewählte Team ist bereits voll!'));
                    return false;

                // Everything Okay! -> Insert!
                } else {
                    $database->query("
                      INSERT INTO %prefix%t2_teammembers 
                      SET
                        tournamentid = ?,
                        userid = ?,
                        teamid = ?", [$team["tournamentid"], $userid, $teamid]);

                    $this->mail->create_sys_mail($userid, t_no_html('Du wurdest dem Team %1 im Turnier %2 hinzugefügt', $team["teamname"], $team["tname"]), t_no_html('Der Ersteller des Teams <b>%1</b> hat dich in sein Team im Turnier <b>%2</b> aufgenommen.', $team["teamname"], $team["tname"]));
                    $func->log_event(t('Der Benutzer %1 ist dem Team %2 im Turnier %3 beigetreten', $auth["username"], $team["teamname"], $team["tname"]), 1, t('Turnier Teamverwaltung'));
                }
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * Create a new team
     *
     * @param int $tournamentid
     * @param int $leaderid
     * @param string $name
     * @param string $password
     * @param string $comment
     * @param string $banner
     * @return bool
     */
    public function create($tournamentid, $leaderid, $name = null, $password = null, $comment = null, $banner = null)
    {
        global $db, $database, $auth, $func;

        if ($this->SignonCheck($tournamentid) and $this->SignonCheckUser($tournamentid, $leaderid)) {
            $t = $database->queryWithOnlyFirstRow("
              SELECT
                name,
                teamplayer,
                maxteams,
                blind_draw
              FROM %prefix%tournament_tournaments
              WHERE
                tournamentid = ?", [$tournamentid]);

            if ($t["teamplayer"] == 1 || $t["blind_draw"]) {
                $c_teams = $database->queryWithOnlyFirstRow("
                  SELECT
                    COUNT(*) AS teams
                  FROM %prefix%t2_teams
                  WHERE
                    tournamentid = ?
                  GROUP BY teamid", [$tournamentid]);
                $completed_teams = 0;
                if (is_array($c_teams)) {
                  $completed_teams = intval($c_teams["teams"]);
                }
                $waiting_teams = 0;
            } else {
                $waiting_teams = 0;
                $completed_teams = 0;
                $c_teams = $db->qry("SELECT teamid FROM %prefix%t2_teams WHERE (tournamentid = %int%)", $tournamentid);
                while ($c_team = $db->fetch_array($c_teams)) {
                    $c_member = $database->queryWithOnlyFirstRow("
                      SELECT
                        COUNT(*) AS members
                      FROM %prefix%t2_teammembers
                      WHERE teamid = ?
                      GROUP BY teamid", [$c_team["teamid"]]);

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
                    $user = $database->queryWithOnlyFirstRow("SELECT username FROM %prefix%user WHERE userid = ?", [$leaderid]);
                    $name = $user["username"];
                }
                $database->query("
                  INSERT INTO %prefix%t2_teams 
                  SET
                    tournamentid = ?,
                    name = ?,
                    leaderid = ?,
                    comment = ?,
                    banner = ?,
                    password = ?", [$tournamentid, $name, $leaderid, $comment, $_FILES[$banner]["name"], PasswordHash::hash($password)]);

                $func->log_event(t('Der Benutzer %1 hat sich zum Turnier %2 angemeldet', $auth["username"], $t["name"]), 1, t('Turnier Teamverwaltung'));
            }
        } else {
            return false;
        }

        return true;
    }

    /**
     * Edits the details of a team
     *
     * @param int $teamid
     * @param string $name
     * @param string $password
     * @param string $comment
     * @param string $banner
     * @return bool
     */
    public function edit($teamid, $name = null, $password = null, $comment = null, $banner = null)
    {
        global $db, $database, $auth, $func;

        $t = $database->queryWithOnlyFirstRow("
          SELECT
            t.name,
            t.teamplayer
          FROM %prefix%tournament_tournaments AS t
          LEFT JOIN %prefix%t2_teams AS team ON t.tournamentid = team.tournamentid
          WHERE team.teamid = ?", [$teamid]);

        // Upload Banner
        if ($_FILES[$banner]["tmp_name"] != "") {
            $func->FileUpload("team_banner", "ext_inc/team_banners/");
            $database->query("UPDATE %prefix%t2_teams SET banner = ? WHERE teamid = ?", [$_FILES["team_banner"]["name"], $_GET["teamid"]]);
        }

        if ($t['teamplayer'] == 1) {
            $name = $auth["username"];
        }

        // Set Password
        if ($password != "") {
          $database->query("UPDATE %prefix%t2_teams SET password = ? WHERE teamid = ?", [PasswordHash::hash($password), $_GET["teamid"]]);
        }

        $database->query("
          UPDATE %prefix%t2_teams 
          SET
            name = ?,
            comment = ?
          WHERE teamid = ?", [$name, $comment, $_GET["teamid"]]);
        $func->log_event(t('Das Team %1 im Turnier %2 hat seine Daten editiert', $_POST['team_name'], $t["name"]), 1, t('Turnier Teamverwaltung'));

        return true;
    }

    /**
     * Deletes a whole team
     *
     * @param int $teamid
     * @return bool
     */
    public function delete($teamid)
    {
        global $db, $database, $func;

        if ($teamid == "") {
            $func->error(t('Du hast kein Team ausgeählt!'));
            return false;
        }

        $team = $database->queryWithOnlyFirstRow("
          SELECT
            t.status,
            t.name AS tname,
            team.name AS teamname,
            team.leaderid
          FROM %prefix%tournament_tournaments AS t
          LEFT JOIN %prefix%t2_teams AS team ON (t.tournamentid = team.tournamentid)
          WHERE teamid = ?", [$teamid]);

        // No delete if tournament is generated
        if ($team['status'] != "open") {
            $func->information(t('Dieses Turnier wird bereits gespielt![br]Ein Abmelden ist daher nicht mehr möglich.'));
            return false;
        }

        // Send Mail to Teammebers
        $members = $db->qry("SELECT userid FROM %prefix%t2_teammembers WHERE teamid = %int%", $teamid);
        while ($member = $db->fetch_array($members)) {
            $this->mail->create_sys_mail($member['userid'], t_no_html('Dein Team im Turnier %1 wurde aufgelöst', $team['tname']), t_no_html('Der Ersteller des Teams hat soeben sein Team aufgelöst. Dies bedeutet, dass du nun nicht mehr zu dem Turnier %1 angemeldet bist.', $team['tname']));
        }
        $db->free_result($members);

        // Perform Action
        $database->query("DELETE FROM %prefix%t2_teams WHERE teamid = ?", [$teamid]);
        $database->query("DELETE FROM %prefix%t2_teammembers WHERE teamid = ?", [$teamid]);

        $func->log_event(t('Das Team %1 wurde aufgelöst', $team['teamname']), 1, t('Turnier Teamverwaltung'));

        return true;
    }

    /**
     * Kicks one player out of the team
     *
     * @param int $teamid
     * @param int $userid
     * @return bool
     */
    public function kick($teamid, $userid)
    {
        global $db, $database, $func;

        if ($teamid == "") {
            $func->error(t('Du hast kein Team ausgeählt!'));
            return false;
        }
        if ($userid == "") {
            $func->error(t('Du hast keinen Benutzer ausgewählt!'));
            return false;
        }

        // Select Output information
        $t = $database->queryWithOnlyFirstRow("
          SELECT t.name
          FROM %prefix%tournament_tournaments AS t
          LEFT JOIN %prefix%t2_teammembers AS m ON t.tournamentid = m.tournamentid
          WHERE
            m.userid = ?
            AND m.teamid = ?", [$userid, $teamid]);
        $user = $database->queryWithOnlyFirstRow("SELECT username FROM %prefix%user WHERE userid = ?", [$userid]);
        $team = $database->queryWithOnlyFirstRow("SELECT name FROM %prefix%t2_teams WHERE teamid = ?", [$teamid]);

        // Is the tournament finished?
        if ($t["status"] == "closed") {
            $func->information(t('Dieses Turnier läuft bereits!'));
            return false;
        }
        // Perform Action
        $database->query("DELETE FROM %prefix%t2_teammembers WHERE userid = ? AND teamid = ?", [$userid, $teamid]);

        // Create Outputs
        $this->mail->create_sys_mail($userid, t_no_html('Du wurdest im Turnier %1 aus deinem Team geworfen', $t["name"]), str_replace("%NAME%", $t["name"], t_no_html('Der Ersteller dieses Teams hat dich soeben aus seinem Team entfernt. Dies bedeutet, dass du nun nicht mehr zu dem Turnier \'%NAME%\' angemeldet bist.')));
        $func->log_event(t('Der Benutzer %1 wurde vom Teamadmin aus dem Team %2 geworfen', $user["username"], $team['name']), 1, t('Turnier Teamverwaltung'));

        return true;
    }
}
