<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System03.05.2004
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.1
*	Filename: 			class_tournament.php
*	Module: 			Tournamentsystem
*	Main editor: 		jochen@orgapage.net
*	Last change: 		22.08.2003 17:05
*	Description: 		tournament exports + functions
*	Remarks: 			
*				
**************************************************************************/


class ranking_data {
	var $id = array();
	var $pos = array();
	var $tid = array();
	var $name = array();
	var $win = array();
	var $score = array();
	var $score_en = array();
	var $score_dif = array();
	var $games = array();
	var $disqualified = array();
	var $reached_finales = array();
}


class tfunc {
	function AddPentRow($key, $value1, $value2, $value3, $value4) {
		global $dsp, $templ;

		$templ['ls']['row']['pent']['key'] = $key;
		$templ['ls']['row']['pent']['value1'] = $value1;
		$templ['ls']['row']['pent']['value2'] = $value2;
		$templ['ls']['row']['pent']['value3'] = $value3;
		$templ['ls']['row']['pent']['value4'] = $value4;

		$dsp->AddModTpl("tournament2", "ls_row_pent");
	}


	// Generates a string to output a memberlist of one team
	function GetMemberList($teamid){
		global $db, $config, $func, $seat2, $lang;

		$member_liste = "";
		$team_memb = $db->query("SELECT user.username, user.userid
				FROM {$config["tables"]["t2_teammembers"]} AS teammember
				LEFT JOIN {$config["tables"]["user"]} AS user ON teammember.userid = user.userid
				WHERE teammember.teamid = $teamid");
		while ($member = $db->fetch_array($team_memb)) $member_liste .= $member['username'] . $func->button_userdetails($member['userid'], "") . " (Platz: ". $seat2->SeatNameLink($member['userid'], '', '') .")" . HTML_NEWLINE;
		$db->free_result($team_memb);

		if ($member_liste == "") return "<i>".t('Keine')."</i>";
		else return $member_liste;
	}


	// Get the number of teams in this tournament
	function GetTeamAnz($tid, $mode, $group = 0) {
		global $db, $config;

		if (($mode == "groups") and ($group == 0)) {
			$game = $db->query("SELECT gameid
				FROM {$config["tables"]["t2_games"]}
				WHERE (tournamentid = $tid) AND (group_nr > 0)
				GROUP BY group_nr
				");
			$team_anz = 2 * $db->num_rows($game);
			$db->free_result($game);
			return $team_anz;

		} else {
			if ($mode != "groups") $group = 0;
			if ($mode == "liga") $group = 1;

			## In liga-mode dye's do not count as team, in ko-modes they do
			($mode == "liga" or $mode == "groups")? $add_where = "AND (leaderid != 0)" : $add_where = "";

			$games = $db->query_first("SELECT COUNT(*) AS anz
				FROM {$config["tables"]["t2_games"]}
				WHERE (tournamentid = $tid) AND (round = 0) AND (group_nr = $group) $add_where
				GROUP BY round
				");
			return $games['anz'];
		}
	}


	## Returns the time, when the given round in this tournament starts
	function GetGameStart($tournament, $round, $group_nr = 0) {
		global $db, $config;

		$break_duration = $tournament["break_duration"] * 60;
		$round_duration = $tournament["max_games"] * $tournament["game_duration"] * 60 + $break_duration;
		($tournament['mode'] == "double")? $faktor = 2 : $faktor = 1;

		## If final games of a group-tournament add time for group-games
		if (($tournament["mode"] == "groups") and ($group_nr == 0)){
			## Count numer of teams of the first group
			$get_team_anz = $db->query_first("SELECT COUNT(*) AS anz
					FROM {$config["tables"]["t2_games"]}
					WHERE (tournamentid = {$tournament["tournamentid"]}) AND (round = 0) AND (group_nr = 1)
					GROUP BY group_nr
					");
			$team_anz = $get_team_anz["anz"];

			$tournament["starttime"] += $round_duration * ($team_anz - 1) * $faktor;
		}

    if ($tournament["mode"] == 'single') {
  		return $tournament["starttime"] + $round_duration * (abs($round));
    } else {
  		if ($round > 0) return $tournament["starttime"] + $round_duration * ($round - 0.5) * $faktor;
  		else return $tournament["starttime"] + $round_duration * abs($round) * $faktor;
    }
	}


	## Returns the time, when the given round in this tournament ends
	function GetGameEnd($tournament, $round, $group_nr = 0) {
		global $db, $config;

		$break_duration = $tournament["break_duration"] * 60;
		$round_duration = $tournament["max_games"] * $tournament["game_duration"] * 60 + $break_duration;
		($tournament['mode'] == "double")? $faktor = 2 : $faktor = 1;

		## If final games of a group-tournament add time for group-games
		if (($tournament["mode"] == "groups") and ($group_nr == 0)){
			## Count numer of teams of the first group
			$get_team_anz = $db->query_first("SELECT COUNT(*) AS anz
					FROM {$config["tables"]["t2_games"]}
					WHERE (tournamentid = {$tournament["tournamentid"]}) AND (round = 0) AND (group_nr = 1)
					GROUP BY group_nr
					");
			$team_anz = $get_team_anz["anz"];

			$tournament["starttime"] += $round_duration * ($team_anz - 1) * $faktor;
		}

    if ($tournament["mode"] == 'single') {
  		return $tournament["starttime"] + $round_duration * (abs($round + 1)) - $break_duration;
    } else {
  		if ($round > 0) return $tournament["starttime"] + $round_duration * ($round + 1 - 0.5) * $faktor - $break_duration;
  		else return $tournament["starttime"] + $round_duration * (abs($round) + 0.5) * $faktor  - $break_duration;
    }
	}


	function GetNextRanks($akt_round, $tournamentid, $ranking_data) {
		global $db, $config, $akt_round, $num, $array_id;

		$teams = $db->query("SELECT teams.teamid, teams.name, teams.disqualified
			FROM {$config["tables"]["t2_games"]} AS games
			LEFT JOIN {$config["tables"]["t2_teams"]} AS teams ON (teams.leaderid = games.leaderid) AND (teams.tournamentid = games.tournamentid)
			WHERE (games.tournamentid = $tournamentid) AND (games.round = $akt_round)
			ORDER BY games.score DESC
			");
		while($team = $db->fetch_array($teams)) if (!in_array ($team['teamid'], $ranking_data->tid)){
			$array_id++;
			array_push ($ranking_data->id, $array_id);
			array_push ($ranking_data->tid, $team['teamid']);
			array_push ($ranking_data->name, $team['name']);
			array_push ($ranking_data->pos, $num++);
			array_push ($ranking_data->disqualified, $team['disqualified']);
		}
		$db->free_result($teams);

		return $ranking_data;
	}



	function get_ranking ($tournamentid, $group_nr = NULL) {
		global $db, $config, $akt_round, $num, $cfg, $array_id;

		$ranking_data = new ranking_data;

		$tournament = $db->query_first("SELECT mode FROM {$config["tables"]["tournament_tournaments"]} WHERE tournamentid = $tournamentid");

		$games = $db->query("SELECT gameid FROM {$config["tables"]["t2_games"]} WHERE (tournamentid = $tournamentid) AND (round=0)");
		$team_anz = $db->num_rows($games);
		$db->free_result($games);

		$akt_round = 0;
		$num = 1;
		$array_id = 0;


		// Je nach Modus ergibt sich ein anderes Ranking
		switch ($tournament['mode']) {
			case 'all':
				$teams = $db->query("SELECT teams.name, teams.teamid, teams.disqualified, games.leaderid, games.score, games.gameid
						FROM {$config["tables"]["t2_games"]} AS games
						LEFT JOIN {$config["tables"]["t2_teams"]} AS teams ON (games.tournamentid = teams.tournamentid) AND (games.leaderid = teams.leaderid)
						WHERE games.tournamentid = '$tournamentid'
						ORDER BY teams.disqualified ASC, games.score DESC, games.position ASC
						");
				while ($team = $db->fetch_array($teams)) {
					$array_id++;
					array_push ($ranking_data->id, $array_id);
					array_push ($ranking_data->tid, $team['teamid']);
					array_push ($ranking_data->name, $team['name']);
					array_push ($ranking_data->pos, $num++);
					array_push ($ranking_data->disqualified, $team['disqualified']);
				}
				$db->free_result($teams);
			break;

			case "single":
			case "double":
				// Array für Teams auslesen
				$teams = $db->query("SELECT teams.teamid, teams.name, teams.disqualified, MAX(games.round) AS rounds
					FROM {$config["tables"]["t2_games"]} AS games
					LEFT JOIN {$config["tables"]["t2_teams"]} AS teams ON (teams.leaderid = games.leaderid) AND (teams.tournamentid = games.tournamentid)
					WHERE games.tournamentid = $tournamentid AND NOT ISNULL( teams.name )
					GROUP BY teams.teamid
					ORDER BY teams.disqualified ASC, rounds DESC, games.score DESC
					");
			
				// Bei Doublemodus die ersten 2 Plätze auslesen und Array neu auslesen
				if($tournament['mode'] == "double"){
					for ($i = 0; $i < 2;$i++){
						$team = $db->fetch_array($teams);
						if ($team['teamid']){
							$array_id++;
							array_push ($ranking_data->id, $array_id);
							array_push ($ranking_data->tid, $team['teamid']);
							array_push ($ranking_data->name, $team['name']);
							array_push ($ranking_data->pos, $num++);
							array_push ($ranking_data->disqualified, $team['disqualified']);
						}
					}
					$db->free_result($teams);

					// Teams auslesen und in Array schreiben
					$teams = $db->query("SELECT teams.teamid, teams.name, teams.disqualified, MIN(games.round) AS rounds
					FROM {$config["tables"]["t2_games"]} AS games
					LEFT JOIN {$config["tables"]["t2_teams"]} AS teams ON (teams.leaderid = games.leaderid) AND (teams.tournamentid = games.tournamentid)
					WHERE games.tournamentid = $tournamentid
					GROUP BY teams.teamid
					ORDER BY teams.disqualified ASC, rounds ASC, games.score DESC
					");
				}

				// Array schreiben
				while ($team = $db->fetch_array($teams)) if ($team['teamid'] && !in_array($team['teamid'],$ranking_data->tid)){
					$array_id++;
					array_push ($ranking_data->id, $array_id);
					array_push ($ranking_data->tid, $team['teamid']);
					array_push ($ranking_data->name, $team['name']);
					array_push ($ranking_data->pos, $num++);
					array_push ($ranking_data->disqualified, $team['disqualified']);
				}
				$db->free_result($teams);

				/*array_multisort ($ranking_data->disqualified, SORT_ASC, SORT_NUMERIC,
							$ranking_data->id, SORT_ASC, SORT_NUMERIC,
							$ranking_data->tid,
							$ranking_data->name,
							$ranking_data->pos);*/
			break;

			case "liga":
			case "groups":
	 			if ($group_nr == '') $group_nr = 1;
	 			
				// Beteiligte Teams in Array einlesen
				$teams = $db->query("SELECT teamid, name, disqualified
					FROM {$config["tables"]["t2_teams"]}
					WHERE (tournamentid = $tournamentid)
					GROUP BY teamid
					ORDER BY teamid
					");

				$i = 0;
				while ($team = $db->fetch_array($teams)){
					$i++;
					array_push ($ranking_data->pos, $i);
					array_push ($ranking_data->tid, $team["teamid"]);
					array_push ($ranking_data->name, $team['name']);
					array_push ($ranking_data->disqualified, $team['disqualified']);
					array_push ($ranking_data->reached_finales, 0);
					array_push ($ranking_data->win, 0);
					array_push ($ranking_data->score, 0);
					array_push ($ranking_data->score_en, 0);
					array_push ($ranking_data->games, 0);
				}

				$scores = $db->query("SELECT teams1.teamid AS tid1, teams2.teamid AS tid2,  games1.score AS s1, games2.score AS s2, games1.group_nr
					FROM {$config["tables"]["t2_games"]} AS games1
					LEFT JOIN {$config["tables"]["t2_games"]} AS games2 ON (games1.tournamentid = games2.tournamentid) AND (games1.round = games2.round) AND (games1.group_nr = games2.group_nr)
					LEFT JOIN {$config["tables"]["t2_teams"]} AS teams1 ON (games1.leaderid = teams1.leaderid) AND (games1.tournamentid = teams1.tournamentid)
					LEFT JOIN {$config["tables"]["t2_teams"]} AS teams2 ON (games2.leaderid = teams2.leaderid) AND (games2.tournamentid = teams2.tournamentid)
					WHERE (games1.tournamentid = $tournamentid)
					AND ((games1.position + 1) = games2.position)
					AND ((games1.position / 2) = FLOOR(games1.position / 2))
					AND ((games1.score != 0) OR (games2.score != 0))
					AND games1.group_nr = '$group_nr'
					");

				while ($score = $db->fetch_array($scores)){
					if ($tournament['mode'] == "groups" and $group_nr == 0){
						$ranking_data->reached_finales[array_search($score['tid1'], $ranking_data->tid)] = 1;
						$ranking_data->reached_finales[array_search($score['tid2'], $ranking_data->tid)] = 1;
					}
					$ranking_data->score[array_search($score['tid1'], $ranking_data->tid)] += $score['s1'];
					$ranking_data->score[array_search($score['tid2'], $ranking_data->tid)] += $score['s2'];
					$ranking_data->score_en[array_search($score['tid1'], $ranking_data->tid)] += $score['s2'];
					$ranking_data->score_en[array_search($score['tid2'], $ranking_data->tid)] += $score['s1'];

					$ranking_data->games[array_search($score['tid1'], $ranking_data->tid)] ++;
					$ranking_data->games[array_search($score['tid2'], $ranking_data->tid)] ++;

					if ($score['s1'] == $score['s2']) {
						$ranking_data->win[array_search($score['tid1'], $ranking_data->tid)] += 1;
						$ranking_data->win[array_search($score['tid2'], $ranking_data->tid)] += 1;
					} elseif ($score['s1'] > $score['s2']) {
						$ranking_data->win[array_search($score['tid1'], $ranking_data->tid)] += $cfg["t_league_points"];
					} elseif ($score['s1'] < $score['s2']) {
						$ranking_data->win[array_search($score['tid2'], $ranking_data->tid)] += $cfg["t_league_points"];
					}
				}
				$db->free_result($teams);

				// Sortieren
				$teams_array_tmp = $ranking_data->tid;
				$i = 0;
				while (array_shift($teams_array_tmp)){
					array_push ($ranking_data->score_dif, ($ranking_data->score[$i] - $ranking_data->score_en[$i]));
					$i++;
				}
				array_multisort ($ranking_data->disqualified, SORT_ASC, SORT_NUMERIC,
					$ranking_data->reached_finales, SORT_DESC, SORT_NUMERIC,
					$ranking_data->win, SORT_DESC, SORT_NUMERIC,
					$ranking_data->score_dif, SORT_DESC, SORT_NUMERIC,
					$ranking_data->score, SORT_DESC, SORT_NUMERIC,
					$ranking_data->score_en, SORT_ASC, SORT_NUMERIC,
					$ranking_data->tid, SORT_ASC, SORT_NUMERIC,
					$ranking_data->name, SORT_ASC, SORT_STRING,
					$ranking_data->games, SORT_ASC, SORT_NUMERIC);
			break;
		}

		return $ranking_data;
	}


	function button_team_details($teamid, $tournamentid) {
		global $auth;

		if ($teamid) {
			$link = " <a href=\"index.php?mod=tournament2&action=tdetails&tournamentid=$tournamentid&teamid=$teamid\"><img src=\"design/". $auth["design"] ."/images/arrows_search.gif\" width=\"12\" height=\"13\" border=\"0\"></a>";
			return $link;
		}
	}


	// Generate the next position in a KO-Tournament, if a score is submitted
	function GenerateNewPosition($player1, $player2){
		global $lang, $db, $func, $config, $tournamentid, $round, $pos, $score, $tournamentid, $leaderid, $num_rounds, $team_anz;

		$team_round[$player1] = $round;
		$team_pos[$player1] = $pos[$player1];

		$team_pow_anz = $team_anz;
		for ($z = 0; $team_pow_anz > 1; $z++) $team_pow_anz /= 2;
		$team_pow_anz = pow(2, $z);

		$team_round_anz = $team_pow_anz;
		for ($z = 0; $z < abs($round); $z++) $team_round_anz /= 2;

		($score[$player1] > $score[$player2]) ? $winner = 1
			: $winner = 0;

		($score[$player1] < $score[$player2]) ? $looser = 1
			: $looser = 0;

		// Runden-Berechnung
		# Gewinnt jemand im Winner-Bracket, wird seine Runde um eins erhöht.
		if ($round >= 0 and $winner) $team_round[$player1]++;

		# Gewinnt jemand im Loser-Bracket, oder verliert das allererste Spiel, so wird seiner Runde 0.5 abgezogen.
		elseif (($round < 0 and $winner) or ($round == 0 and $looser)) $team_round[$player1] -= 0.5;

		# Verliert jemand im Winner-Bracket, wird seine Runde mit -1 multipliziert.
		elseif ($round > 0 and $looser) $team_round[$player1] *= (-1);

		# Gewinnt jemand das Loser-Bracket, so wird seine Runde mit -1 multipliziert und anschließend 0.5 addiert. 
		if ($round == ($num_rounds * (-1) + 1)) $team_round[$player1] = $team_round[$player1] * (-1) + 0.5;


		// Positions-Berechnung
		# Die Possition wird bei Siegern in ganzzahligen Runden und Verlieren der allerersten Runde halbiert
		if (($round == floor($round) and $winner) or ($looser and $round == 0)) $team_pos[$player1] = floor($team_pos[$player1] / 2);

		# Die Possition wird bei Siegern in 0.5-Runde und beim Gewinner des LB jeweils bei geraden Zahlen um 1 erhöht
		if (($round != floor($round) and $winner) or ($round == ($num_rounds * (-1) + 1))) $team_pos[$player1] = floor($team_pos[$player1] / 2) * 2 + 1;

		# Bei Verlierern im WB wird bei ungeraden Zahlen (in geraden Runden) 1 abgezogen und
		elseif (($round > 0) and $looser and (floor($round / 2) == $round / 2)) $team_pos[$player1] = floor($team_pos[$player1] / 2) * 2;

		# Bei Verlierern im WB wird bei ungeraden Zahlen (in ungeraden Runden) 1 abgezogen und das Ergebnis von der Teamanzahl dieser Runde - 2 abgezogen (zum Spiegeln des Baumes)
		elseif (($round > 0) and $looser and (floor($round / 2) != $round / 2)) $team_pos[$player1] = $team_round_anz - 2 - floor($team_pos[$player1] / 2) * 2;


		# Wenn im LB, oder Finale verloren wurde -> ausgeschieden. Sonst neuer Eintrag
		if ($winner or ($looser and $round >= 0 and $round != $num_rounds)) {
			$db->query("DELETE FROM {$config["tables"]["t2_games"]}
				WHERE (tournamentid = $tournamentid) AND (round = $team_round[$player1]) AND (position = $team_pos[$player1]) AND (group_nr = 0)
				");

			$db->query("INSERT INTO {$config["tables"]["t2_games"]} SET
				tournamentid = '$tournamentid',
				leaderid = '{$leaderid[$player1]}',
				round = {$team_round[$player1]},
				position = {$team_pos[$player1]},
				score = 0
				");
		}


		// Freilose in Runde -0.5 und -1
		if ($team_round[$player1] == -0.5) {
			if ($team_pos[$player1] % 2 == 0) $en_position = $team_pos[$player1] + 1;
			else $en_position = $team_pos[$player1] - 1;

			# Daten des neuen Gegners auslesen
			$en_game = $db->query_first("SELECT gameid
				FROM {$config["tables"]["t2_games"]}
				WHERE (tournamentid = '$tournamentid') AND (position = $en_position) AND (round = -0.5) AND (leaderid = 0)
				");

			# Wenn neuer Gegner ein Freilos, Spieler eine Runde weiter schieben
			if ($en_game['gameid'] != 0) {
				$db->query("DELETE FROM {$config["tables"]["t2_games"]}
					WHERE (tournamentid = $tournamentid) AND (round = -1) AND (position = ". (floor($team_pos[$player1]/2)*2 + 1) .") AND (group_nr = 0)
					");

				$query = $db->query("INSERT INTO {$config["tables"]["t2_games"]}
					SET tournamentid = '$tournamentid',
					leaderid = '{$leaderid[$player1]}',
					round = -1,
					position = ". (floor($team_pos[$player1]/2)*2 + 1) .",
					score = 0
					");
			}
		}
		if ($team_round[$player1] == -1) {
			if ($team_pos[$player1] % 2 == 0) $en_position = $team_pos[$player1] + 1;
			else $en_position = $team_pos[$player1] - 1;

			# Daten des neuen Gegners auslesen
			$en_game = $db->query_first("SELECT gameid
				FROM {$config["tables"]["t2_games"]}
				WHERE (tournamentid = '$tournamentid') AND (position = $en_position) AND (round = -1) AND (leaderid = 0)
				");

			# Wenn neuer Gegner ein Freilos, Spieler eine Runde weiter schieben
			if ($en_game['gameid'] != 0) {
				$db->query("DELETE FROM {$config["tables"]["t2_games"]}
					WHERE (tournamentid = $tournamentid) AND (round = -1.5) AND (position = ". floor($team_pos[$player1]/2) .") AND (group_nr = 0)
					");

				$query = $db->query("INSERT INTO {$config["tables"]["t2_games"]}
					SET tournamentid = '$tournamentid',
					leaderid = '". $leaderid[$player1] ."',
					round = -1.5,
					position = ". floor($team_pos[$player1]/2) .",
					score = 0
					");
			}
		}
	}


	// Sumbit Score $score1:$score2 in the tournament $tournamentid, for the game $gameid1 vs. $gameid2
	function SubmitResult($ttid, $gameid1, $gameid2, $score1, $score2, $comment) {
		global $lang, $db, $func, $config, $tournamentid, $round, $pos, $score, $leaderid, $num_rounds, $team_anz;
		$tournamentid = $ttid;
		$score[1] = $score1;
		$score[2] = $score2;

		// Read data
		$tournament = $db->query_first("SELECT name, mode FROM {$config["tables"]["tournament_tournaments"]} WHERE tournamentid = $tournamentid");

		$gr_game = $db->query_first("SELECT group_nr
				FROM {$config["tables"]["t2_games"]}
				WHERE gameid= $gameid1
				");

		$team_anz = $this->GetTeamAnz($tournamentid, $tournament['mode'], $gr_game["group_nr"]);

		$team1 = $db->query_first("SELECT games.position, games.leaderid, games.round
			FROM {$config["tables"]["t2_games"]} AS games
			WHERE (games.tournamentid = $tournamentid) AND (games.gameid = $gameid1)
			");
		$round = $team1["round"];
		$pos[1] = $team1["position"];
		$leaderid1 = $team1["leaderid"];
		$leaderid[1] = $leaderid1;

		$team2 = $db->query_first("SELECT games.position, games.leaderid
			FROM {$config["tables"]["t2_games"]} AS games
			WHERE (games.tournamentid = $tournamentid) AND (games.gameid = $gameid2)
			");
		$pos[2] = $team2["position"];
		$leaderid2 = $team2["leaderid"];
		$leaderid[2] = $leaderid2;


		// Write Score for current game
		$query = $db->query("UPDATE {$config["tables"]["t2_games"]} 
					    SET score = '". $score1 ."',
						comment = '". $func->text2db($comment) ."'
						WHERE gameid = $gameid1
						");
		$query = $db->query("UPDATE {$config["tables"]["t2_games"]} 
					    SET score = '". $score2 ."'
						WHERE gameid = $gameid2
						");
		$func->log_event(t('Das Ergebnis (%1 : %2) des Spieles #%3 vs. #%4 wurde eingetragen.', $score1, $score2, $gameid1, $gameid2), 1, t('Turnier Ergebnise'), $gameid1);

		# Zusätzlich eine Mail an beide Teamleiter senden?


		// Groups + KO
		if ($tournament["mode"] == "groups") {
			$game = $db->query("SELECT gameid
					FROM {$config["tables"]["t2_games"]}
					WHERE tournamentid = ". (int)$tournamentid ." and group_nr > 0
					GROUP BY group_nr
					");
			$num_groups = $db->num_rows($game);
			$db->free_result($game);

			for ($akt_group = 1; $akt_group <= $num_groups; $akt_group++){

				// Wenn letztes Ergebnis in einer Gruppe: Erste 2 Teams in den KO-Baum schreiben
				$unfinished_games = $db->query_first("SELECT games1.gameid
					FROM {$config["tables"]["t2_games"]} AS games1
					LEFT JOIN {$config["tables"]["t2_games"]} AS games2 ON (games1.round = games2.round) AND (games1.group_nr = games2.group_nr) AND (games1.tournamentid = games2.tournamentid)
					WHERE  (games1.tournamentid = $tournamentid)
					AND ((games1.position + 1) = games2.position)
					AND ((games1.position / 2) = FLOOR(games1.position / 2))
					AND (games1.score = 0) AND (games2.score = 0)
					AND (games1.leaderid != 0) AND (games2.leaderid != 0)
					AND (games1.group_nr = $akt_group)
					");

				if ($unfinished_games['gameid'] == ""){
					$ranking_data = $this->get_ranking($tournamentid, $akt_group);

					// IF not already written
					$game_written = $db->query_first("SELECT leaderid
						FROM {$config["tables"]["t2_games"]}
						WHERE (tournamentid = $tournamentid) AND (round = 0) AND (position = ". (($akt_group - 1) * 2) .") AND (group_nr = 0)
						");

					if ($game_written['leaderid'] == ""){

						// Write Winner
						$leader = $db->query_first("SELECT leaderid
							FROM {$config["tables"]["t2_teams"]}
							WHERE teamid = {$ranking_data->tid[0]}
							");

						$query = $db->query("INSERT INTO {$config["tables"]["t2_games"]}
							SET tournamentid = $tournamentid,
							leaderid = {$leader['leaderid']},
							round = 0,
							position = ". (($akt_group - 1) * 2) .",
							group_nr = 0,
							score = 0
							");

						// Write Semi-Winner
						$leader = $db->query_first("SELECT leaderid
							FROM {$config["tables"]["t2_teams"]}
							WHERE teamid = {$ranking_data->tid[1]}
							");

						$query = $db->query("INSERT INTO {$config["tables"]["t2_games"]}
							SET tournamentid = $tournamentid,
							leaderid = {$leader['leaderid']},
							round = 0,
							position = ". (($num_groups - ($akt_group - 1)) * 2 - 1) .",
							group_nr = 0,
							score = 0
							");
					}
				}
			}
		}

		// League
		if ($tournament["mode"] == "liga") {
			// Wenn letztes Ergebnis: Turnierstatus auf "closed" setzen
			$unfinished_games = $db->query_first("SELECT games1.gameid
					FROM {$config["tables"]["t2_games"]} AS games1
					LEFT JOIN {$config["tables"]["t2_games"]} AS games2 ON (games1.round = games2.round) AND (games1.group_nr = games2.group_nr) AND (games1.tournamentid = games2.tournamentid)
					WHERE  (games1.tournamentid = '$tournamentid')
					AND ((games1.position + 1) = games2.position)
					AND ((games1.position / 2) = FLOOR(games1.position / 2))
					AND (games1.score = 0) AND (games2.score = 0)
					AND (games1.leaderid != 0) AND (games2.leaderid != 0)
					");
			if ($unfinished_games['gameid'] == ""){
				$db->query("UPDATE {$config["tables"]["tournament_tournaments"]} SET status='closed' WHERE tournamentid = '$tournamentid'");
				$func->log_event(t('Das letzte Ergebnis im Turnier %1 wurde gemeldet. Das Turnier ist damit geschlossen worden.', $tournament["name"]), 1, t('Turnier Verwaltung'));
			}
		}


		// KO-Systems
		if (($tournament["mode"] == "single") or ($tournament["mode"] == "double")
			or (($tournament["mode"] == "groups") and ($gr_game["group_nr"] == 0))) {
			$num_rounds = 1;
			for ($z = $team_anz/2; $z > 1; $z/=2) $num_rounds++;

			// Wenn Final-Ergebnis: Turnierstatus auf "closed" setzen
			if (($round == $num_rounds)
			or ((($tournament["mode"] == "single") or ($tournament["mode"] == "groups")) and ($round == $num_rounds - 1))) {
				$db->query("UPDATE {$config["tables"]["tournament_tournaments"]} SET status='closed' WHERE tournamentid = $tournamentid");
				$func->log_event(t('Das letzte Ergebnis im Turnier %1 wurde gemeldet. Das Turnier ist damit geschlossen worden.', $tournament["name"]), 1, t('Turnier Verwaltung'));
			}

			$this->GenerateNewPosition(1, 2);
			$this->GenerateNewPosition(2, 1);

		} // END: KO-Systems
	} // END: SubmitResult





	// Functions for CheckTimeExceed
	function CheckRound($max_pos) {
		global $team_anz, $akt_round, $tournament, $db, $config, $tournamentid, $lang, $mail, $func, $game, $first, $score1, $gameid1, $name1, $leaderid1, $cfg;

		$round_end = $this->GetGameEnd($tournament, $akt_round);

		if (time() > $round_end) {
			$first = 1;
			for ($akt_pos = 0; $akt_pos <= $max_pos-1; $akt_pos ++) {
				$game = $db->query_first("SELECT games.score, games.gameid, teams.name, teams.leaderid
						FROM {$config["tables"]["t2_games"]} AS games
						LEFT JOIN {$config["tables"]["t2_teams"]} AS teams ON (teams.leaderid = games.leaderid) AND (teams.tournamentid = games.tournamentid)
						WHERE (games.tournamentid = $tournamentid) AND (games.round = $akt_round) AND (games.position = $akt_pos) AND (games.group_nr = 0)
						");
				$this->WriteResult();
			}
		}
	}

	function WriteResult (){
		global $game, $first, $score1, $gameid1, $name1, $leaderid1, $tournamentid, $lang, $func, $tournament, $mail, $cfg;

		if ($first) {
			$first = 0;
			$score1 = $game['score'];
			$score1 = $score1 + 0;
			$gameid1 = $game['gameid'];
			$name1 = $game['name'];
			$leaderid1 = $game['leaderid'];
		} else {
			$first = 1;
			$score2 = $game['score'];
			$score2 = $score2 + 0;
			$gameid2 = $game['gameid'];
			$name2 = $game['name'];
			$leaderid2 = $game['leaderid'];

			// If no result has been submitted, and both gameids are set and none of the teams is a bye (leaderid = 0)
			if (($score1 == 0) and ($score2 == 0) and ($gameid1 != "") and ($gameid2 != "") and ($leaderid1) and ($leaderid2)) {

				// Choose random winner and set score to default win
				if ($cfg["t_default_win"] == 0) $cfg["t_default_win"] = 2;
				if (rand(0, 1) == 1) {
					$score1 = $cfg["t_default_win"];
					$score2 = 0;
				} else {
					$score1 = 0;
					$score2 = $cfg["t_default_win"];
				}

				$this->SubmitResult($tournamentid, $gameid1, $gameid2, $score1, $score2, t('Ergbnis wurde automatisch gelost, da die Zeit Ã¼berschritten wurde'));

				// Log action and send mail
				$func->log_event(t('Das Ergebnis des Spieles %1 gegen %2 im Turnier %3 wurde automatisch gelost, da die Zeit Ã¼berschritten wurde', $name1, $name2, $tournament['name']), 1, t('Turnier Ergebnise'));
				$mail->create_sys_mail($leaderid1,
					t('ZeitÃ¼berschreitung im Turnier %1', $tournament['name']),
					t('Das Ergebnis Ihres Spieles %1 gegen %2 im Turnier %5 wurde nicht rechtzeitig gemeldet. Um VerzÃ¶gerungen im Turnier zu vermeiden haben die Organisatoren festgelegt, dass das Ergebnis in diesem Fall gelost werden soll. Das geloste Ergebnis ist: %1 %3 - %2 %4. Falls Sie denken diese Entscheidung wurde zu Unrecht getroffen, melden Sie sich bitte schnellstmÃ¶glich bei den Organisatoren.', $name1, $name2, $score1, $score2, $tournament['name'])
					);
				$mail->create_sys_mail($leaderid2,
					t('ZeitÃ¼berschreitung im Turnier %1', $tournament['name']),
					t('Das Ergebnis Ihres Spieles %1 gegen %2 im Turnier %5 wurde nicht rechtzeitig gemeldet. Um VerzÃ¶gerungen im Turnier zu vermeiden haben die Organisatoren festgelegt, dass das Ergebnis in diesem Fall gelost werden soll. Das geloste Ergebnis ist: %1 %3 - %2 %4. Falls Sie denken diese Entscheidung wurde zu Unrecht getroffen, melden Sie sich bitte schnellstmÃ¶glich bei den Organisatoren.', $name1, $name2, $score1, $score2, $tournament['name'])
					);
			}
		}
	}


	function CheckTimeExceed($tournamentid) {
		global $team_anz, $akt_round, $tournament, $db, $config, $lang, $mail, $func, $game, $mail, $first, $score1, $gameid1, $name1, $leaderid1, $cfg;

		$tournament = $db->query_first("SELECT mode, defwin_on_time_exceed, name,
			break_duration, max_games, game_duration, UNIX_TIMESTAMP(starttime) AS starttime, tournamentid
			FROM {$config["tables"]["tournament_tournaments"]}
			WHERE tournamentid = $tournamentid
			");

		if ($tournament["defwin_on_time_exceed"] == "1"){
			$team_anz = $this->GetTeamAnz($tournamentid, $tournament['mode'], 0);	// Is 0 okay? Maybe group-number is needed

			switch ($tournament['mode']) {
				case "liga":
				case "groups":
					$games = $db->query("SELECT teams.name, teams.teamid, games.leaderid, games.gameid, games.score, games.group_nr, games.round, games.position, games.leaderid
							FROM {$config["tables"]["t2_games"]} AS games
							LEFT JOIN {$config["tables"]["t2_teams"]} AS teams ON (games.tournamentid = teams.tournamentid) AND (games.leaderid = teams.leaderid)
							WHERE (games.tournamentid = $tournamentid) AND (games.group_nr > 0)
							GROUP BY games.gameid
							ORDER BY games.group_nr, games.round, games.position
							");
					$first = 1;
					while ($game = $db->fetch_array($games)){

						$round_end = $this->GetGameEnd($tournament, $game['round']);
						if (time() > $round_end) $this->WriteResult();
					}
					$db->free_result($games);
				break;
			}

			switch ($tournament['mode']){
				case "single":
				case "double":
				case "groups":
					$akt_round = 0;
					$this->CheckRound ($team_anz['anz']);

					$akt_round = 1;
					if ($tournament['mode'] == "double") $limit_round = 2;
					else $limit_round = 4;
					for ($z = $team_anz['anz']/2; $z >= $limit_round; $z/=2) {
						$this->CheckRound ($z);
						if ($tournament['mode'] == "double") {
							$akt_round*=-1;
							$akt_round+=0.5;
							$this->CheckRound ($z);
							$akt_round-=0.5;
							$this->CheckRound ($z);
							$akt_round*=-1;
						}
						$akt_round++;
					}
					$this->CheckRound (2);
				break;
			}
		}
	} // END: CheckTimeExceede

} // END: Class
