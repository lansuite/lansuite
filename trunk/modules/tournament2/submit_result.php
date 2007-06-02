<?php

include_once("modules/tournament2/class_tournament.php");
$tfunc = new tfunc;

$qacc 		= $_GET["qacc"];
$tournamentid 	= $_GET["tournamentid"];
$gameid1 		= $_GET["gameid1"];
$gameid2 		= $_GET["gameid2"];
$score_team1 		= $vars["score_team1"];
$score_team2 		= $vars["score_team2"];
$score_comment 		= $vars["score_comment"];


########## Infos holen
$tournament = $db->query_first("SELECT name, teamplayer, over18, status, mode, mapcycle, starttime, max_games, game_duration, break_duration, tournamentid FROM {$config["tables"]["tournament_tournaments"]} WHERE tournamentid = '$tournamentid'");
$map = explode("\n", $func->db2text($tournament["mapcycle"]));
if ($map[0] == "") $map[0] = $lang["tourney"]["unknown"];

$games = $db->query_first("SELECT COUNT(*) AS anz FROM {$config["tables"]["t2_games"]} WHERE (tournamentid = '$tournamentid') AND (round=0) GROUP BY round");
$team_anz = $games["anz"];

$team1 = $db->query_first("SELECT games.group_nr, games.round, games.score, games.comment, teams.name, teams.teamid, teams.disqualified, user.userid, user.username
		FROM {$config["tables"]["t2_games"]} AS games
		LEFT JOIN {$config["tables"]["t2_teams"]} AS teams ON games.leaderid = teams.leaderid
		LEFT JOIN {$config["tables"]["user"]} AS user ON user.userid = teams.leaderid
		WHERE (teams.tournamentid = $tournamentid) AND (games.gameid = $gameid1)
		");

$team2 = $db->query_first("SELECT games.round, games.score, games.comment, teams.name, teams.teamid, teams.disqualified, user.userid, user.username
		FROM {$config["tables"]["t2_games"]} AS games
		LEFT JOIN {$config["tables"]["t2_teams"]} AS teams ON games.leaderid = teams.leaderid
		LEFT JOIN {$config["tables"]["user"]} AS user ON user.userid = teams.leaderid
		WHERE (teams.tournamentid = $tournamentid) AND (games.gameid = $gameid2)
		");


########## Einschränkungen prüfen
if ($tournament["name"] == "") { 
	$func->error($lang["tourney"]["s_res_err_no_t"], "index.php?mod=tournament2&action=details&tournamentid=$tournamentid");


########## Keine Einschränkungen gefunden
} else {
	switch ($_GET["step"]) {
		default:
			unset($_SESSION['tournament_submit_result_blocker']);

			$dsp->NewContent(str_replace("%TEAM1%", $team1['name'], str_replace("%TEAM2%", $team2['name'], $lang["tourney"]["s_res_caption"])), $lang["tourney"]["s_res_subcaption"]);
			// Write Start and Enddate for each round
			$round_start = $tfunc->GetGameStart($tournament, $team1['round'],$team1['group_nr']);
			$round_end = $tfunc->GetGameEnd($tournament, $team1['round'],$team1['group_nr']);
			$dsp->AddDoubleRow($lang["tourney"]["gametime"], $func->unixstamp2date($round_start, "datetime") ." - ". $func->unixstamp2date($round_end, "datetime"));
			$dsp->AddDoubleRow($lang["tourney"]["map"], $map[(abs(floor($team1['round'])) % count($map))]);

			$dsp->AddHRuleRow();
			$dsp->AddSingleRow("<b>{$lang["tourney"]["s_res_submit_score"]}</b>");
			$dsp->SetForm("index.php?mod=tournament2&action=submit_result&step=2&tournamentid=$tournamentid&gameid1=$gameid1&gameid2=$gameid2", '', '', 'multipart/form-data');

			// Write Team 1
			$disqualify_link = "";
/*  // Disquallifiy droped, due to errors
			if ($auth["type"] > 1 and $tournament['status'] == "process") {
				if ($team1['disqualified']) $disqualify_link = "<font color=\"#ff0000\">{$lang["tourney"]["details_disqualifyed"]}</font> ". $dsp->FetchButton("index.php?mod=tournament2&action=disqualify&teamid={$team1['teamid']}&step=10", "undisqualify");
				else $disqualify_link = $dsp->FetchButton("index.php?mod=tournament2&action=disqualify&teamid={$team1['teamid']}", "disqualify");
			}
*/
			$dsp->AddFieldSetStart(t('Team'). ' 1'. $tfunc->button_team_details($team1['teamid'], $tournamentid) . " ". $disqualify_link);
			$dsp->AddDoubleRow($lang["tourney"]["s_res_teamleader"], $team1['username'] . $func->button_userdetails($team1['userid'], "") . " ({$lang["tourney"]["position"]}: ". $seat2->SeatNameLink($team1['userid'], '', '') .")");
			$dsp->AddTextFieldRow("score_team1", $lang["tourney"]["s_res_score"], (int) $team1["score"], "");
			$dsp->AddFieldSetEnd();

			// Write Team 2
			$disqualify_link = "";
/*  // Disquallifiy droped, due to errors
			if ($auth["type"] > 1 and $tournament['status'] == "process") {
				if ($team2['disqualified']) $disqualify_link = "<font color=\"#ff0000\">{$lang["tourney"]["details_disqualifyed"]}</font> ". $dsp->FetchButton("index.php?mod=tournament2&action=disqualify&teamid={$team2['teamid']}&step=10", "undisqualify");
				else $disqualify_link = $dsp->FetchButton("index.php?mod=tournament2&action=disqualify&teamid={$team2['teamid']}", "disqualify");
			}
*/
			$dsp->AddFieldSetStart(t('Team'). ' 2'. $tfunc->button_team_details($team2['teamid'], $tournamentid) . " ". $disqualify_link);
			$dsp->AddDoubleRow($lang["tourney"]["s_res_teamleader"], $team2['username'] . $func->button_userdetails($team2['userid'], "") . " ({$lang["tourney"]["position"]}: ". $seat2->SeatNameLink($team2['userid'], '', '') .")");
			$dsp->AddTextFieldRow("score_team2", $lang["tourney"]["s_res_score"], (int) $team2["score"], "");
			$dsp->AddFieldSetEnd();

			// Write Comment
			$dsp->AddFieldSetStart(t('Anmerkungen'));
      $dsp->AddFileSelectRow('screenshot', t('Screenshot anhängen'), '', '', '', 1);
      if (file_exists('ext_inc/tournament_screenshots/'. $_GET['gameid1'] .'.png'))
        $dsp->AddDoubleRow(t('Aktuelles Bild'), '<img src="ext_inc/tournament_screenshots/'. $_GET['gameid1'] .'.png" />');

			if ($team1['comment'] != "") $score_comment = $team1['comment'];
			$dsp->AddTextAreaPlusRow("score_comment", $lang["tourney"]["s_res_comment"], $score_comment, "", "", "", 1);
			$dsp->AddFormSubmitRow("result");
			$dsp->AddFieldSetEnd();

			$buttons = "";
			$buttons .= $dsp->FetchButton("index.php?mod=tournament2&action=games&step=2&tournamentid=$tournamentid", "games");
			$buttons .= " ". $dsp->FetchButton("index.php?mod=tournament2&action=tree&step=2&tournamentid=$tournamentid", "tree");
			$dsp->AddDoubleRow("", $buttons);
		break;

		// Formular in Datenbank eintragen
		case 2:
			## Berechtigungsprüfung
			$berechtigt = 0;
			if ($auth["type"] > 1) $berechtigt = 1;
			if (($team1['userid'] == $auth["userid"]) && ($score_team1 < $score_team2)) $berechtigt = 1;
			if (($team2['userid'] == $auth["userid"]) && ($score_team1 > $score_team2)) $berechtigt = 1;
			if (!$cfg["t_only_loser_submit"]) $berechtigt = 1;

			## Wurde Ergebnis schon eingetragen?
			$not_new = 0;
			if (($tournament["mode"] == "single") || ($tournament["mode"] == "double")) {
				$score = $db->query_first("SELECT score FROM {$config["tables"]["t2_games"]} WHERE (gameid = $gameid1 OR gameid = $gameid2) AND score != 0");
				if ($score['score']) $not_new = 1;
			}

			if ($_SESSION['tournament_submit_result_blocker']) {
				$func->error("NO_REFRESH", "index.php?mod=tournament2&action=submit_result&step=1&tournamentid=$tournamentid&gameid1=$gameid1&gameid2=$gameid2");

			} elseif ($tournament["status"] != "process") { 
				$func->information($lang["tourney"]["s_res_err_finished"], "index.php?mod=tournament2&action=submit_result&step=1&tournamentid=$tournamentid&gameid1=$gameid1&gameid2=$gameid2");

			} elseif (($vars['score_team1'] == "") && ($vars['score_team2'] == "")) { 
				$func->information($lang["tourney"]["s_res_err_noscore"], "index.php?mod=tournament2&action=submit_result&step=1&tournamentid=$tournamentid&gameid1=$gameid1&gameid2=$gameid2");

			} elseif (($vars['score_team1'] == $vars['score_team2']) && (
				($tournament["mode"] == "single") || ($tournament["mode"] == "double")
				|| (($tournament["mode"] == "groups") && ($team1["group_nr"] == 0))
				)) {
				$func->information($lang["tourney"]["s_res_err_nodraw"], "index.php?mod=tournament2&action=submit_result&step=1&tournamentid=$tournamentid&gameid1=$gameid1&gameid2=$gameid2");

			} elseif (($vars['score_team1'] == $vars['score_team2']) && ($tournament["mode"] == "liga") && ($vars['score_team1'] == 0)){
				$func->information($lang["tourney"]["s_res_err_nozero"], "index.php?mod=tournament2&action=submit_result&step=1&tournamentid=$tournamentid&gameid1=$gameid1&gameid2=$gameid2");

			} elseif (!$berechtigt) { 
				$func->information($lang["tourney"]["s_res_err_noright"], "index.php?mod=tournament2&action=submit_result&step=1&tournamentid=$tournamentid&gameid1=$gameid1&gameid2=$gameid2");

			} elseif (($not_new) && ($auth["type"] <= 1)) { 
				$func->information($lang["tourney"]["s_res_err_noresubmit"], "index.php?mod=tournament2&action=submit_result&step=1&tournamentid=$tournamentid&gameid1=$gameid1&gameid2=$gameid2");

			} else {
        // Upload Screenshot
        $old_file = $func->FileUpload('screenshot', 'ext_inc/tournament_screenshots/');
        if ($old_file) {
          unlink('ext_inc/tournament_screenshots/'. $_GET['gameid1'] .'.png');
          $gd->CreateThumb($old_file, 'ext_inc/tournament_screenshots/'. $_GET['gameid1'] .'.png', 800, 600);
        }
        
				if (($not_new) && ($qacc != 1)){
					$func->question($lang["tourney"]["s_res_question_score_submitted"], "index.php?mod=tournament2&action=submit_result&step=2&gameid1=$gameid1&gameid2=$gameid2&tournamentid=$tournamentid&qacc=1&score_team1=$score_team1&score_team2=$score_team2&score_comment=$score_comment", "index.php?mod=tournament2&action=submit_result&step=1&gameid1=$gameid1&gameid2=$gameid2&tournamentid=$tournamentid");
				} else {
					$_SESSION["tournament_submit_result_blocker"] = TRUE;

					$tfunc->SubmitResult($tournamentid, $gameid1, $gameid2, $vars["score_team1"], $vars["score_team2"], $vars["score_comment"]);

					$func->confirmation($lang["tourney"]["s_res_success"], "index.php?mod=tournament2&action=submit_result&step=1&tournamentid=$tournamentid&gameid1=$gameid1&gameid2=$gameid2");
/*
					$cronjob->load_job("cron_tmod");
					if($tournament['mode'] == "groups"){
						$cronjob->loaded_class->add_job($_GET["tournamentid"],$team1["group_nr"]);
					}else{
						$cronjob->loaded_class->add_job($_GET["tournamentid"],"");
					}
*/
				}
			}
		break;
	} // Switch
}
?>
