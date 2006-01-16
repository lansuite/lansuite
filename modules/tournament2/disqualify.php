<?php

$teamid = $_GET["teamid"];

$team = $db->query_first("SELECT teams.name, t.name AS t_name, teams.leaderid, teams.tournamentid
		FROM {$config["tables"]["t2_teams"]} AS teams
		LEFT JOIN {$config["tables"]["tournament_tournaments"]} AS t ON t.tournamentid = teams.tournamentid
		WHERE (teams.teamid = $teamid)
		");

if (!$team['tournamentid']) $func->error($lang["tourney"]["t_not_exist"], "");
else switch ($_GET["step"]){
	// Disqualify-Question
	default:
		$func->question(str_replace("%NAME%", $team['name'], str_replace("%T%", $team['t_name'], $lang["tourney"]["disqualify_question"])), "index.php?mod=tournament2&action=disqualify&step=2&teamid=$teamid", $func->internal_referer);
	break;

	// Disqualify
	case 2:
		$db->query("UPDATE {$config["tables"]["t2_teams"]} SET disqualified='1' WHERE (teamid = $teamid)");

		include("modules/tournament2/class_tournament.php");
		$tfunc = new tfunc;

		$team2['teamid'] = 1;
		while ($team2['teamid']){
			$team2 = $db->query_first("SELECT games1.gameid AS gid1, games2.gameid AS gid2, teams1.teamid
				FROM {$config["tables"]["t2_games"]} AS games1
				INNER JOIN {$config["tables"]["t2_games"]} AS games2 ON (games1.round = games2.round) AND ((games1.position + 1) = games2.position) AND (games1.tournamentid = games2.tournamentid)
				LEFT JOIN {$config["tables"]["t2_teams"]} AS teams1 ON (games1.leaderid = teams1.leaderid) AND (games1.tournamentid = teams1.tournamentid)
				LEFT JOIN {$config["tables"]["t2_teams"]} AS teams2 ON (games2.leaderid = teams2.leaderid) AND (games2.tournamentid = teams2.tournamentid)
				WHERE ((games1.position / 2) = FLOOR(games1.position / 2))
				AND (games1.score = 0) AND (games2.score = 0)
				AND ((teams1.teamid = $teamid) OR (teams2.teamid = $teamid))
				");

			if ($team2['teamid']){
				// Set score to default win for opponent
				if ($cfg["t_default_win"] == 0) $cfg["t_default_win"] = 2;
				if ($team2['teamid'] == $teamid) {
					$score1 = 0;
					$score2 = $cfg["t_default_win"];
				} else {
					$score1 = $cfg["t_default_win"];
					$score2 = 0;
				}
				$tfunc->SubmitResult($team['tournamentid'], $team2['gid1'], $team2['gid2'], $score1, $score2, addslashes(str_replace("%NAME%", $team['name'], $lang["tourney"]["disqualify_comment"])));
			}
		}

		$func->log_event(str_replace("%NAME%", $team['name'], str_replace("%T%", $team['t_name'], $lang["tourney"]["disqualify_log"])), 1, $lang["tourney"]["log_t_teammanage"]);

		$mail->create_sys_mail($team['leaderid'], str_replace("%NAME%", $team['name'], str_replace("%T%", $team['t_name'], $lang["tourney"]["disqualify_mail_subj"])), str_replace("%NAME%", $team['name'], str_replace("%T%", $team['t_name'], $lang["tourney"]["disqualify_mail"])));

		$func->confirmation(str_replace("%NAME%", $team['name'], str_replace("%T%", $team['t_name'], $lang["tourney"]["disqualify_success"])), "index.php?mod=tournament2");
	break;


	// Un-Disqualify
	case 10:
		$db->query("UPDATE {$config["tables"]["t2_teams"]} SET disqualified='0' WHERE (teamid = $teamid)");

		$func->log_event(str_replace("%NAME%", $team['name'], str_replace("%T%", $team['t_name'], $lang["tourney"]["undisqualify_log"])), 1, $lang["tourney"]["log_t_teammanage"]);

		$mail->create_sys_mail($team['leaderid'], str_replace("%NAME%", $team['name'], str_replace("%T%", $team['t_name'], $lang["tourney"]["undisqualify_mail_subj"])), str_replace("%NAME%", $team['name'], str_replace("%T%", $team['t_name'], $lang["tourney"]["undisqualify_mail"])));

		$func->confirmation(str_replace("%NAME%", $team['name'], str_replace("%T%", $team['t_name'], $lang["tourney"]["undisqualify_success"])), "index.php?mod=tournament2");
	break;
}
?>
