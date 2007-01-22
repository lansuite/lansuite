<?php

include_once("modules/tournament2/tree.class.php");
include_once("modules/tournament2/sp_tree.class.php");

$tournamentid 		= $vars["tournamentid"];
$fullscreen 		= $vars["fullscreen"];
$group		= $vars["group"];
if ($group == "") $group = 1;

$tournament = $db->query_first("SELECT name, mode FROM {$config["tables"]["tournament_tournaments"]} WHERE tournamentid = '$tournamentid'");

if (($tournament['mode'] == "groups") && ($group > 0)) {
	$games = $db->query_first("SELECT COUNT(*) AS anz
		FROM {$config["tables"]["t2_games"]}
		WHERE (tournamentid = $tournamentid) AND (round = 0) AND (group_nr = $group) AND (leaderid != 0)
		GROUP BY round
		");
	$team_anz = $games['anz'];
} elseif (($tournament['mode'] == "groups") && ($group == 0)) {
	$game = $db->query("SELECT gameid
		FROM {$config["tables"]["t2_games"]}
		WHERE (tournamentid = $tournamentid) AND (group_nr > 0) AND (round = 0)
		GROUP BY group_nr
		");
	$team_anz = 2 * $db->num_rows($game);
	$db->free_result($game);
} elseif ($tournament["mode"] == "liga"){
	$games = $db->query_first("SELECT COUNT(*) AS anz
		FROM {$config["tables"]["t2_games"]}
		WHERE (tournamentid = '$tournamentid') AND (round = 0) AND (leaderid != 0)
		GROUP BY round
		");
	$team_anz = $games['anz'];
} else {
	$games = $db->query_first("SELECT COUNT(*) AS anz
		FROM {$config["tables"]["t2_games"]}
		WHERE (tournamentid = '$tournamentid') AND (round = 0) AND (group_nr = 0)
		GROUP BY round
		");
	$team_anz = $games['anz'];
}


if ($team_anz == 0) {
	$func->information($lang["tourney"]["games_pairs_unknown"], "index.php?mod=tournament2&action=tree&step=1");
} else {

	function write_pairs ($bracket, $max_pos) {
		global $map_output, $width, $x_start, $height, $height_menu, $box_height, $box_width, $config, $dsp, $db, $tournamentid, $tfunc, $akt_round, $team_anz, $tournament;

		if ($akt_round > 0) $xpos = $x_start + (($box_width + 10) * $akt_round);
		else $xpos = $x_start + ((2 * ($box_width + 10)) * $akt_round);

		for ($akt_pos = 1; $akt_pos <= $max_pos; $akt_pos+=2) {
			$game = $db->query_first("SELECT games1.leaderid AS l1, games1.gameid AS g1, games2.leaderid AS l2, games2.gameid AS g2
					FROM {$config["tables"]["t2_games"]} AS games1
					INNER JOIN {$config["tables"]["t2_games"]} AS games2 ON (games1.tournamentid = games2.tournamentid) AND (games1.round = games2.round) AND (games1.group_nr = games2.group_nr)
					WHERE (games1.tournamentid = $tournamentid) AND (games1.group_nr = 0)
					AND (games1.round = $akt_round) AND (games1.position = ($akt_pos-1))
					AND ((games1.position + 1) = games2.position)
					AND (games1.leaderid != 0) AND (games2.leaderid != 0)
					GROUP BY games1.gameid
					");

			$ypos = $akt_pos * ($height / $max_pos) + $height_menu - ($box_height / 2);
			if ($game) $map_output .= "<area shape=\"rect\" coords=\"$xpos,$ypos,". ($xpos+$box_width) .",". ($ypos+$box_height) ."\" href=\"index.php?mod=tournament2&action=submit_result&step=1&tournamentid=$tournamentid&gameid1={$game['g1']}&gameid2={$game['g2']}\" title=\"Runde: $akt_round Paarung: ". (floor($akt_pos / 2) + 1) ."\" target=\"_parent\">";
		}
	}

	$akt_round = 1;
	for ($z = $team_anz/2; $z >= 2; $z/=2) $akt_round++;
	$map_output = "";

	if (($tournament['mode'] == "single") || ($tournament['mode'] == "double")
		|| (($tournament["mode"] == "groups") && ($group == 0))) {

    if ($cfg['t_text_tree']) {
      /**
       * tournament tree HACK by sparkY
       *
       * not tested with single-elimination & grids of size 8+
       */
      $ret = '';
      $t = new lansuiteTree($tournamentid, $team_anz, $db);
      $t->prepareWB();

      if ($tournament['mode'] == "double")
      	$t->prepareLB();

      $t->mkTree();
      $ret .= '<h3>Winner-Bracket</h3>';
      $ret .= $t->getWBString();

      if ($tournament['mode'] == "double") {
      	$ret .= '<br><br><h3>Lower-Bracket</h3>';
      	$ret .= $t->getLBString();
      }

      /**
       * we're done - assign output to template and return
       */
      $templ['index']['info']['content'] = $ret;
      // ******* EOF HACK BY sparkY ************ //

    } else {
  		$box_width = 120;
  		$x_start = 10;
  		$width = ($box_width + 10) * $akt_round;
  		if ($tournament['mode'] == "double") {
  			$width = $width * 3;
  			$x_start = $width * 2/3 - 2 * ($box_width + 10) + 5;
  			$width -= ($box_width + 10);
  		}
  		$width += 10;

  		$height = (($team_anz/2) * 50);
  		$height_menu = 35;
  		$box_height = 40;
  		$img_height = $height + $height_menu;

  		$akt_round = 0;
  		write_pairs ("Winner", $team_anz);

  		$akt_round = 1;
  		if ($tournament['mode'] == "double") $limit_round = 2;
  		else $limit_round = 4;
  		for ($z = $team_anz/2; $z >= $limit_round; $z/=2) {
  			write_pairs ("Winner", $z);
  			if ($tournament['mode'] == "double") {
  				$akt_round*=-1;
  				$akt_round+=0.5;
  				write_pairs ("Loser", $z);
  				$akt_round-=0.5;
  				write_pairs ("Loser", $z);
  				$akt_round*=-1;
  			}
  			$akt_round++;
  		}

  		write_pairs ("Winner", 2);
      $templ['index']['info']['content'] = "<map name=\"tree\">$map_output</map><img src=\"index.php?mod=tournament2&action=tree_img&design=base&tournamentid=$tournamentid&group=$group\" usemap=\"#tree\" border=\"0\">";
    }
	}

	if (($tournament['mode'] == "liga")
		|| (($tournament["mode"] == "groups") && ($group > 0))){
		$x_start = 5;
		$x_len = 80;
		$y_start = 5;
		$y_len = 20;

		$width = ($team_anz + 1) * $x_len + $x_start;
		$img_height = ($team_anz + 1) * $y_len + $y_start;

		$leader_array = array();
		$leader_name_array = array();
		$leaders = $db->query("SELECT teams.leaderid, teams.name
			FROM {$config["tables"]["t2_teams"]} AS teams
			LEFT JOIN {$config["tables"]["t2_games"]} AS games ON (teams.tournamentid = games.tournamentid) AND (teams.leaderid = games.leaderid)
			WHERE (teams.tournamentid = $tournamentid) AND (games.group_nr = $group)
			GROUP BY teams.leaderid
			ORDER BY teams.leaderid
			");
		while ($leader = $db->fetch_array($leaders)){
			array_push($leader_array, $leader["leaderid"]);
			array_push($leader_name_array, $leader["name"]);
		}
		$db->free_result($leaders);

		for ($y = 1; $y <= $team_anz; $y++){

			for ($x = 0; $x < $y-1; $x++){
				$score = $db->query_first("SELECT games1.gameid AS gameid1, games2.gameid AS gameid2
					FROM {$config["tables"]["t2_games"]} AS games1
					INNER JOIN {$config["tables"]["t2_games"]} AS games2 ON (games1.tournamentid = games2.tournamentid) AND (games1.round = games2.round) AND (games1.group_nr = games2.group_nr)
					WHERE (games1.tournamentid = $tournamentid) AND (games1.group_nr = $group)
					AND ((games1.position + 1) = games2.position)
					AND ((games1.position / 2) = FLOOR(games1.position / 2))
					AND (((games1.leaderid = '". $leader_array[$x] ."') AND (games2.leaderid = '". $leader_array[$y-1] ."'))
					OR ((games1.leaderid = '". $leader_array[$y-1] ."') AND (games2.leaderid = '". $leader_array[$x] ."')))
					");
				$gameid1 = $score['gameid1'];
				$gameid2 = $score['gameid2'];

				$xpos = $x_start + $x_len*($x+1);
				$ypos = $y_start + $y_len*$y;
				$map_output .= "<area shape=\"rect\" coords=\"$xpos,$ypos,". ($xpos+$x_len-20) .",". ($ypos+$y_len-10) ."\" href=\"index.php?mod=tournament2&action=submit_result&step=1&tournamentid=$tournamentid&gameid1=$gameid1&gameid2=$gameid2\" title=\"#$gameid1 : #$gameid2\" target=\"_parent\">";
			}
		}
		$templ['index']['info']['content'] = "<map name=\"tree\">$map_output</map><img src=\"index.php?mod=tournament2&action=tree_img&design=base&tournamentid=$tournamentid&group=$group\" usemap=\"#tree\" border=\"0\">";
	}

}

/*if($tournament["mode"] == "groups"){
	$templ['index']['info']['content'] = "<map name=\"tree\">$map_output</map><img src=\"ext_inc/tournament_trees/tournament_$tournamentid" . "_" . $_GET['group'] . ".png\" usemap=\"#tree\" border=\"0\">";
}else{
	$templ['index']['info']['content'] = "<map name=\"tree\">$map_output</map><img src=\"ext_inc/tournament_trees/tournament_$tournamentid.png\" usemap=\"#tree\" border=\"0\">";
}*/

if ($_SESSION["lansuite"]["fullscreen"]) {
	$templ['index']['info']['content'] .= "<script type=\"text/javascript\">\r\n<!--\r\n";
	$templ['index']['info']['content'] .= "var x=0;\r\n";
	$templ['index']['info']['content'] .= "setInterval(\"x = x + 1; window.scrollTo(x,0)\", 50);\r\n";
	$templ['index']['info']['content'] .= "//-->\r\n";
	$templ['index']['info']['content'] .= "</script>\r\n";
}

$dsp->AddSingleRow($index);
$dsp->AddContent();

?>