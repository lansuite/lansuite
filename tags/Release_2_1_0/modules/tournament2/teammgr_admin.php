<?php

include("modules/tournament2/class_team.php");
$tteam = new team;

$tournamentid 	= $vars["tournamentid"];
$userid 	= $vars["userid"];
$teamid 	= $vars["teamid"];
$member_user 	= $vars["member_user"];


switch($_GET["step"]) {
	// Team löschen
	case 10:
		if ($tteam->delete($_POST["teamid"])) $func->confirmation($lang["tourney"]["admteammgr_del_success"], "index.php?mod=tournament2&action=teammgr_admin");
	break;

	// Spieler einem Team hinzufügen - Suchen
	case 20:
		$mastersearch = new MasterSearch($vars, "index.php?mod=tournament2&action=teammgr_admin&step=20&teamid=$teamid", "index.php?mod=tournament2&action=teammgr_admin&step=21&teamid=$teamid&userid=", " AND p.party_id={$party->party_id} AND (p.paid) GROUP BY u.email");
		$mastersearch->LoadConfig("users", $lang["tourney"]["teammgr_ms_caption"], $lang["tourney"]["teammgr_ms_subcaption"]);
		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();

		$templ['index']['info']['content'] .= $mastersearch->GetReturn();
	break;

	// Spieler einem Team hinzufügen - Ausführen
	case 21:
		if ($tteam->join($_GET["teamid"], $_GET["userid"])) $func->confirmation($lang["tourney"]["teammgr_adm_join_success"], "index.php?mod=tournament2&action=teammgr_admin");
	break;

	// Member aus Team löschen
	case 30:
		list($team_id, $user_id) = split("-", $_POST["member_user"], 2);
		if ($tteam->kick($team_id, $user_id)) $func->confirmation($lang["tourney"]["teammgr_adm_delmemb_success"], "index.php?mod=tournament2&action=teammgr_admin");
	break;

	// Neues Team eröffnen - Teamleiter auswählen
	case 40:
		if ($tteam->SignonCheck($vars["tournamentid"])) {
			$mastersearch = new MasterSearch($vars, "index.php?mod=tournament2&action=teammgr_admin&step=40&tournamentid=$tournamentid", "index.php?mod=tournament2&action=teammgr_admin&step=41&tournamentid=$tournamentid&userid=", " AND p.party_id={$party->party_id} AND (p.paid) GROUP BY u.email");
			$mastersearch->LoadConfig("users", $lang["tourney"]["teammgr_adm_ms_caption"], $lang["tourney"]["teammgr_adm_ms_subcaption"]);
			$mastersearch->PrintForm();
			$mastersearch->Search();
			$mastersearch->PrintResult();

			$templ['index']['info']['content'] .= $mastersearch->GetReturn();
		}
	break;

	// Neues Team eröffnen - Teamname eingeben
	case 41:
		$sec->unlock("t_admteam_create");

		$t = $db->query_first("SELECT teamplayer FROM {$config["tables"]["tournament_tournaments"]} WHERE tournamentid = '$tournamentid'");
		if ($t['teamplayer'] == 1) $_POST["team_name"] = $auth["username"];

		if ($tteam->SignonCheckUser($_GET["tournamentid"], $_GET["userid"])) {
			$dsp->SetForm("index.php?mod=tournament2&action=teammgr_admin&step=42&tournamentid=$tournamentid&userid=$userid");
			$dsp->AddTextFieldRow("team_name", $lang["tourney"]["join_teamname"], $_POST["team_name"], "");
			$dsp->AddPasswordRow("set_password", $lang["tourney"]["join_team_pw1"], $_POST["set_password"], $error["set_password"]);
			$dsp->AddPasswordRow("set_password2", $lang["tourney"]["join_team_pw2"], $_POST["set_password2"], $error["set_password2"]);
			$dsp->AddTextAreaPlusRow("team_comment", $lang["tourney"]["join_comment"], $team_comment, "", "", "", 1);
			$dsp->AddFileSelectRow("team_banner", $lang["tourney"]["join_banner"], "", "", 1000000, 1);
			$dsp->AddFormSubmitRow("add");
			$dsp->AddBackButton("index.php?mod=tournament2&action=teammgr_admin", ""); 
		}
	break;

	// Neues Team eröffnen - In DB schreiben
	case 42:
		if ($_POST["set_password"] and $_POST["set_password"] != $_POST["set_password2"]) $func->information("Die Passworteingaben stimmen nicht überein", "index.php?mod=tournament2&action=teammgr_admin&step=41&tournamentid=$tournamentid&userid=$userid");
		elseif ($_POST['team_name'] == "") $func->information($lang["tourney"]["join_err_no_name"], "index.php?mod=tournament2&action=teammgr_admin&step=41&tournamentid=$tournamentid&userid=$userid");

		elseif (!$sec->locked("t_admteam_create")) {
			$t = $db->query_first("SELECT name FROM {$config["tables"]["tournament_tournaments"]} WHERE tournamentid = {$_GET["tournamentid"]}");

			if ($tteam->create($_GET["tournamentid"], $_GET["userid"], $_POST["team_name"], $_POST["set_password"], $_POST["team_comment"], "team_banner")) $func->confirmation(str_replace("%T%", $t["name"], $lang["tourney"]["teammgr_adm_add_success"]), "index.php?mod=tournament2&action=teammgr_admin");

			$sec->lock("t_admteam_create");
		}
	break;


	default:
		$dsp->NewContent($lang["tourney"]["teammgr_adm_caption"], $lang["tourney"]["teammgr_adm_subcaption"]);

		// Neues Team anmelden
		$tourneys = $db->query("SELECT tournamentid, name FROM {$config["tables"]["tournament_tournaments"]} WHERE (status = 'open')  AND party_id='$party->party_id' ORDER BY name");
		if ($db->num_rows($tourneys) == 0) $dsp->AddDoubleRow($lang["tourney"]["teammgr_adm_new_team"], $lang["tourney"]["teammgr_adm_no_new_team"]);
		else {
			$t_array = array("<option value=\"\">{$lang["tourney"]["teammgr_adm_select_t"]}</option>");
			while($tourney = $db->fetch_array($tourneys)) {
				array_push ($t_array, "<option value=\"{$tourney['tournamentid']}\">{$tourney['name']}</option>");
			}
			$dsp->SetForm("index.php?mod=tournament2&action=teammgr_admin&step=40");
			$dsp->AddDropDownFieldRow("tournamentid", $lang["tourney"]["teammgr_adm_new_team"], $t_array, "");
			$dsp->AddFormSubmitRow("send");
		}
		$db->free_result($teams);

		// Team löschen Auswahl
		$teams = $db->query("SELECT teams.teamid, teams.name, t.name AS tname, t.teamplayer
			FROM {$config["tables"]["t2_teams"]} AS teams
			LEFT JOIN {$config["tables"]["tournament_tournaments"]} AS t ON t.tournamentid = teams.tournamentid
			WHERE t.status = 'open' AND t.party_id = '$party->party_id'
			ORDER BY t.name, teams.name
			");
		if ($db->num_rows($teams) == 0) $dsp->AddDoubleRow($lang["tourney"]["teammgr_adm_del_team"], $lang["tourney"]["teammgr_adm_no_teams"]);
		else {
			$t_array = array("<option value=\"\">{$lang["tourney"]["teammgr_adm_select_team"]}</option>");
			while($team = $db->fetch_array($teams)) {
				array_push ($t_array, "<option value=\"{$team['teamid']}\">{$team['tname']} - {$team['name']}</option>");
			}
			$dsp->SetForm("index.php?mod=tournament2&action=teammgr_admin&step=10");
			$dsp->AddDropDownFieldRow("teamid", $lang["tourney"]["teammgr_adm_del_team"], $t_array, "");
			$dsp->AddFormSubmitRow("delete");
		}
		$db->free_result($teams);

		// Spieler hinzufügen Auswahl
		$teams = $db->query("SELECT teams.teamid, teams.name, t.name AS tname, t.teamplayer
			FROM {$config["tables"]["t2_teams"]} AS teams
			LEFT JOIN {$config["tables"]["tournament_tournaments"]} AS t ON t.tournamentid = teams.tournamentid
			WHERE t.teamplayer > 1 AND t.status != 'closed' AND t.party_id = '$party->party_id'
			ORDER BY t.name, teams.name
			");
		if ($db->num_rows($teams) == 0) $dsp->AddDoubleRow($lang["tourney"]["teammgr_adm_addtoteam"], $lang["tourney"]["teammgr_adm_no_waiting"]);
		else {
			$t_array = array("<option value=\"\">{$lang["tourney"]["teammgr_adm_select_team"]}</option>");
			while($team = $db->fetch_array($teams)) {
				$member = $db->query_first("SELECT COUNT(*) AS members FROM {$config["tables"]["t2_teammembers"]} WHERE teamid = {$team['teamid']} GROUP BY teamid");

				$freie_platze = $team['teamplayer'] - ($member['members'] + 1);
				if ($freie_platze > 0) 
					array_push ($t_array, "<option value=\"{$team['teamid']}\">{$team['tname']} - {$team['name']} (". str_replace("%FREE%", $freie_platze,  $lang["tourney"]["admteammgr_free_slots"]) .")</option>");
			}
			$dsp->SetForm("index.php?mod=tournament2&action=teammgr_admin&step=20");
			$dsp->AddDropDownFieldRow("teamid", $lang["tourney"]["teammgr_adm_addtoteam"], $t_array, "");
			$dsp->AddFormSubmitRow("send");
		}
		$db->free_result($teams);

		// Member aus Team löschen - Auswahl
		$teams = $db->query("SELECT teams.teamid, teams.name, t.name AS tname, users.username AS mname, t.teamplayer, members.userid AS userid
			FROM {$config["tables"]["t2_teams"]} AS teams
			LEFT JOIN {$config["tables"]["t2_teammembers"]} AS members ON teams.teamid = members.teamid
			LEFT JOIN {$config["tables"]["user"]} AS users ON members.userid = users.userid
			LEFT JOIN {$config["tables"]["tournament_tournaments"]} AS t ON t.tournamentid = teams.tournamentid
			WHERE t.teamplayer > 1 AND t.party_id = '$party->party_id'
			ORDER BY t.name, teams.name
			");
		if ($db->num_rows($teams) == 0) $dsp->AddDoubleRow($lang["tourney"]["teammgr_adm_delfromteam"], $lang["tourney"]["teammgr_adm_no_memb"]);
		else {
			$t_array = array("<option value=\"\">{$lang["tourney"]["teammgr_adm_select_team"]}</option>");
			while($team = $db->fetch_array($teams)) {
				array_push ($t_array, "<option value=\"{$team['teamid']}-{$team['userid']}\">{$team['tname']} - {$team['name']} - {$team['mname']}</option>");
			}
			$dsp->SetForm("index.php?mod=tournament2&action=teammgr_admin&step=30");
			$dsp->AddDropDownFieldRow("member_user", $lang["tourney"]["teammgr_adm_delfromteam"], $t_array, "");
			$dsp->AddFormSubmitRow("delete");
		}
		$db->free_result($teams);

		$dsp->AddBackButton("index.php?mod=tournament2", "tournament2/teammgr_admin"); 
	break;
}

$dsp->AddContent();
?>
