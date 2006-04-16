<?php

include_once("modules/tournament2/class_tournament.php");
$tfunc = new tfunc;

if (!$_GET["teamid"]) $func->error($lang["tourney"]["tdet_no_team"]);

else {
	$dsp->NewContent($lang["tourney"]["t_add_caption"], $lang["tourney"]["t_add_subcaption"]);

	// Get Data
	$team = $db->query_first("SELECT teams.name, teams.comment, teams.disqualified, teams.banner, users.username, users.userid
			FROM {$config["tables"]["t2_teams"]} AS teams
			LEFT JOIN {$config["tables"]["user"]} AS users ON (teams.leaderid = users.userid)
			WHERE (teams.teamid = {$_GET["teamid"]})
			");

	// Teamname
	$dsp->AddDoubleRow($lang["tourney"]["t_det_teamname"], $team['name']);

	// Disqualified
	if ($team['disqualified']) $dsp->AddDoubleRow("", "<font color=\"#ff0000\">{$lang["tourney"]["details_disqualifyed"]}</font>");

	// Banner
	if ($team['banner']) $dsp->AddSingleRow("<img src=\"ext_inc/team_banners/{$team['banner']}\" alt=\"{$team['banner']}\">");

	// Leader
	$dsp->AddDoubleRow($lang["tourney"]["t_det_leader"], $team['username'] . $func->button_userdetails($team['userid'], "") . " (Platz: ". $seat2->SeatNameLink($team['userid'], '', '') .")");

	// Members
	$dsp->AddDoubleRow($lang["tourney"]["t_det_member"], $tfunc->GetMemberList($_GET["teamid"]));

	// Stats
	$game_anz = 0;
	$won = 0;
	$lost = 0;

	$games = $db->query("SELECT g1.score AS s1, g2.score AS s2, g1.leaderid
			FROM {$config["tables"]["t2_games"]} AS g1
			LEFT JOIN {$config["tables"]["t2_games"]} AS g2 ON (g1.tournamentid = g2.tournamentid) AND (g1.round = g2.round) AND ((g1.position + 1) = g2.position)
			WHERE ((g1.score != 0) OR (g2.score != 0))
			AND ((g1.position / 2) = FLOOR(g1.position / 2))
			AND ((g1.leaderid = {$team['userid']}) OR (g2.leaderid = {$team['userid']}))
			");
	while ($game = $db->fetch_array($games)) {
		$game_anz++;
		if ($game['leaderid'] == $team['userid']) ($game[s1] > $game[s2])? $won++ : $lost++;
		else ($game[s1] > $game[s2])? $lost++ : $won++;
	}
	$db->free_result($games);

	$stats2 = $lang["tourney"]["t_det_stats_won"] .": $won" . HTML_NEWLINE . $lang["tourney"]["t_det_stats_lost"] .": $lost" . HTML_NEWLINE . $lang["tourney"]["t_det_stats_sum"] .": $game_anz";
	if ($game_anz > 0) $stats2 .= HTML_NEWLINE . $lang["tourney"]["t_det_stats_quota"] .": ". ($won / $game_anz * 100) ."%";
	$dsp->AddDoubleRow($lang["tourney"]["t_det_stats"], $stats2);

	// Comment
	$dsp->AddDoubleRow($lang["tourney"]["t_det_comment"], $func->text2html($team['comment']));

	// Output
	$dsp->AddBackButton($func->internal_referer, "tournament2/team_details"); 
	$dsp->AddContent();
}
?>
