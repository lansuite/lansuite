<?php

// TOURNAMENT

$smarty->assign('caption', t('Turnier: Spielpaarungen'));
$content = "";

// When a user logged in, specify the query based on the user id
$loggedInUserQueryPart = '';
if ($auth["userid"]) {
    $loggedInUserQueryPart = 'AND ((games1.leaderid = %int%) OR (games2.leaderid = %int%)
    OR (memb1.userid = %int%) OR (memb2.userid = %int%))';
}
$query = "
    SELECT
        games1.gameid AS gid1,
        games2.gameid AS gid2,
        teams1.name AS name1,
        teams2.name AS name2,
        t.name AS tuname,
        t.tournamentid AS tid
    FROM
        %prefix%t2_games AS games1
        INNER JOIN %prefix%t2_games AS games2 ON (games1.tournamentid = games2.tournamentid) AND (games1.round = games2.round) 
        LEFT JOIN %prefix%tournament_tournaments AS t ON (t.tournamentid = games1.tournamentid)
        LEFT JOIN %prefix%t2_teams AS teams1 ON (games1.leaderid = teams1.leaderid) AND (t.tournamentid = teams1.tournamentid)
        LEFT JOIN %prefix%t2_teams AS teams2 ON (games2.leaderid = teams2.leaderid) AND (t.tournamentid = teams2.tournamentid)
        LEFT JOIN %prefix%t2_teammembers AS memb1 ON (teams1.teamid = memb1.teamid)
        LEFT JOIN %prefix%t2_teammembers AS memb2 ON (teams2.teamid = memb2.teamid)
    WHERE
        ((games1.position / 2) = FLOOR(games1.position / 2))
        AND (games1.score = 0)
        AND (games1.leaderid != 0)
        AND ((games1.position + 1) = games2.position)
        AND (games2.score = 0)
        AND (games2.leaderid != 0)
        " . $loggedInUserQueryPart . "
        AND (teams1.disqualified = '0')
        AND (teams2.disqualified = '0')
        AND (t.party_id = %int%)
        AND (t.status = 'process')
    GROUP BY games1.gameid, games2.gameid
    LIMIT 0, %int%";

if ($auth["userid"]) {
    $teams = $db->qry($query, $auth["userid"], $auth["userid"], $auth["userid"], $auth["userid"], $party->party_id, $cfg['home_item_cnt_tournament2']);
} else {
    $teams = $db->qry($query, $party->party_id, $cfg['home_item_cnt_tournament2']);
}

if (!$teams instanceof mysqli_result || $db->num_rows($teams) == 0) {
    $content = "<i>". t('Es sind keine aktuellen Spielpaarungen vorhanden') ."</i>";
} else {
    while ($team = $db->fetch_array($teams)) {
        $smarty->assign('link', "index.php?mod=tournament2&action=submit_result&step=1&tournamentid={$team["tid"]}&gameid1={$team["gid1"]}&gameid2={$team["gid2"]}");
        $smarty->assign('text', "{$team["name1"]} vs {$team["name2"]}");
        $smarty->assign('text2', "({$team["tuname"]})");
        $content .= $smarty->fetch('modules/home/templates/show_row.htm');
    }
    $db->free_result($teams);
}

// Show dropdown to see all active games
if ($auth['type'] > 1) {
    $teams = $db->qry("SELECT games1.gameid AS gid1, games2.gameid AS gid2, teams1.name AS name1, teams2.name AS name2, t.name AS tuname, t.tournamentid AS tid
		FROM %prefix%t2_games AS games1
		INNER JOIN %prefix%t2_games AS games2 ON (games1.tournamentid = games2.tournamentid) AND (games1.round = games2.round) 
		LEFT JOIN %prefix%tournament_tournaments AS t ON (t.tournamentid = games1.tournamentid)
		LEFT JOIN %prefix%t2_teams AS teams1 ON (games1.leaderid = teams1.leaderid) AND (t.tournamentid = teams1.tournamentid)
		LEFT JOIN %prefix%t2_teams AS teams2 ON (games2.leaderid = teams2.leaderid) AND (t.tournamentid = teams2.tournamentid)
		WHERE ((games1.position / 2) = FLOOR(games1.position / 2)) AND (games1.score = 0) AND (games1.leaderid != 0)
			AND ((games1.position + 1) = games2.position) AND (games2.score = 0) AND (games2.leaderid != 0)
			AND (teams1.disqualified = '0')
			AND (teams2.disqualified = '0')
			AND (t.party_id = %int%) AND (t.status = 'process')
		ORDER BY t.tournamentid, teams1.name
		", $party->party_id);
    $x = 0;
    $multi_select_actions = '';
    $t_select_options = '';
    while ($team = $db->fetch_array($teams)) {
        if ($x == 0) {
            $multi_select_actions = $multi_select_actions . '"'. "tournamentid={$team["tid"]}&gameid1={$team["gid1"]}&gameid2={$team["gid2"]}" .'"';
        } else {
            $multi_select_actions = $multi_select_actions . ', "'. "tournamentid={$team["tid"]}&gameid1={$team["gid1"]}&gameid2={$team["gid2"]}" .'"';
        }
        
        $team['tuname'] = $func->CutString($team['tuname'], 12);
        $team['name1'] = $func->CutString($team['name1'], 12);
        $team['name2'] = $func->CutString($team['name2'], 12);
        $t_select_options .= "<option value=\"$x\">{$team["tuname"]} - {$team["name1"]} vs {$team["name2"]}</option>";
        $smarty->assign('t_select_options', "<option value=\"$x\">{$team["tuname"]} - {$team["name1"]} vs {$team["name2"]}</option>");
        $x++;
    }
    $smarty->assign('multi_select_actions', $multi_select_actions);
    $smarty->assign('t_select_options', $t_select_options);
    $content .= $smarty->fetch('modules/home/templates/admin_tournament_selection.htm');
}
