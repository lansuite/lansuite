<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.1
*	Filename: 			join.php
*	Module: 			Tournamentsystem
*	Main editor: 		jochen@one-network.org
*	Last change: 		26.04.2004
*	Description: 		Undo status changing
*	Remarks: 			
*
**************************************************************************/

$tournamentid 	= $_GET["tournamentid"];

if ($tournamentid == "") $func->error($lang["tourney"]["t_not_exist"], "index.php?mod=tournament2");

else {
	$tournament = $db->query_first("SELECT status, teamplayer, name, mode, blind_draw FROM {$config["tables"]["tournament_tournaments"]} WHERE tournamentid = '$tournamentid'");

	switch ($_GET["action"]) {
		case "undo_generate":
			switch ($_GET["step"]){
				default:
					$func->question($lang["tourney"]["chg_st_question"], "index.php?mod=tournament2&action=undo_generate&step=2&tournamentid=$tournamentid", "index.php?mod=tournament2&action=details&tournamentid=$tournamentid&headermenuitem=1");
				break;

				case 2:
					## Blind-Draw Teas auflösen
					if ($tournament["blind_draw"]) {
						$bd_teams = $db->query("SELECT * FROM {$config["tables"]["t2_teammembers"]} WHERE tournamentid = {$_GET["tournamentid"]}");
						while ($bd_team = $db->fetch_array($bd_teams)) {
							$leader = $db->query_first("SELECT username FROM {$config["tables"]["user"]} WHERE userid = {$bd_team["userid"]}");
							$db->query("INSERT INTO {$config["tables"]["t2_teams"]} 
								SET tournamentid = {$_GET["tournamentid"]},
								name = '{$leader["username"]}',
								leaderid = {$bd_team["userid"]}
								");
							$db->query("DELETE FROM {$config["tables"]["t2_teammembers"]} WHERE teamid = {$bd_team["teamid"]} AND userid = {$bd_team["userid"]}");
						}
					}

					$db->query("DELETE FROM {$config["tables"]["t2_games"]} WHERE tournamentid = '$tournamentid'");
					$db->query("UPDATE {$config["tables"]["tournament_tournaments"]} SET status='open' WHERE tournamentid = '$tournamentid'");

					$func->confirmation(str_replace("%NAME%", $tournament["name"], $lang["tourney"]["chg_st_success"]), "index.php?mod=tournament2&action=details&tournamentid=$tournamentid");
					$func->log_event(str_replace("%NAME%", $tournament["name"], $lang["tourney"]["chg_st_log"]), 1, $lang["tourney"]["log_t_manage"]);
				break;
			}
		break;

		case "undo_close":
			$db->query("UPDATE {$config["tables"]["tournament_tournaments"]} SET status='process' WHERE tournamentid = '$tournamentid'");

			$func->confirmation(str_replace("%NAME%", $tournament["name"], $lang["tourney"]["chg_st_close_success"]), "index.php?mod=tournament2&action=details&tournamentid=$tournamentid");
			$func->log_event(str_replace("%NAME%", $tournament["name"], $lang["tourney"]["chg_st_close_log"]), 1, $lang["tourney"]["log_t_manage"]);
		break;
	}
}
?>
