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
*	Description: 		Generate pairs for the tournament
*	Remarks:
*
**************************************************************************/

$teams = $db->query("SELECT teamid, leaderid, seeding_mark FROM {$config["tables"]["t2_teams"]} WHERE (tournamentid = '{$_GET["tournamentid"]}') ORDER BY RAND()");
$team_anz = $db->num_rows($teams);

$tournament = $db->query_first("SELECT status, teamplayer, name, mode, blind_draw, mapcycle FROM {$config["tables"]["tournament_tournaments"]} WHERE tournamentid = '{$_GET["tournamentid"]}'");

$seeded = $db->query_first("SELECT COUNT(*) AS anz FROM {$config["tables"]["t2_teams"]} WHERE (tournamentid = '{$_GET["tournamentid"]}') AND (seeding_mark = '1') GROUP BY tournamentid");

if ($_GET["step"] < 2 and $tournament["blind_draw"]) $team_anz = floor($team_anz / $tournament["teamplayer"]);


########## Fehler prüfen
## Mind. 4 Teams im Turnier
if ($team_anz < 4) {
	$func->information($lang["tourney"]["g_pairs_tofew_teams"], "index.php?mod=tournament2&action=details&tournamentid={$_GET["tournamentid"]}&headermenuitem=2");

## Bei Gruppen-Modus: Mind. 6 Teams im Turnier
} elseif ($tournament['mode'] == "groups" and $team_anz < 6) {
	$func->information($lang["tourney"]["g_pairs_tofew_teams6"], "index.php?mod=tournament2&action=details&tournamentid={$_GET["tournamentid"]}&headermenuitem=2");

## Satus noch Offen
} elseif ($tournament['status'] != "open") {
	$func->information($lang["tourney"]["g_pairs_started_error"], "index.php?mod=tournament2&action=details&tournamentid={$_GET["tournamentid"]}&headermenuitem=1");

## Nicht mehr als die Hälft geseeded
} elseif (($seeded['anz']) > ($team_anz / 2)){
	$func->information($lang["tourney"]["seeding_error"], "index.php?mod=tournament2&action=details&tournamentid={$_GET["tournamentid"]}&headermenuitem=2");


########## Keine Fehler gefunden
} else {

	if ($_GET["step"] == 2) {
		## Blind-Draw Teams zulosen
		if ($tournament["blind_draw"]) {
			$bd_teams = $db->query("SELECT * FROM {$config["tables"]["t2_teams"]} WHERE (tournamentid = {$_GET["tournamentid"]}) ORDER BY RAND()");
			$z = 0;
			while ($bd_team = $db->fetch_array($bd_teams)) {
				if ($z % $tournament["teamplayer"] == 0) $teamid = $bd_team["teamid"];
				else {
					$db->query("INSERT INTO {$config["tables"]["t2_teammembers"]}
						SET tournamentid = {$_GET["tournamentid"]},
						userid = {$bd_team["leaderid"]},
						teamid = $teamid
						");
					$db->query("DELETE FROM {$config["tables"]["t2_teams"]} WHERE teamid = {$bd_team["teamid"]}");
				}
				$z++;
			}

			// Recalculate team-anz
			$teams = $db->query("SELECT teamid, leaderid, seeding_mark FROM {$config["tables"]["t2_teams"]} WHERE (tournamentid = '{$_GET["tournamentid"]}') ORDER BY RAND()");
			$team_anz = $db->num_rows($teams);
		}

		## Prüfen auf unvollständige Teams
		## Unvollständige Teams zählen
		$waiting_teams = 0;
		$teams2 = $db->query("SELECT name, teamid FROM {$config["tables"]["t2_teams"]} WHERE (tournamentid = {$_GET["tournamentid"]})");
		while($team2 = $db->fetch_array($teams2)) {
			$members = $db->query_first("SELECT COUNT(*) AS members
				FROM {$config["tables"]["t2_teammembers"]}
				WHERE (teamid = {$team2['teamid']})
				GROUP BY teamid
				");
			if(($members["members"] + 1) < $tournament['teamplayer']) $waiting_teams ++;
		}
		$db->free_result($teams2);

		## Wenn unvollständige Teams vorhanden: Fragen, ob löschen
		if (($tournament['teamplayer'] == 1) || ($waiting_teams == 0)) $_GET["step"] = 3;
		else $func->question($lang["tourney"]["g_pairs_uncompleted_question"], "index.php?mod=tournament2&action=generate_pairs&step=4&tournamentid={$_GET["tournamentid"]}", "index.php?mod=tournament2&action=generate_pairs&step=3&tournamentid={$_GET["tournamentid"]}");
	}

	## Unvollständige Teams löschen
	if ($_GET["step"] == 4) {
		$teams2 = $db->query("SELECT teamid, leaderid FROM {$config["tables"]["t2_teams"]} WHERE (tournamentid = {$_GET["tournamentid"]})");
		while($team2 = $db->fetch_array($teams2)) {
			$members = $db->query_first("SELECT COUNT(*) AS members
				FROM {$config["tables"]["t2_teammembers"]}
				WHERE (teamid = {$team2['teamid']})
				GROUP BY teamid
				");
			if(($members["members"] + 1) < $tournament['teamplayer']) {
				$db->query("DELETE FROM {$config["tables"]["t2_teams"]} WHERE (teamid = {$team2["teamid"]}) AND (tournamentid = {$_GET["tournamentid"]})");
				$db->query("DELETE FROM {$config["tables"]["t2_teammembers"]} WHERE (teamid = {$team2["teamid"]}) AND (tournamentid = {$_GET["tournamentid"]})");

				$mail->create_sys_mail($team2['leaderid'], str_replace("%NAME%", $tournament['name'], $lang["tourney"]["g_pairs_mail_fail_subj"]) , str_replace("%NAME%", $tournament['name'], $lang["tourney"]["g_pairs_mail_fail"]));
				$func->log_event(str_replace("%NAME%", $tournament['name'], $lang["tourney"]["g_pairs_log_del"]), 1, $lang["tourney"]["log_t_teammanage"]);
			}
		}
		$db->free_result($teams2);

		$func->question(str_replace("%NAME%", $tournament["name"], $lang["tourney"]["g_pairs_del_success"]), "index.php?mod=tournament2&action=generate_pairs&step=3&tournamentid={$_GET["tournamentid"]}", "index.php?mod=tournament2&action=details&tournamentid={$_GET["tournamentid"]}&headermenuitem=2");
	}


	## Generieren

	//random mapcycle
	$rand_map = explode("\r\n", $func->db2text($tournament["mapcycle"]));
	shuffle($rand_map);
	$db->query("UPDATE {$config["tables"]["tournament_tournaments"]} SET mapcycle = '" . $func->text2db(implode("\r\n", $rand_map)) . "' WHERE tournamentid = {$_GET["tournamentid"]}");

	if ($_GET["step"] == 3) {
		switch ($tournament['mode']) {
			case "single":
			case "double":
				########## Anzahl an benötigten Freilosen bestimmen
				$exp = 0;
				for ($z = $team_anz; $z > 1; $z /= 2) $exp++;
				$needed_freilose = pow(2, $exp) - $team_anz;

				########## Seeding durchführen
				## Teams werden in 2 Array geteilt: Geseedet und Nicht-geseedet
				$seed_team_liste = array();
				$noseed_team_liste = array();

				$teams_num = 0;
				while($team = $db->fetch_array($teams)) {
					$teams_num++;
					if ($team["seeding_mark"]) array_push($seed_team_liste, $team["leaderid"]);
					else array_push($noseed_team_liste, $team["leaderid"]);

					$mail->create_sys_mail($team['leaderid'], str_replace("%NAME%", $tournament['name'], $lang["tourney"]["g_pairs_mail_success_subj"]) , str_replace("%NAME%", $tournament['name'], $lang["tourney"]["g_pairs_mail_success"]));
				}
				$seeded_teams_num = count($seed_team_liste);

				## Jedes wie vielte Element soll geseedet werden?
				($seeded_teams_num) ? $seed_this = ceil($teams_num / $seeded_teams_num) : $seed_this = 0;

				## Die beiden Arrays wieder sortiert zu einem zusammenfügen
				$team_liste = array();
				for ($akt = 1; $akt <= $teams_num; $akt++){
					$error = 0;
					if (($seed_this) && (($akt % $seed_this) == 1)) {
						if (!($akt_leaderid = array_shift($seed_team_liste))) $error = 1;
					} else {
						if (!($akt_leaderid = array_shift($noseed_team_liste))) $error = 1;
					}
					if (!$error) array_push($team_liste, $akt_leaderid);
					if ($error) echo "FEHLER beim Seeding!";
				}


				########## Teams in die Paarungen-Tabelle schreiben
				$pos_round0 = 0;
				$pos_round1 = 0;
				$pos_round05 = 0;
				$pos_roundm1 = 1;

				while ($akt_leaderid = array_shift($team_liste)){
					$db->query("INSERT INTO {$config["tables"]["t2_games"]} SET
							tournamentid = '{$_GET["tournamentid"]}',
							leaderid = $akt_leaderid,
							round = 0,
							position = $pos_round0,
							score = 0
							");
					$pos_round0++;

					// Freilose einfügen
					if ($needed_freilose > 0) {
						$needed_freilose--;
						$db->query("INSERT INTO {$config["tables"]["t2_games"]} SET
							tournamentid = '{$_GET["tournamentid"]}',
							leaderid = 0,
							round = 0,
							position = $pos_round0,
							score = 0
							");
						$pos_round0++;
						// Spieler gegen Freilose in nächste Runde schieben
						$db->query("INSERT INTO {$config["tables"]["t2_games"]} SET
							tournamentid = '{$_GET["tournamentid"]}',
							leaderid = $akt_leaderid,
							round = 1,
							position = $pos_round1,
							score = 0
							");
						$pos_round1++;
						// Freilose ins Loser-Bracket schieben
						$db->query("INSERT INTO {$config["tables"]["t2_games"]} SET
							tournamentid = '{$_GET["tournamentid"]}',
							leaderid = 0,
							round = -0.5,
							position = $pos_round05,
							score = 0
							");
						$pos_round05++;
						// Freilose vs Freilose im Loser-Bracket Runde -0.5 auswerten und nach Runde -1 verschieben
						if (($needed_freilose % 2) == 1) {
							$db->query("INSERT INTO {$config["tables"]["t2_games"]} SET
								tournamentid = '{$_GET["tournamentid"]}',
								leaderid = 0,
								round = -1,
								position = $pos_roundm1,
								score = 0
								");
							$pos_roundm1+=2;
						}
					}
				}
			break;

			case "liga":
			case "groups":
				// Calculate size and number of groups
				$group_anz = 1;
				if ($tournament['mode'] == "groups") {
					$res = 10;
					while ($res >= 3){
						$group_anz *= 2;
						$res = floor($team_anz / $group_anz);
					}
					$group_anz /= 2;
				}
				$num_over_size = $team_anz % $group_anz;

				// for each group, round, position
				for ($group = 1; $group <= $group_anz; $group++){

					$group_size = floor($team_anz / $group_anz);

					// If there are still teams with oversize, increase group size for this group
					$team_liste = array();
					if ($num_over_size > 0){
						$num_over_size--;
						$group_size++;
					}

					// Get teams for this round
					$i = 0;
					while (($i < $group_size) && ($team = $db->fetch_array($teams))) {
						$i++;
						array_push ($team_liste, $team["leaderid"]);
					}
					// If odd, insert faketeam "Geamefree"
					if (floor($group_size / 2) != ($group_size / 2)) {
						array_push ($team_liste, "0");
						$group_size++;
					}

					for ($round = 0; $round < ($group_size-1); $round++) {
						$team_liste_2 = $team_liste;

						// Write games to db
						for ($position = 0; $position < $group_size; $position++) {
							$akt_leader_id = array_shift ($team_liste);
							$db->query("INSERT INTO {$config["tables"]["t2_games"]} SET
									tournamentid = {$_GET["tournamentid"]},
									leaderid = '$akt_leader_id',
									round = $round,
									position = $position,
									group_nr = $group,
									score = 0
									");
						}

						// Rotate position for next round
						array_push ($team_liste, $team_liste_2[0]);
						array_push ($team_liste, $team_liste_2[2]);
						for ($position = 2; $position <= ($group_size-4); $position+=2) {
							array_push ($team_liste, $team_liste_2[$position+2]);
							array_push ($team_liste, $team_liste_2[$position-1]);
						}
						array_push ($team_liste, $team_liste_2[$group_size-1]);
						array_push ($team_liste, $team_liste_2[$group_size-3]);
					}
				}
			break;

			case "all":
				$z = 0;
				while ($team = $db->fetch_array($teams)) {
					$db->query("INSERT INTO {$config["tables"]["t2_games"]} SET
							tournamentid = '{$_GET["tournamentid"]}',
							leaderid = {$team['leaderid']},
							round = 0,
							position = $z,
							score = 0
							");
					$z++;
				}
			break;
		} // Switch Tournament-Mode
		$db->free_result($teams);

		########## Turnierstatus auf "process" setzen
		$db->query("UPDATE {$config["tables"]["tournament_tournaments"]} SET status='process' WHERE tournamentid = '{$_GET["tournamentid"]}'");

		$func->confirmation(str_replace("%NAME%", $tournament["name"], $lang["tourney"]["g_pairs_success"]), "index.php?mod=tournament2&action=details&tournamentid={$_GET["tournamentid"]}");
		$func->log_event(str_replace("%NAME%", $tournament["name"], $lang["tourney"]["g_pairs_log_success"]), 1, $lang["tourney"]["log_t_manage"]);
		$cronjob->load_job("cron_tmod");
		if($tournament['mode'] == "groups"){
			for ($i = 0; $i <= $group_anz; $i++){
				$cronjob->loaded_class->add_job($_GET["tournamentid"],$i);
			}
		}else{
			$cronjob->loaded_class->add_job($_GET["tournamentid"],"");
		}
		
	} // Step = 3
}
?>
