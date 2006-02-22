<?php
class team {

	// To Check if one can still signon to a tournament
	function SignonCheck($tid) {
		global $db, $config, $func, $lang;

		if ($tid == "") {
			$func->error($lang["tourney"]["join_err_no_t"], $func->internal_referer);
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
		if ($t["status"] != "open") {
			$func->information($lang["tourney"]["join_err_started"], $func->internal_referer);
			return false;
		}

		// Is the tournament allready full?
		if ($completed_teams >= $t["maxteams"]){
			$func->information(str_replace("%TEAMS%", $completed_teams, str_replace("%MAXTEAMS%", $t["maxteams"], $lang["tourney"]["join_err_full"])), $func->internal_referer);
			return false;
		}

		return true;
	}


	// To Check if a user may signon to a tournament
	function SignonCheckUser($tid, $userid) {
		global $db, $config, $lang, $func, $party, $cfg;

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
		if ($t["over18"] == 1) {
			require_once("inc/classes/class_seat.php");
			$seat = new seat;
			if ($seat->check_u18_block($userid, "u")) $over_18_error = 1;
		}

		$team_coin = $db->query_first("SELECT SUM(t.coins) AS t_coins
			FROM {$config["tables"]["tournament_tournaments"]} AS t
			LEFT JOIN {$config["tables"]["t2_teams"]} AS teams ON t.tournamentid = teams.tournamentid
			WHERE (teams.leaderid = $userid)
			GROUP BY teams.leaderid
			");

		$member_coin = $db->query_first("SELECT SUM(t.coins) AS t_coins
			FROM {$config["tables"]["tournament_tournaments"]} AS t
			LEFT JOIN {$config["tables"]["t2_teammembers"]} AS members ON t.tournamentid = members.tournamentid
			WHERE (members.userid = $userid)
			GROUP BY members.userid
			");


		// Has the tournament started?
		if ($t["status"] == "closed") $func->information($lang["tourney"]["join_err_started"], $func->internal_referer);

		// Is the user allready signed on to this tournament?
		elseif ($team["found"]) $func->information(str_replace("%USER%", $user["username"], $lang["tourney"]["join_err_reg"]), $func->internal_referer);

		// Is the user member of a team, allready signed on to this tournament?
		elseif ($teammember["found"] != "") $func->information(str_replace("%USER%", $user["username"], $lang["tourney"]["join_err_reg_memb"]), $func->internal_referer);

		// Is the user allready signed on to a tournament in the same group as this tournament?
		elseif ($in_group["found"] != "") $func->information(str_replace("%USER%", $user["username"], $lang["tourney"]["join_err_reg_group"]), $func->internal_referer);

		// Is the user member of a team, allready signed on to a tournament in the same group as this tournament?
		elseif ($memb_in_group["found"] != "") $func->information(str_replace("%USER%", $user["username"], $lang["tourney"]["join_err_reg_group_memb"]), $func->internal_referer);

		// Has the user paid?
		elseif (!$user["paid"]) $func->information(str_replace("%USER%", $user["username"], $lang["tourney"]["join_err_paid"]), $func->internal_referer);

		// Is the user 18 (only for 18+ tournaments)?
		elseif ($over_18_error) $func->information(str_replace("%USER%", $user["username"], $lang["tourney"]["join_err_u18"]), $func->internal_referer);

		// Are enough coins left to afford this tournament
		elseif (($cfg["t_coins"] - $team_coin["t_coins"] - $member_coin["t_coins"] - $t["coins"]) < 0) $func->information(str_replace("%USER%", $user["username"], $lang["tourney"]["join_err_tofew_coins"]), $func->internal_referer);

		else return true;

		return false;
	}


	// To join an existing Team
	function join($teamid, $userid, $password = NULL) {
		global $db, $config, $auth, $lang, $func, $mail;

		if ($teamid == "") { 
			$func->error($lang["tourney"]["teammgr_err_noteam"], $func->internal_referer);
			return false;
		} elseif ($userid == "") {
			$func->error($lang["tourney"]["teammgr_err_nouser"], $func->internal_referer);
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
					$func->information($lang["tourney"]["join_err_team_full"], $func->internal_referer);
					return false;

				// Everything Okay! -> Insert!
				} else {
					$db->query("INSERT INTO {$config["tables"]["t2_teammembers"]} 
						SET tournamentid = {$team["tournamentid"]},
						userid = $userid,
						teamid = $teamid
						");

					$team = $db->query_first("SELECT name FROM {$config["tables"]["t2_teams"]} WHERE tournamentid = $teamid");

					$mail->create_sys_mail($userid, str_replace("%T%", $team["tname"], str_replace("%TEAM%", $team["teamname"], $lang["tourney"]["teammgr_join_mail_subj"])), str_replace("%T%", $team["tname"], str_replace("%TEAM%", $team["teamname"], $lang["tourney"]["teammgr_join_mail"])));

					$func->log_event(str_replace("%USER%", $auth["username"], str_replace("%TEAM%", $team["teamname"], str_replace("%T%", $team["tname"], $lang["tourney"]["join_log_success_team"]))), 1, $lang["tourney"]["log_t_teammanage"]);
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
				$func->error(str_replace("%TEAMS%", $completed_teams + $waiting_teams, str_replace("%MAXTEAMS%", $t["maxteams"], str_replace("%WAITING%", $waiting_teams, $lang["tourney"]["join_waiting_only"]))), $func->internal_referer);
				return false;

			} else {
				if ($_FILES[$banner]["tmp_name"] != "") $func->FileUpload("team_banner", "ext_inc/team_banners/");

				if (!$name) {
					$user = $db->query_first("SELECT username FROM {$config["tables"]["user"]} WHERE userid = $leaderid");
					$name = $user["username"];
				}
				$query = $db->query("INSERT INTO {$config["tables"]["t2_teams"]} 
					SET tournamentid = $tournamentid,
					name = '$name',
					leaderid = $leaderid,
					comment = '$comment',
					banner = '{$_FILES[$banner]["name"]}',
					password = '". md5($password) ."'
					");

				$func->log_event(str_replace("%USER%", $auth["username"], str_replace("%T%", $t["name"], $lang["tourney"]["join_log_success"])), 1, $lang["tourney"]["log_t_teammanage"]);
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
		$func->log_event(str_replace("%TEAM%", $_POST['team_name'], str_replace("%T%", $t["name"], $lang["tourney"]["teammgr_edit_team_log"])), 1, $lang["tourney"]["log_t_teammanage"]);

		$this->UpdateLeagueIDs($auth["userid"], $_POST["wwclid"], $_POST["wwclclanid"], $_POST["nglid"], $_POST["nglclannid"], $_POST["lgzid"], $_POST["lgzclannid"]);

		return true;
	}


	// Deletes a whole team
	function delete($teamid) {
		global $db, $config, $lang, $func, $mail;

		if ($teamid == "") {
			$func->error($lang["tourney"]["teammgr_err_noteam"], $func->internal_referer);
			return false;
		}

		$team = $db->query_first("SELECT t.status, t.name AS tname, team.name AS teamname, team.leaderid
			FROM {$config["tables"]["tournament_tournaments"]} AS t
			LEFT JOIN {$config["tables"]["t2_teams"]} AS team ON (t.tournamentid = team.tournamentid)
			WHERE (teamid = $teamid)
			");

		// No delete if tournament is generated
		if ($team['status'] != "open") {
			$func->information($lang["tourney"]["teammgr_err_nosignoff"], $func->internal_referer);
			return false;
		}

		// Send Mail to Teammebers
		$members = $db->query("SELECT userid FROM {$config["tables"]["t2_teammembers"]} WHERE teamid = $teamid");
		while ($member = $db->fetch_array($members)) {
			$mail->create_sys_mail($member['userid'], str_replace("%NAME%", $team['tname'], $lang["tourney"]["teammgr_signoff_mail_subj"]), str_replace("%NAME%", $team['tname'], $lang["tourney"]["teammgr_signoff_mail"]));
		}
		$db->free_result($members);

		// Perform Action
		$db->query("DELETE FROM {$config["tables"]["t2_teams"]} WHERE teamid = $teamid");
		$db->query("DELETE FROM {$config["tables"]["t2_teammembers"]} WHERE teamid = $teamid");

		$func->log_event(str_replace("%NAME%", $team['teamname'], $lang["tourney"]["teammgr_signoff_log"]), 1, $lang["tourney"]["log_t_teammanage"]);

		return true;
	}


	// Kicks one player out of the team
	function kick($teamid, $userid) {
		global $db, $config, $lang, $func, $mail;

		if ($teamid == "") {
			$func->error($lang["tourney"]["teammgr_err_noteam"], $func->internal_referer);
			return false;
		}
		if ($userid == "") {
			$func->error($lang["tourney"]["teammgr_err_nouser"], $func->internal_referer);
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
			$func->information($lang["tourney"]["join_err_started"], $func->internal_referer);
			return false;
		}
		// Perform Action
		$db->query("DELETE FROM {$config["tables"]["t2_teammembers"]} WHERE (userid = $userid) AND (teamid = $teamid)");

		// Create Outputs
		$mail->create_sys_mail($userid, str_replace("%NAME%", $t["name"], $lang["tourney"]["teammgr_deluser_mail_subj"]), str_replace("%NAME%", $t["name"], $lang["tourney"]["teammgr_deluser_mail"]));
		$func->log_event(str_replace("%NAME%", $user["username"], str_replace("%TEAM%", $team, $lang["tourney"]["teammgr_deluser_log"])), 1, $lang["tourney"]["log_t_teammanage"]);

		return true;
	}


	// To set new League IDs
	function UpdateLeagueIDs($userid, $wwclid = NULL, $wwclclanid = NULL, $nglid = NULL, $nglclanid = NULL, $lgzid = NULL, $lgzclanid = NULL) {
		global $db, $config, $auth;

		if ($wwclid != "") $db->query("UPDATE {$config["tables"]["user"]} SET wwclid = $wwclid WHERE userid = $userid");
		if ($wwclclanid != "") $db->query("UPDATE {$config["tables"]["user"]} SET wwclclanid = $wwclclanid WHERE userid = $userid");
		if ($nglid != "") $db->query("UPDATE {$config["tables"]["user"]} SET nglid = $nglid WHERE userid = $userid");
		if ($nglclanid != "") $db->query("UPDATE {$config["tables"]["user"]} SET nglclanid = $nglclanid WHERE userid = $userid");
		if ($lgzid != "") $db->query("UPDATE {$config["tables"]["user"]} SET lgzid = $lgzid WHERE userid = $userid");
		if ($lgzclanid != "") $db->query("UPDATE {$config["tables"]["user"]} SET lgzclanid = $lgzclanid WHERE userid = $userid");
	}

}
?>
