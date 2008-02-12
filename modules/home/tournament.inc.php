<?php

// TOURNAMENT

$templ['home']['show']['item']['info']['caption'] = $lang["home"]["tourney_caption"];
$templ['home']['show']['item']['control']['row'] = "";
$templ['home']['show']['row']['text']['info']['text'] = "";

if ($auth["userid"]) {
	$teams = $db->query("SELECT games1.gameid AS gid1, games2.gameid AS gid2, teams1.name AS name1, teams2.name AS name2, tournament.name AS tuname, tournament.tournamentid AS tid
		FROM {$config["tables"]["t2_games"]} AS games1
		INNER JOIN {$config["tables"]["t2_games"]} AS games2 ON (games1.tournamentid = games2.tournamentid) AND (games1.round = games2.round) 
		LEFT JOIN {$config["tables"]["tournament_tournaments"]} AS tournament ON (tournament.tournamentid = games1.tournamentid)
		LEFT JOIN {$config["tables"]["t2_teams"]} AS teams1 ON (games1.leaderid = teams1.leaderid) AND (tournament.tournamentid = teams1.tournamentid)
		LEFT JOIN {$config["tables"]["t2_teams"]} AS teams2 ON (games2.leaderid = teams2.leaderid) AND (tournament.tournamentid = teams2.tournamentid)
		WHERE ((games1.position / 2) = FLOOR(games1.position / 2)) AND (games1.score = 0) AND (games1.leaderid != 0)
			AND ((games1.position + 1) = games2.position) AND (games2.score = 0) AND (games2.leaderid != 0)
			AND ((games1.leaderid = ". $auth["userid"] .") OR (games2.leaderid = ". $auth["userid"] ."))
			AND (!teams1.disqualified)
			AND (!teams2.disqualified)
		");

	$members = $db->query("SELECT games1.gameid AS gid1, games2.gameid AS gid2, teams1.name AS name1, teams2.name AS name2, tournament.name AS tuname, tournament.tournamentid AS tid
		FROM {$config["tables"]["t2_games"]} AS games1
		INNER JOIN {$config["tables"]["t2_games"]} AS games2 ON (games1.tournamentid = games2.tournamentid) AND (games1.round = games2.round)
		LEFT JOIN {$config["tables"]["tournament_tournaments"]} AS tournament ON (tournament.tournamentid = games1.tournamentid)
		LEFT JOIN {$config["tables"]["t2_teams"]} AS teams1 ON (games1.leaderid = teams1.leaderid) AND (tournament.tournamentid = teams1.tournamentid)
		LEFT JOIN {$config["tables"]["t2_teams"]} AS teams2 ON (games2.leaderid = teams2.leaderid) AND (tournament.tournamentid = teams2.tournamentid)
		LEFT JOIN {$config["tables"]["t2_teammembers"]} AS memb1 ON (teams1.teamid = memb1.teamid)
		LEFT JOIN {$config["tables"]["t2_teammembers"]} AS memb2 ON (teams2.teamid = memb2.teamid)
		WHERE (tournament.teamplayer > 1)
			AND ((games1.position / 2) = FLOOR(games1.position / 2)) AND (games1.score = 0) AND (games1.leaderid != 0)
			AND	((games1.position + 1) = games2.position) AND (games2.score = 0) AND (games2.leaderid != 0)
			AND (!teams1.disqualified)
			AND (!teams2.disqualified)
			AND ((memb1.userid = ". $auth["userid"] .") OR (memb2.userid = ". $auth["userid"] ."))
		");
}

if (($db->num_rows($teams) == 0) && ($db->num_rows($members) == 0)) {
	$templ['home']['show']['row']['text']['info']['text'] = "<i>{$lang["home"]["tourney_noentry"]}</i>";
	$templ['home']['show']['item']['control']['row'] .= $dsp->FetchModTpl("home", "show_row_text");
} else {
	while($team = $db->fetch_array($teams)) {
		$templ['home']['show']['row']['control']['link']	= "index.php?mod=tournament2&action=submit_result&step=1&tournamentid={$team["tid"]}&gameid1={$team["gid1"]}&gameid2={$team["gid2"]}";
		$templ['home']['show']['row']['info']['text']		= "{$team["name1"]} vs {$team["name2"]}";
		$templ['home']['show']['row']['info']['text2']		= "({$team["tuname"]})";
		$templ['home']['show']['item']['control']['row'] .= $dsp->FetchModTpl("home", "show_row");
	}
	while($member = $db->fetch_array($members)) {
		$templ['home']['show']['row']['control']['link']	= "index.php?mod=tournament2&action=submit_result&step=1&tournamentid={$member["tid"]}&gameid1={$member["gid1"]}&gameid2={$member["gid2"]}";
		$templ['home']['show']['row']['info']['text']		= "{$member["name1"]} vs {$member["name2"]}";
		$templ['home']['show']['row']['info']['text2']		= "({$member["tuname"]})";
		$templ['home']['show']['item']['control']['row'] .= $dsp->FetchModTpl("home", "show_row");
	}
}
$db->free_result($teams);
$db->free_result($members);

?>
