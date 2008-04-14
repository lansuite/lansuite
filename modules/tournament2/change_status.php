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

if ($tournamentid == "") $func->error(t('Das ausgewählte Turnier existiert nicht'), "index.php?mod=tournament2");

else {
	$tournament = $db->query_first("SELECT status, teamplayer, name, mode, blind_draw FROM {$config["tables"]["tournament_tournaments"]} WHERE tournamentid = '$tournamentid'");

	switch ($_GET["action"]) {
		case "undo_generate":
			switch ($_GET["step"]){
				default:
					$func->question(t('Sind Sie sicher, dass Sie das generieren rückgängig machen wollen? Alle Paarungen und alle bereits eingetragenen Ergebnisse dieses Turnieres werden dabei gelöscht! Bei bereits beendeten Turnieren geht dadurch außerdem die Rangliste verloren!'), "index.php?mod=tournament2&action=undo_generate&step=2&tournamentid=$tournamentid", "index.php?mod=tournament2&action=details&tournamentid=$tournamentid&headermenuitem=1");
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

					$func->confirmation(str_replace("%NAME%", $tournament["name"], t('Das Turnier \'%NAME%\' wurde erfolgreich zurückgesetzt')), "index.php?mod=tournament2&action=details&tournamentid=$tournamentid");
					$func->log_event(str_replace("%NAME%", $tournament["name"], t('Das Generieren des Turnieres \'%NAME%\' wurde rückgängig gemacht')), 1, t('Turnier Verwaltung'));
				break;
			}
		break;

		case "undo_close":
			$db->query("UPDATE {$config["tables"]["tournament_tournaments"]} SET status='process' WHERE tournamentid = '$tournamentid'");

			$func->confirmation(str_replace("%NAME%", $tournament["name"], t('Der Status wurde wieder auf \'wird gespielt\' gesetzt. Das Turnier wird wieder beendet, sobald Sie das nächste Ergebniss eingetragen haben.')), "index.php?mod=tournament2&action=details&tournamentid=$tournamentid");
			$func->log_event(str_replace("%NAME%", $tournament["name"], t('Der Status wurde wieder auf \'wird gespielt\' gesetzt')), 1, t('Turnier Verwaltung'));
		break;
	}
}
?>