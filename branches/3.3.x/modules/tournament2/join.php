<?php

include("modules/tournament2/class_team.php");
$tteam = new team;

$tournamentid 	= $_GET["tournamentid"];

$tournament = $db->query_first("SELECT name, teamplayer, over18, status, groupid, coins, wwcl_gameid, ngl_gamename, lgz_gamename, maxteams FROM {$config["tables"]["tournament_tournaments"]} WHERE tournamentid = '$tournamentid'");

if ($auth["userid"] == "") $auth["userid"] = 0;

$user = $db->query_first("SELECT wwclid, wwclclanid, nglid, nglclanid, lgzid, lgzclanid FROM {$config["tables"]["user"]} WHERE userid = '{$auth["userid"]}'");


if ($tteam->SignonCheck($tournamentid)) {
	switch($_GET["step"]) {
		case 3:
			if (!$sec->locked("t_join")) {
				$error = array();

        // If joining an existing team
				if (($_POST['existing_team_name'] != "") and ($tournament['teamplayer'] > 1)) {
					$success = $tteam->join($_POST["existing_team_name"], $auth["userid"], $_POST["password"]);

        // If creating a new team
				} else {
					if ($tournament['teamplayer'] == 1) $_POST['team_name'] = "";

					if ($_POST["set_password"] and $_POST["set_password"] != $_POST["set_password2"]) $error["set_password2"] = "Die Passworteingaben stimmen nicht überein";
					if ($_POST['team_name'] == "" and $tournament['teamplayer'] > 1) $error["team_name"] = $lang["tourney"]["join_err_no_name"];
					if (count($error) == 0) $success = $tteam->create($_GET["tournamentid"], $auth["userid"], $_POST['team_name'], $_POST["set_password"], $_POST['team_comment'], "team_banner");
				}

				if (count($error) == 0 and $success) {
					// Update-League-IDs
					$tteam->UpdateLeagueIDs($auth["userid"], $_POST["wwclid"], $_POST["wwclclanid"], $_POST["nglid"], $_POST["nglclanid"], $_POST["lgzid"], $_POST["lgzclanid"]);

					$func->confirmation(str_replace("%NAME%", $tournament["name"], $lang["tourney"]["join_success"]), "index.php?mod=tournament2&action=details&tournamentid=$tournamentid");
				}
				$sec->lock("t_join");
			}

			if (count($error) > 0) $_GET['step']--;
		break;
	}

	switch($_GET["step"]) {
		case 2:
			$sec->unlock("t_join");

			$dsp->NewContent(str_replace("%NAME%", $tournament['name'], $lang["tourney"]["join_caption"]), $lang["tourney"]["join_subcaption"]);

			$dsp->SetForm("index.php?mod=tournament2&action=join&step=3&tournamentid=$tournamentid", "", "", "multipart/form-data");

			if ($tournament['teamplayer'] == 1 or $tournament['blind_draw'] == 1) $dsp->AddDoubleRow($lang["tourney"]["join_teamname"], $auth["username"]);
			else {
				$dsp->AddSingleRow("<b>". $lang["tourney"]["join_j_existing"] ."</b>");

				// Vorhandene Teams
				$t_array = array("<option $selected value=\"\">-{$lang["tourney"]["join_create_new_team"]}-</option>");
				$teams = $db->query("SELECT teamid, name FROM {$config["tables"]["t2_teams"]} WHERE tournamentid = $tournamentid");
				while ($team = $db->fetch_array($teams)) {
					if ($_POST["existing_team_name"] == $team['teamid']) $selected = "selected";
					array_push ($t_array, "<option $selected value=\"{$team['teamid']}\">{$team['name']}</option>");
				}
				$db->free_result($teams);
				$dsp->AddDropDownFieldRow("existing_team_name", $lang["tourney"]["join_j_team"], $t_array, "");
				$dsp->AddPasswordRow("password", $lang["tourney"]["join_team_pw"], $_POST["password"], $error["password"]);

				// Neues Team
				$dsp->AddSingleRow("<b>". $lang["tourney"]["join_or_new"] ."</b>");
				$dsp->AddTextFieldRow("team_name", $lang["tourney"]["join_teamname"], $_POST["team_name"], $error["team_name"]);
				$dsp->AddPasswordRow("set_password", $lang["tourney"]["join_team_pw1"], $_POST["set_password"], $error["set_password"]);
				$dsp->AddPasswordRow("set_password2", $lang["tourney"]["join_team_pw2"], $_POST["set_password2"], $error["set_password2"]);
			}

			$dsp->AddTextAreaPlusRow("team_comment", $lang["tourney"]["join_comment"], $_POST["team_comment"], "", "", "", 1);
			$dsp->AddFileSelectRow("team_banner", $lang["tourney"]["join_banner"], "", "", 1000000, 1);

			if ($tournament['wwcl_gameid'] > 0){
				$dsp->AddTextFieldRow("wwclid", $lang["tourney"]["join_wwcl_id"], $user['wwclid'], "");
				if ($tournament['teamplayer'] > 1) $dsp->AddTextFieldRow("wwclclanid", $lang["tourney"]["join_wwcl_clan_id"], $user['wwclclanid'], "");
			}
			if ($tournament['ngl_gamename'] != ""){
				$dsp->AddTextFieldRow("nglid", $lang["tourney"]["join_ngl_id"], $user['nglid'], "");
				if ($tournament['teamplayer'] > 1) $dsp->AddTextFieldRow("nglclanid", $lang["tourney"]["join_ngl_clan_id"], $user['nglclanid'], "");
			}
			if ($tournament['lgz_gamename'] != ""){
				$dsp->AddDoubleRow($lang["tourney"]["join_lgz_id"], $lang["tourney"]["join_lgz_info"]);
				$dsp->AddTextFieldRow("lgzid", "", $user['lgzid'], "");
				if ($tournament['teamplayer'] > 1) $dsp->AddTextFieldRow("lgzclanid", $lang["tourney"]["join_lgz_clan_id"], $user['lgzclanid'], "");
			}

			$dsp->AddFormSubmitRow("join");
			$dsp->AddBackButton("index.php?mod=tournament2&action=details&tournamentid=$tournamentid", "tournament2/join"); 

			$dsp->AddContent();
		break;
	}
}
?>
