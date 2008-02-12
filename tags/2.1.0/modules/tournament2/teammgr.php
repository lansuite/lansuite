<?php

include("modules/tournament2/class_team.php");
$tteam = new team;

$step 		= $vars["step"];
$tournamentid 	= $vars["tournamentid"];
$userid 	= $vars["userid"];

switch($step) {
	// Team verlassen
	case 10:
		if ($tteam->kick($_GET["teamid"], $auth["userid"])) $func->confirmation($lang["tourney"]["teammgr_del_success"], "index.php?mod=tournament2&action=teammgr");
	break;

	// Spieler aus Team entfernen
	case 20:
		if ($tteam->kick($_GET["teamid"], $userid)) $func->confirmation($lang["tourney"]["teammgr_deluser_success"], "index.php?mod=tournament2&action=teammgr");
	break;

	// Team abmelden (löschen) / Mich abmelden
	case 30:
		if ($tteam->delete($_GET["teamid"])) $func->confirmation($lang["tourney"]["teammgr_signoff_success"], "index.php?mod=tournament2&action=teammgr");
	break;

	// Spieler zum eigenen Team hinzufügen - Suchen
	case 40:
		$mastersearch = new MasterSearch($vars, 
			"index.php?mod=tournament2&action=teammgr&step=40&teamid={$_GET["teamid"]}&tournamentid=$tournamentid", 
			"index.php?mod=tournament2&action=teammgr&step=41&teamid={$_GET["teamid"]}&tournamentid=$tournamentid&userid=", 
			" AND p.party_id={$party->party_id} AND (p.paid) GROUP BY u.email");
		$mastersearch->LoadConfig("users", $lang["tourney"]["teammgr_ms_caption"], $lang["tourney"]["teammgr_ms_subcaption"]);
		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();

		$templ['index']['info']['content'] .= $mastersearch->GetReturn();
	break;

	// Spieler zum eigenen Team hinzufügen - In DB schreiben
	case 41:
		if ($tteam->join($_GET["teamid"], $userid)) $func->confirmation($lang["tourney"]["teammgr_join_success"], "index.php?mod=tournament2&action=teammgr");
	break;

	// Edit Teamdetails (Form)
	case 50:
		$sec->unlock("t_team_edit");

		$tournament = $db->query_first("SELECT teamplayer, wwcl_gameid, ngl_gamename FROM {$config["tables"]["tournament_tournaments"]} WHERE tournamentid = '$tournamentid'");

		$team = $db->query_first("SELECT team.name, team.comment, team.banner, user.nglid, user.nglclanid, user.wwclid, user.wwclclanid
				FROM {$config["tables"]["t2_teams"]} AS team
				LEFT JOIN {$config["tables"]["user"]} AS user ON user.userid = team.leaderid
				WHERE teamid = '{$_GET["teamid"]}'");

		$dsp->NewContent($lang["tourney"]["teammgr_caption"], $lang["tourney"]["teammgr_subcaption"]);

		$dsp->SetForm("index.php?mod=tournament2&action=teammgr&step=51&teamid={$_GET["teamid"]}&tournamentid=$tournamentid", "", "", "multipart/form-data");

		if ($tournament['teamplayer'] == 1) {
			$dsp->AddDoubleRow($lang["tourney"]["join_teamname"], $auth["username"]);
			$team['name'] = $auth["username"];
		} else $dsp->AddTextFieldRow("team_name", $lang["tourney"]["join_teamname"], $team['name'], "");

		$dsp->AddPasswordRow("set_password", $lang["tourney"]["join_team_pw1"], $_POST["set_password"], $error["set_password"]);
		$dsp->AddPasswordRow("set_password2", $lang["tourney"]["join_team_pw2"], $_POST["set_password2"], $error["set_password2"]);

		$dsp->AddTextAreaPlusRow("team_comment", $lang["tourney"]["join_comment"], $team['comment'], "", "", "", 1);
		if ($team["banner"]) $dsp->AddSingleRow("<img src=\"ext_inc/team_banners/{$team['banner']}\" alt=\"{$team['banner']}\">");
		$dsp->AddFileSelectRow("team_banner", $lang["tourney"]["join_banner"], "", "", 1000000, 1);

		if ($tournament['wwcl_gameid'] > 0){
			$dsp->AddTextFieldRow("wwclid", $lang["tourney"]["join_wwcl_id"], $team['wwclid'], "");
			if ($tournament['teamplayer'] > 1) $dsp->AddTextFieldRow("wwclclanid", $lang["tourney"]["join_wwcl_clan_id"], $team['wwclclanid'], "");
		}
		if ($tournament['ngl_gamename'] != ""){
			$dsp->AddTextFieldRow("nglid", $lang["tourney"]["join_ngl_id"], $team['nglid'], "");
			if ($tournament['teamplayer'] > 1) $dsp->AddTextFieldRow("nglclanid", $lang["tourney"]["join_ngl_clan_id"], $team['nglclanid'], "");
		}

		$dsp->AddFormSubmitRow("edit");
		$dsp->AddBackButton("index.php?mod=tournament2&action=teammgr", "tournament2/teammgr");

		$dsp->AddContent();
	break;

	// Edit Teamdetails (Action)
	case 51:
		if (!$sec->locked("t_team_edit")) {
			$tournament = $db->query_first("SELECT name FROM {$config["tables"]["tournament_tournaments"]} WHERE tournamentid = '$tournamentid'");

			if ($_POST['team_name'] == "" and $tournament['teamplayer'] > 1){
				$func->information($lang["tourney"]["join_err_no_name"], "index.php?mod=tournament2&action=teammgr&tournamentid=$tournamentid&teamid={$_GET["teamid"]}&step=50");
				break;
			}

			if ($_POST["set_password"] and $_POST["set_password"] != $_POST["set_password2"]) $error["set_password2"] = "Die Passworteingaben stimmen nicht überein";

			if ($tteam->edit($_GET["teamid"], $_POST['team_name'], $_POST["set_password"], $_POST['team_comment'], "team_banner")) $func->confirmation($lang["tourney"]["teammgr_edit_team_success"], "index.php?mod=tournament2&action=teammgr");

			$sec->lock("t_team_edit");
		}
	break;


	default:
		$dsp->NewContent($lang["tourney"]["teammgr_caption"], $lang["tourney"]["teammgr_subcaption"]);

		$dsp->AddSingleRow($lang["tourney"]["teammgr_sp_caption"]);
		// Teamname und Turniername auslesen
		$i=0;
		$teams = $db->query("SELECT teams.teamid, teams.name, t.name AS tname, t.teamplayer, t.tournamentid
			FROM {$config["tables"]["t2_teams"]} AS teams
			LEFT JOIN {$config["tables"]["tournament_tournaments"]} AS t ON (t.tournamentid = teams.tournamentid)
			WHERE (teams.leaderid = ". $auth["userid"] .") AND (t.teamplayer = 1) AND t.party_id='$party->party_id'");
		if ($db->num_rows($teams) == 0) $dsp->AddDoubleRow($lang["tourney"]["teammgr_tourney"], $lang["tourney"]["teammgr_no_sp"]);
		else while($team = $db->fetch_array($teams)) {
			$i++;
			
			$dsp->AddDoubleRow($lang["tourney"]["teammgr_tourney"] ." ". $i, "{$team["tname"]}" . HTML_NEWLINE . "<a href=\"index.php?mod=tournament2&action=teammgr&step=50&teamid={$team['teamid']}&tournamentid={$team['tournamentid']}\">{$lang["tourney"]["teammgr_edit_team"]}</a>" . HTML_NEWLINE . "<a href=\"index.php?mod=tournament2&action=teammgr&step=30&teamid={$team['teamid']}\">{$lang["tourney"]["teammgr_signoff"]}</a>");
		}
		$db->free_result($teams);


		$dsp->AddSingleRow($lang["tourney"]["teammgr_created_caption"]);
		// Teamname und Turniername auslesen
		$i=0;
		$teams = $db->query("SELECT teams.teamid, teams.name, t.name AS tname, t.tournamentid, t.teamplayer
			FROM {$config["tables"]["t2_teams"]} AS teams
			LEFT JOIN {$config["tables"]["tournament_tournaments"]} AS t ON t.tournamentid = teams.tournamentid
			WHERE (teams.leaderid = ". $auth["userid"] .") AND (t.teamplayer > 1) AND t.party_id='$party->party_id'
			");
		if ($db->num_rows($teams) == 0) $dsp->AddDoubleRow($lang["tourney"]["teammgr_team"], $lang["tourney"]["teammgr_no_create"]);
		else while($team = $db->fetch_array($teams)) {
			$i++;

			// Mitgliedernamen auslesen
			$members = $db->query("SELECT users.username, members.userid, members.teamid
				FROM {$config["tables"]["t2_teammembers"]} AS members
				LEFT JOIN {$config["tables"]["t2_teams"]} AS teams ON members.teamid = teams.teamid
				LEFT JOIN {$config["tables"]["user"]} AS users ON members.userid = users.userid
				WHERE (teams.teamid = {$team['teamid']})");

			$member_liste = "";
			$anz_memb = 0;
			while($member = $db->fetch_array($members)) {
				$anz_memb++;
				$member_liste .= HTML_NEWLINE . "- ". $member["username"] ." <a href=\"index.php?mod=usrmgr&action=details&userid={$member['userid']}\"><img src=\"/design/". $_SESSION["auth"]["design"] ."/images/arrows_user.gif\" border=\"0\"></a> ". $dsp->FetchButton("index.php?mod=tournament2&action=teammgr&step=20&teamid={$member['teamid']}&userid={$member['userid']}", "kick");
			}
			$db->free_result($members);
			
			$dsp->AddDoubleRow($lang["tourney"]["teammgr_team"] ." ". $i, "{$team["name"]} ({$team["tname"]}) ({$lang["tourney"]["teammgr_teamsize"]}: ". ($anz_memb+1) ."/{$team["teamplayer"]}) $member_liste" . HTML_NEWLINE . "<a href=\"index.php?mod=tournament2&action=teammgr&step=40&teamid={$team['teamid']}&tournamentid={$team['tournamentid']}\">{$lang["tourney"]["teammgr_add_player"]}</a>" . HTML_NEWLINE . "<a href=\"index.php?mod=tournament2&action=teammgr&step=50&teamid={$team['teamid']}&tournamentid={$team['tournamentid']}\">{$lang["tourney"]["teammgr_edit_team"]}</a>" . HTML_NEWLINE . "<a href=\"index.php?mod=tournament2&action=teammgr&step=30&teamid={$team['teamid']}\">{$lang["tourney"]["teammgr_signoff_team2"]}</a>");
		}
		$db->free_result($teams);


		$dsp->AddSingleRow($lang["tourney"]["teammgr_memb_caption"]);
		$members = $db->query("SELECT users.username, users.userid, teams.name, teams.teamid, t.name AS tname, t.teamplayer
			FROM {$config["tables"]["t2_teammembers"]} AS members
			LEFT JOIN {$config["tables"]["t2_teams"]} AS teams ON members.teamid = teams.teamid
			LEFT JOIN {$config["tables"]["user"]} AS users ON teams.leaderid = users.userid
			LEFT JOIN {$config["tables"]["tournament_tournaments"]} AS t ON teams.tournamentid = t.tournamentid
			WHERE (members.userid = {$auth["userid"]}) AND t.party_id='$party->party_id'");

		$member_liste = "";
		$anz_memb = 0;
		$i = 0;
		if ($db->num_rows($members) == 0) $dsp->AddDoubleRow($lang["tourney"]["teammgr_team"], $lang["tourney"]["teammgr_no_memb"]);
		else while($member = $db->fetch_array($members)) {
			$i++;

			// Mitgliedernamen auslesen
			$members2 = $db->query("SELECT users.username, members.userid, members.teamid
				FROM {$config["tables"]["t2_teammembers"]} AS members
				LEFT JOIN {$config["tables"]["t2_teams"]} AS teams ON members.teamid = teams.teamid
				LEFT JOIN {$config["tables"]["user"]} AS users ON members.userid = users.userid
				WHERE (teams.teamid = {$member['teamid']})");

			$member_liste = "";
			$anz_memb = 0;
			while($member2 = $db->fetch_array($members2)) {
				$anz_memb++;
				$member_liste .= HTML_NEWLINE . "- ". $member2["username"] ." <a href=\"index.php?mod=usrmgr&action=details&userid={$member2['userid']}\"><img src=\"/design/". $_SESSION["auth"]["design"] ."/images/arrows_user.gif\" border=\"0\"></a>";
			}
			$db->free_result($members2);

			$dsp->AddDoubleRow($lang["tourney"]["teammgr_team"] ." ". $i, "{$member["name"]} ({$member["tname"]}) ({$lang["tourney"]["teammgr_teamsize"]}: ". ($anz_memb+1) ."/{$member["teamplayer"]})" . HTML_NEWLINE . "{$lang["tourney"]["teammgr_leader"]}: ". $member["username"] ." <a href=\"index.php?mod=usrmgr&action=details&userid={$member['userid']}\"><img src=\"/design/". $_SESSION["auth"]["design"] ."/images/arrows_user.gif\" border=\"0\"></a>$member_liste" . HTML_NEWLINE . "<a href=\"index.php?mod=tournament2&action=teammgr&step=10&teamid={$member['teamid']}\">{$lang["tourney"]["teammgr_signoff_team"]}</a>");
		}
		$db->free_result($members);


		$dsp->AddSingleRow($lang["tourney"]["teammgr_add_hint"]);

		$dsp->AddBackButton("index.php?mod=tournament2", "tournament2/teammgr"); 
		$dsp->AddContent();
	break;
} // Switch $step

?>
