<?php

// TOURNAMENT

$templ['home']['show']['item']['info']['caption'] = t('Turnier: Spielpaarungen');
$templ['home']['show']['item']['control']['row'] = "";
$templ['home']['show']['row']['text']['info']['text'] = "";

if ($auth["userid"]) {
	$teams = $db->query("SELECT games1.gameid AS gid1, games2.gameid AS gid2, teams1.name AS name1, teams2.name AS name2, t.name AS tuname, t.tournamentid AS tid
		FROM {$config["tables"]["t2_games"]} AS games1
		INNER JOIN {$config["tables"]["t2_games"]} AS games2 ON (games1.tournamentid = games2.tournamentid) AND (games1.round = games2.round) 
		LEFT JOIN {$config["tables"]["tournament_tournaments"]} AS t ON (t.tournamentid = games1.tournamentid)
		LEFT JOIN {$config["tables"]["t2_teams"]} AS teams1 ON (games1.leaderid = teams1.leaderid) AND (t.tournamentid = teams1.tournamentid)
		LEFT JOIN {$config["tables"]["t2_teams"]} AS teams2 ON (games2.leaderid = teams2.leaderid) AND (t.tournamentid = teams2.tournamentid)
		LEFT JOIN {$config["tables"]["t2_teammembers"]} AS memb1 ON (teams1.teamid = memb1.teamid)
		LEFT JOIN {$config["tables"]["t2_teammembers"]} AS memb2 ON (teams2.teamid = memb2.teamid)
		WHERE ((games1.position / 2) = FLOOR(games1.position / 2)) AND (games1.score = 0) AND (games1.leaderid != 0)
			AND ((games1.position + 1) = games2.position) AND (games2.score = 0) AND (games2.leaderid != 0)
			AND ((games1.leaderid = ". $auth["userid"] .") OR (games2.leaderid = ". $auth["userid"] .")
        OR (memb1.userid = ". $auth["userid"] .") OR (memb2.userid = ". $auth["userid"] ."))
			AND (teams1.disqualified = '0')
			AND (teams2.disqualified = '0')
		GROUP BY games1.gameid, games2.gameid
		");
}

if (($db->num_rows($teams) == 0) && ($db->num_rows($members) == 0)) $templ['home']['show']['item']['control']['row'] = "<i>". t('Es sind keine aktuellen Spielpaarungen vorhanden') ."</i>";
else {
	while($team = $db->fetch_array($teams)) {
		$templ['home']['show']['row']['control']['link']	= "index.php?mod=tournament2&action=submit_result&step=1&tournamentid={$team["tid"]}&gameid1={$team["gid1"]}&gameid2={$team["gid2"]}";
		$templ['home']['show']['row']['info']['text']		= "{$team["name1"]} vs {$team["name2"]}";
		$templ['home']['show']['row']['info']['text2']		= "({$team["tuname"]})";
		$templ['home']['show']['item']['control']['row'] .= $dsp->FetchModTpl("home", "show_row");
	}
}
$db->free_result($teams);

// Show dropdown to see all active games
if ($auth['type'] > 1) {
	$teams = $db->query("SELECT games1.gameid AS gid1, games2.gameid AS gid2, teams1.name AS name1, teams2.name AS name2, t.name AS tuname, t.tournamentid AS tid
		FROM {$config["tables"]["t2_games"]} AS games1
		INNER JOIN {$config["tables"]["t2_games"]} AS games2 ON (games1.tournamentid = games2.tournamentid) AND (games1.round = games2.round) 
		LEFT JOIN {$config["tables"]["tournament_tournaments"]} AS t ON (t.tournamentid = games1.tournamentid)
		LEFT JOIN {$config["tables"]["t2_teams"]} AS teams1 ON (games1.leaderid = teams1.leaderid) AND (t.tournamentid = teams1.tournamentid)
		LEFT JOIN {$config["tables"]["t2_teams"]} AS teams2 ON (games2.leaderid = teams2.leaderid) AND (t.tournamentid = teams2.tournamentid)
		WHERE ((games1.position / 2) = FLOOR(games1.position / 2)) AND (games1.score = 0) AND (games1.leaderid != 0)
			AND ((games1.position + 1) = games2.position) AND (games2.score = 0) AND (games2.leaderid != 0)
			AND (teams1.disqualified = '0')
			AND (teams2.disqualified = '0')
		ORDER BY t.tournamentid, teams1.name
		");
	$x = 0;
	while($team = $db->fetch_array($teams)) {
    if ($x == 0) $templ['home']['multi_select_actions'] = '"'. "tournamentid={$team["tid"]}&gameid1={$team["gid1"]}&gameid2={$team["gid2"]}" .'"';
    else $templ['home']['multi_select_actions'] .= ', "'. "tournamentid={$team["tid"]}&gameid1={$team["gid1"]}&gameid2={$team["gid2"]}" .'"';	
    
    $templ['home']['t_select_options'] .= "<option value=\"$x\">{$team["tuname"]} - {$team["name1"]} vs {$team["name2"]}</option>";
		$x++;
	}
	$templ['home']['show']['item']['control']['row'] .= $dsp->FetchModTpl("home", "admin_tournament_selection");
}

?>