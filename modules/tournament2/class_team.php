<?php
class team {

	// To Check if one can still signon to a tournament
	function SignonCheck($tid) {
		global $db, $config, $func, $lang;

		if ($tid == "") {
			$func->error(t('Sie müssen zuerst ein Turnier auswählen!'), $func->internal_referer);
			return false;
		}

		$t = $db->query_first("SELECT status, teamplayer, maxteams, blind_draw FROM {$config["tables"]["tournament_tournaments"]} WHERE tournamentid = $tid");

		if ($t["teamplayer"] == 1 or $t["blind_draw"]) {
			$c_teams = $db->query_first("SELECT COUNT(*) AS teams FROM {$config["tables"]["t2_teams"]} WHERE (tournamentid = $tid) GROUP BY teamid");
			$completed_teams = $c_teams["teams"];
			$waiting_teams = 0;
		} else {
			$waiting_teams = 0;
			$completed_teams = 0;
			$c_teams = $db->query("SELECT teamid FROM {$config["tables"]["t2_teams"]} WHERE (tournamentid = $tid)");
			while($c_team = $db->fetch_array($c_teams)) {
				$c_member = $db->query_first("SELECT COUNT(*) AS members
					FROM {$config["tables"]["t2_teammembers"]}
					WHERE (teamid = {$c_team["teamid"]})
					GROUP BY teamid
					");

				if (($c_member["members"] + 1) < $t["teamplayer"]) $waiting_teams++;
				else $completed_teams++;
			}
		}
		if ($t["blind_draw"]) $completed_teams = floor($completed_teams / $t["teamplayer"]);

		// Is the tournament finished?
		if ($t["status"] != "open") $func->information(t('Dieses Turnier befindet sich momentan nicht in der Anmeldephase!'), $func->internal_referer);

		// Is the tournament allready full?
		elseif ($completed_teams >= $t["maxteams"]) $func->information(t('Es haben sich bereits %1 von %2 Teams zu diesem Turnier angemeldet. Das Turnier ist damit ausgebucht.', $completed_teams, $t["maxteams"]), $func->internal_referer);

    // Everything fine
		else return true;

		return false;
	}


	// To Check if a user may signon to a tournament
	function SignonCheckUser($tid, $userid) {
		global $db, $config, $lang, $func, $party, $cfg, $seat2;

		$t = $db->query_first("SELECT groupid, maxteams, over18, status, coins FROM {$config["tables"]["tournament_tournaments"]} WHERE tournamentid = $tid");
		$user = $db->query_first("SELECT p.paid, u.username
			FROM {$config["tables"]["user"]} AS u
			LEFT JOIN {$config["tables"]["party_user"]} AS p ON u.userid = p.user_id
			WHERE u.userid = $userid AND party_id={$party->party_id}
			");

		$team = $db->query_first("SELECT 1 AS found FROM {$config["tables"]["t2_teams"]} WHERE (tournamentid = $tid) AND (leaderid = $userid)");
		$teammember = $db->query_first("SELECT 1 AS found FROM {$config["tables"]["t2_teammembers"]} WHERE (tournamentid = $tid) AND (userid = $userid)");

		$in_group = $db->query_first("SELECT 1 AS found
			FROM {$config["tables"]["t2_teams"]} AS team
			LEFT JOIN {$config["tables"]["tournament_tournaments"]} AS t ON (t.tournamentid = team.tournamentid)
			WHERE (t.groupid = {$t["groupid"]}) AND (t.groupid != 0)  AND (t.party_id={$party->party_id}) AND (team.leaderid = $userid)
			");
		
		$memb_in_group = $db->query_first("SELECT 1 AS found
			FROM {$config["tables"]["t2_teammembers"]} AS members
			LEFT JOIN {$config["tables"]["tournament_tournaments"]} AS t ON (t.tournamentid = members.tournamentid)
			WHERE (t.groupid = {$t["groupid"]}) AND (t.groupid != 0)  AND (t.party_id={$party->party_id}) AND (userid = $userid)
			");

		$over_18_error = 0;
		if ($t["over18"] == 1 and $seat2->U18Block($userid, "u")) $over_18_error = 1;

		$team_coin = $db->query_first("SELECT SUM(t.coins) AS t_coins
			FROM {$config["tables"]["tournament_tournaments"]} AS t
			LEFT JOIN {$config["tables"]["t2_teams"]} AS teams ON t.tournamentid = teams.tournamentid
			WHERE teams.leaderid = $userid AND t.party_id = ". (int)$party->party_id ."
			GROUP BY teams.leaderid
			");

		$member_coin = $db->query_first("SELECT SUM(t.coins) AS t_coins
			FROM {$config["tables"]["tournament_tournaments"]} AS t
			LEFT JOIN {$config["tables"]["t2_teammembers"]} AS members ON t.tournamentid = members.tournamentid
			WHERE members.userid = $userid AND t.party_id = ". (int)$party->party_id ."
			GROUP BY members.userid
			");


		// Is the user allready signed on to this tournament?
		if ($team["found"]) $func->information(t('%1 ist bereits zu diesem Turnier angemeldet!', $user["username"]), $func->internal_referer);

		// Is the user member of a team, allready signed on to this tournament?
		elseif ($teammember["found"] != "") $func->information(t('%1 ist bereits Mitglied eines Teams, dass sich zu diesem Turnier angemeldet hat!', $user["username"]), $func->internal_referer);

		// Is the user allready signed on to a tournament in the same group as this tournament?
		elseif ($in_group["found"] != "") $func->information(t('%1 ist bereits zu einem Turnier angemeldet, welches der gleichen Gruppe angehört!', $user["username"]), $func->internal_referer);

		// Is the user member of a team, allready signed on to a tournament in the same group as this tournament?
		elseif ($memb_in_group["found"] != "") $func->information(t('%1 ist bereits Mitglied eines Teams, dass sich zu einem Turnier der gleichen Gruppe angemeldet hat!', $user["username"]), $func->internal_referer);

		// Has the user paid?
		elseif (!$user["paid"]) $func->information(t('%1 muss erst für diese Party bezahlen, um sich an einem Turnier anmelden zu können!', $user["username"]), $func->internal_referer);

		// Is the user 18 (only for 18+ tournaments)?
		elseif ($over_18_error) $func->information(t('%1 kann diesem Turnier nicht beitreten. In diesem Turnier dürfen nur Benutzer mitspielen, die <b>nicht</b> in einem Unter-18-Block sitzen', $user["username"]), $func->internal_referer);

		// Are enough coins left to afford this tournament
		elseif (($cfg["t_coins"] - $team_coin["t_coins"] - $member_coin["t_coins"] - $t["coins"]) < 0) $func->information(t('%1 besitzt nicht genügend Coins um an diesem Turnier teilnehmen zu können!', $user["username"]), $func->internal_referer);

    // Everything fine
		else return true;

		return false;
	}


	// To join an existing Team
	function join($teamid, $userid, $password = NULL) {
		global $db, $config, $auth, $lang, $func, $mail;

		if ($teamid == "") { 
			$func->error(t('Sie haben kein Team ausgeählt!'), $func->internal_referer);
			return false;
		} elseif ($userid == "") {
			$func->error(t('Sie haben keinen Benutzer ausgewählt!'), $func->internal_referer);
			return false;

		} else {
			$team = $db->query_first("SELECT team.tournamentid, team.password, team.leaderid, team.name AS teamname, t.name AS tname, t.teamplayer
				FROM {$config["tables"]["t2_teams"]} AS team
				LEFT JOIN {$config["tables"]["tournament_tournaments"]} AS t ON team.tournamentid = t.tournamentid
				WHERE team.teamid = $teamid
				");

      // Check password, if set and if acction is not performed, by teamadmin or ls-admin
      if (($auth['userid'] != $team['leaderid']) and ($auth['type'] <= 1) and ($team['password'] != '') and (md5($password) != $team['password'])) {
        $func->information('Das eingegebene Kennwort ist nicht korrekt', $func->internal_referer);
        return false;

			// May one still signon for this tournament?
			} elseif ($this->SignonCheckUser($team["tournamentid"], $userid)) {
				$member_anz = $db->query_first("SELECT COUNT(*) AS members FROM {$config["tables"]["t2_teammembers"]} WHERE teamid = $teamid GROUP BY teamid");

				// Isn't the team full yet?
				if ($team["teamplayer"] <= ($member_anz["members"] + 1)) {
					$func->information(t('Das gewählte Team ist bereits voll!'), $func->internal_referer);
					return false;

				// Everything Okay! -> Insert!
				} else {
					$db->query("INSERT INTO {$config["tables"]["t2_teammembers"]} 
						SET tournamentid = {$team["tournamentid"]},
						userid = $userid,
						teamid = $teamid
						");

					$mail->create_sys_mail($userid, t('Sie wurden dem Team <b>%1</b> im Turnier <b>%2</b> hinzugefügt', $team["teamname"], $team["tname"]), t('Der Ersteller des Teams <b>%1</b> hat Sie in sein Team im Turnier <b>%2</b> aufgenommen.', $team["teamname"], $team["tname"]));

					$func->log_event(t('Der Benutzer <b>%1</b> ist dem Team <b>%2</b> im Turnier <b>%3</b> beigetreten', $auth["username"], $team["teamname"], $team["tname"]), 1, t('Turnier Teamverwaltung'));
				}
			} else return false;
		}
		return true;
	}


	// To create a new Team
	function create($tournamentid, $leaderid, $name = NULL, $password = NULL, $comment = NULL, $banner = NULL) {
		global $db, $config, $auth, $lang, $func;

		if ($this->SignonCheck($tournamentid) and $this->SignonCheckUser($tournamentid, $leaderid)) {
			$t = $db->query_first("SELECT name, teamplayer, maxteams FROM {$config["tables"]["tournament_tournaments"]} WHERE tournamentid = $tournamentid");

			if ($t["teamplayer"] == 1 or $t["blind_draw"]) {
				$c_teams = $db->query_first("SELECT COUNT(*) AS teams FROM {$config["tables"]["t2_teams"]} WHERE (tournamentid = $tournamentid) GROUP BY teamid");
				$completed_teams = $c_teams["teams"];
				$waiting_teams = 0;
			} else {
				$waiting_teams = 0;
				$completed_teams = 0;
				$c_teams = $db->query("SELECT teamid FROM {$config["tables"]["t2_teams"]} WHERE (tournamentid = $tournamentid)");
				while($c_team = $db->fetch_array($c_teams)) {
					$c_member = $db->query_first("SELECT COUNT(*) AS members
						FROM {$config["tables"]["t2_teammembers"]}
						WHERE (teamid = {$c_team["teamid"]})
						GROUP BY teamid
						");

					if (($c_member["members"] + 1) < $t["teamplayer"]) $waiting_teams++;
					else $completed_teams++;
				}
			}
			if ($t["blind_draw"]) $completed_teams = floor($completed_teams / $t["teamplayer"]);

			if (($completed_teams + $waiting_teams) >= $t["maxteams"]) {
				$func->error(t('Es haben sich bereits %1 von %2 Teams zu diesem Turnier angemeldet. Es gibt jedoch noch in %3 der angemeldeten Teams freie Plätze, dazu bitte eines der Teams mit den freien Plätzen auswählen und beitreten', $completed_teams + $waiting_teams, $t["maxteams"], $waiting_teams), $func->internal_referer);
				return false;

			} else {
				if ($_FILES[$banner]["tmp_name"] != "") $func->FileUpload("team_banner", "ext_inc/team_banners/");

				if (!$name) {
					$user = $db->query_first("SELECT username FROM {$config["tables"]["user"]} WHERE userid = $leaderid");
					$name = $func->escape_sql($user["username"]);
				}
				$query = $db->query("INSERT INTO {$config["tables"]["t2_teams"]} 
					SET tournamentid = $tournamentid,
					name = '$name',
					leaderid = $leaderid,
					comment = '$comment',
					banner = '{$_FILES[$banner]["name"]}',
					password = '". md5($password) ."'
					");

				$func->log_event(t('Der Benutzer <b>%1</b> hat sich zum Turnier <b>%2</b> angemeldet', $auth["username"], $t["name"]), 1, t('Turnier Teamverwaltung'));
			}
		} else return false;

		return true;
	}


	// Edits the details of a team
	function edit($teamid, $name = NULL, $password = NULL, $comment = NULL, $banner = NULL){
		global $db, $config, $auth, $lang, $func;

		$t = $db->query_first("SELECT t.name, t.teamplayer FROM {$config["tables"]["tournament_tournaments"]} AS t
			LEFT JOIN {$config["tables"]["t2_teams"]} AS team ON t.tournamentid = team.tournamentid
			WHERE team.teamid = $teamid
			");

		// Upload Banner
		if ($_FILES[$banner]["tmp_name"] != "") {
			$func->FileUpload("team_banner", "ext_inc/team_banners/");
			$db->query("UPDATE {$config["tables"]["t2_teams"]} SET banner = '{$_FILES["team_banner"]["name"]}' WHERE teamid = {$_GET["teamid"]}");
		}

		if ($t['teamplayer'] == 1) $name = $auth["username"];

		// Set Password
		if ($password != "") $db->query("UPDATE {$config["tables"]["t2_teams"]} SET password = '". md5($password) ."' WHERE teamid = {$_GET["teamid"]}");

		$db->query("UPDATE {$config["tables"]["t2_teams"]} 
			SET name = '$name',
			comment = '$comment'
			WHERE teamid = {$_GET["teamid"]}
			");
		$func->log_event(t('Das Team <b>%1</b> im Turnier <b>%2</b> hat seine Daten editiert', $_POST['team_name'], $t["name"]), 1, t('Turnier Teamverwaltung'));

		$this->UpdateLeagueIDs($auth["userid"], $_POST["wwclid"], $_POST["wwclclanid"], $_POST["nglid"], $_POST["nglclannid"], $_POST["lgzid"], $_POST["lgzclannid"]);

		return true;
	}


	// Deletes a whole team
	function delete($teamid) {
		global $db, $config, $lang, $func, $mail;

		if ($teamid == "") {
			$func->error(t('Sie haben kein Team ausgeählt!'), $func->internal_referer);
			return false;
		}

		$team = $db->query_first("SELECT t.status, t.name AS tname, team.name AS teamname, team.leaderid
			FROM {$config["tables"]["tournament_tournaments"]} AS t
			LEFT JOIN {$config["tables"]["t2_teams"]} AS team ON (t.tournamentid = team.tournamentid)
			WHERE (teamid = $teamid)
			");

		// No delete if tournament is generated
		if ($team['status'] != "open") {
			$func->information(t('Dieses Turnier wird bereits gespielt!HTML_NEWLINEEin Abmelden ist daher nicht mehr möglich.'), $func->internal_referer);
			return false;
		}

		// Send Mail to Teammebers
		$members = $db->query("SELECT userid FROM {$config["tables"]["t2_teammembers"]} WHERE teamid = $teamid");
		while ($member = $db->fetch_array($members)) {
			$mail->create_sys_mail($member['userid'], t('Ihr Team im Turnier %1 wurde aufgelöst', $team['tname']), t('Der Ersteller des Teams hat soeben sein Team aufgelöst. Dies bedeutet, dass Sie nun nicht mehr zu dem Turnier %1 angemeldet sind.', $team['tname']));
		}
		$db->free_result($members);

		// Perform Action
		$db->query("DELETE FROM {$config["tables"]["t2_teams"]} WHERE teamid = $teamid");
		$db->query("DELETE FROM {$config["tables"]["t2_teammembers"]} WHERE teamid = $teamid");

		$func->log_event(t('Das Team %1 wurde aufgelöst', $team['teamname']), 1, t('Turnier Teamverwaltung'));

		return true;
	}


	// Kicks one player out of the team
	function kick($teamid, $userid) {
		global $db, $config, $lang, $func, $mail;

		if ($teamid == "") {
			$func->error(t('Sie haben kein Team ausgeählt!'), $func->internal_referer);
			return false;
		}
		if ($userid == "") {
			$func->error(t('Sie haben keinen Benutzer ausgewählt!'), $func->internal_referer);
			return false;
		}

		// Select Output information
		$t = $db->query_first("SELECT t.name
			FROM {$config["tables"]["tournament_tournaments"]} AS t
			LEFT JOIN {$config["tables"]["t2_teammembers"]} AS m ON t.tournamentid = m.tournamentid
			WHERE (m.userid = $userid) AND (m.teamid = $teamid)
			");
		$user = $db->query_first("SELECT username FROM {$config["tables"]["user"]} WHERE userid = $userid");
		$team = $db->query_first("SELECT name FROM {$config["tables"]["t2_teams"]} WHERE teamid = $teamid");

		// Is the tournament finished?
		if ($t["status"] == "closed") {
			$func->information(t('Dieses Turnier läuft bereits!'), $func->internal_referer);
			return false;
		}
		// Perform Action
		$db->query("DELETE FROM {$config["tables"]["t2_teammembers"]} WHERE (userid = $userid) AND (teamid = $teamid)");

		// Create Outputs
		$mail->create_sys_mail($userid, t('Sie wurden im Turnier %1 aus ihrem Team geworfen', $t["name"]), str_replace("%NAME%", $t["name"], t('Der Ersteller dieses Teams hat Sie soeben aus seinem Team entfernt. Dies bedeutet, dass Sie nun nicht mehr zu dem Turnier \'%NAME%\' angemeldet sind.')));
		$func->log_event(t('Der Benutzer %1 wurde vom Teamadmin aus dem Team %2 geworfen', $user["username"], $team['name']), 1, t('Turnier Teamverwaltung'));

		return true;
	}


	// To set new League IDs
	function UpdateLeagueIDs($userid, $wwclid = NULL, $wwclclanid = NULL, $nglid = NULL, $nglclanid = NULL, $lgzid = NULL, $lgzclanid = NULL) {
		global $db, $config, $auth;

		if ($wwclid != "") $db->qry('UPDATE %prefix%user SET wwclid = %string% WHERE userid = %int%', $wwclid, $userid);
		if ($wwclclanid != "") $db->qry('UPDATE %prefix%user SET wwclclanid = %string% WHERE userid = %int%', $wwclclanid, $userid);
		if ($nglid != "") $db->qry('UPDATE %prefix%user SET nglid = %string% WHERE userid = %int%', $nglid, $userid);
		if ($nglclanid != "") $db->qry('UPDATE %prefix%user SET nglclanid = %string% WHERE userid = %int%', $nglclanid, $userid);
		if ($lgzid != "") $db->qry('UPDATE %prefix%user SET lgzid = %string% WHERE userid = %int%', $lgzid, $userid);
		if ($lgzclanid != "") $db->qry('UPDATE %prefix%user SET lgzclanid = %string% WHERE userid = %int%', $lgzclanid, $userid);
	}

}
?>
