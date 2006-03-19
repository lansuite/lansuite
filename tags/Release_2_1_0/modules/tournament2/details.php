<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.1
*	Filename: 			details.php
*	Module: 			Tournamentsystem
*	Main editor: 		jochen@orgapage.net
*	Last change: 		30.04.2004
*	Description: 		show tournament details
*	Remarks: 		
*
**************************************************************************/

include_once("modules/tournament2/class_tournament.php");
$tfunc = new tfunc;

$tournamentid 	= $vars["tournamentid"];
$teamid 	= $vars["teamid"];
$step 	= $vars["step"];
$headermenuitem	= $vars["headermenuitem"];

if ($headermenuitem == "") $headermenuitem = 1;

$tournament = $db->query_first_rows("SELECT * FROM {$config["tables"]["tournament_tournaments"]} WHERE tournamentid = '$tournamentid'"); 

if($tournament["number"] == 0) $func->error($lang["tourney"]["t_not_exist"], "index.php?mod=tournament2");
else {

	switch ($step){
		case 10:	// Activate Seeding
			$seeded = $db->query_first("SELECT COUNT(*) AS anz FROM {$config["tables"]["t2_teams"]} WHERE (tournamentid = '$tournamentid') AND (seeding_mark = '1') GROUP BY tournamentid");
			$team = $db->query_first("SELECT COUNT(*) AS anz FROM {$config["tables"]["t2_teams"]} WHERE (tournamentid = '$tournamentid') GROUP BY tournamentid");

			if (($seeded['anz']+1) > ($team['anz'] / 2)){
				$func->information($lang["tourney"]["seeding_error"], "index.php?mod=tournament2&action=details&tournamentid=$tournamentid&headermenuitem=2");
			} else {
				$db->query("UPDATE {$config["tables"]["t2_teams"]} SET seeding_mark = '1' WHERE (teamid = $teamid)");
				$func->confirmation($lang["tourney"]["seeding_success"], "index.php?mod=tournament2&action=details&tournamentid=$tournamentid&headermenuitem=2");
			}
		break;

		case 11:	// Deaktivate Seeding
			$db->query("UPDATE {$config["tables"]["t2_teams"]} SET seeding_mark = '0' WHERE (teamid = $teamid)");
			$func->confirmation($lang["tourney"]["seeding_unmark_success"], "index.php?mod=tournament2&action=details&tournamentid=$tournamentid&headermenuitem=2");
		break;

		default:	// Show details
			$dsp->NewContent(str_replace("%NAME%", $tournament['name'], $lang["tourney"]["details_caption"]), $lang["tourney"]["details_subcaption"]);

			$menunames[] = $lang["tourney"]["details_navi_info"];
			$menunames[] = $lang["tourney"]["details_navi_regteams"];
			$dsp->AddHeaderMenu($menunames, "index.php?mod=tournament2&action=details&tournamentid=$tournamentid", $headermenuitem);

			switch ($headermenuitem) {
				case 1:
					$dsp->AddDoubleRow($lang["tourney"]["details_name"], $func->db2text($tournament['name']));

					if (($tournament['icon']) && ($tournament['icon'] != "none")) $icon = "<img src=\"ext_inc/tournament_icons/{$tournament['icon']}\" alt=\"Icon\"> ";
					if ($tournament['version'] == "") $tournament['version'] = "<i>{$lang["tourney"]["unknown"]}</i>";
					$dsp->AddDoubleRow($lang["tourney"]["details_game"], $icon . $func->db2text($tournament['game']) ." ({$lang["tourney"]["details_version"]}: ". $func->db2text($tournament['version']) .")");

					$league = "";
					if ($tournament['wwcl_gameid'] != 0) $league .= ", <img src=\"ext_inc/tournament_icons/leagues/wwcl.png\" alt=\"WWCL\">";
					if ($tournament['ngl_gamename'] != "") $league .= ", <img src=\"ext_inc/tournament_icons/leagues/ngl.png\" alt=\"NGL\">";
					if ($tournament['mode'] == "single") $modus = $lang["tourney"]["se"];
					if ($tournament['mode'] == "double") $modus = $lang["tourney"]["de"];
					if ($tournament['mode'] == "liga") $modus = $lang["tourney"]["league"];
					if ($tournament['mode'] == "groups") $modus = $lang["tourney"]["groups"];
					if ($tournament['mode'] == "all") $modus = $lang["tourney"]["all"];
					if ($tournament['blind_draw']) $blind_draw = " (Blind Draw)";
					else $blind_draw = "";
					$dsp->AddDoubleRow($lang["tourney"]["details_mode"], $modus .", ". $tournament['teamplayer'] ." {$lang["tourney"]["details_vs"]} ". $tournament['teamplayer'] . $blind_draw . $league);

					$dsp->AddSingleRow("<b>". $lang["tourney"]["details_reg_limits"] ."</b>");
					if ($tournament['status'] == "invisible") $status = $lang["tourney"]["details_state_invisible"];
					if ($tournament['status'] == "open") $status = $lang["tourney"]["details_state_open"];
					if ($tournament['status'] == "closed") $status = "<div class=\"tbl_error\">{$lang["tourney"]["details_state_closed"]}</div>";
					if ($tournament['status'] == "process") $status = "<div class=\"tbl_error\">{$lang["tourney"]["details_state_process"]}</div>";
					$dsp->AddDoubleRow($lang["tourney"]["details_state"], $status);

					($tournament['groupid'] == 0) ?
						$dsp->AddDoubleRow($lang["tourney"]["details_group"], $lang["tourney"]["details_nogroup"])
						: $dsp->AddDoubleRow($lang["tourney"]["details_group"], str_replace("%GROUP%", $tournament['groupid'], $lang["tourney"]["details_group_out"]));

					if ($tournament['coins'] == 0) $dsp->AddDoubleRow($lang["tourney"]["details_coins"], $lang["tourney"]["details_nocoins"]);
					else {
						$team_coin = $db->query_first("SELECT SUM(t.coins) AS t_coins
							FROM {$config["tables"]["tournament_tournaments"]} AS t
							INNER JOIN {$config["tables"]["t2_teams"]} AS teams ON t.tournamentid = teams.tournamentid
							WHERE (teams.leaderid = '{$auth["userid"]}')
							AND t.party_id='$party->party_id' 
							GROUP BY teams.leaderid
							");
						$member_coin = $db->query_first("SELECT SUM(t.coins) AS t_coins
							FROM {$config["tables"]["tournament_tournaments"]} AS t
							INNER JOIN {$config["tables"]["t2_teammembers"]} AS members ON t.tournamentid = members.tournamentid
							WHERE (members.userid = '{$auth["userid"]}')
							AND t.party_id='$party->party_id' 
							GROUP BY members.userid
							");
						(($cfg['t_coins'] - $team_coin['t_coins'] - $member_coin['t_coins']) < $tournament['coins']) ?
							$coin_out = $lang["tourney"]["details_tofew_coins"]
							: $coin_out = $lang["tourney"]["details_enough_coins"];
						
						$dsp->AddDoubleRow($lang["tourney"]["details_coins"], "<div class=\"tbl_error\">". str_replace("%IS%", ($cfg['t_coins'] - $team_coin['t_coins'] - $member_coin['t_coins']), str_replace("%COST%", $tournament['coins'], $coin_out)) ."</div>");
					}

					($tournament['over18']) ?
						$dsp->AddDoubleRow($lang["tourney"]["details_u18"], $lang["tourney"]["details_u18_limit"])
						: $dsp->AddDoubleRow($lang["tourney"]["details_u18"], $lang["tourney"]["details_u18_nolimit"]);

					($tournament["defwin_on_time_exceed"] == "1")? $defwin_warning = "<div class=\"tbl_error\">{$lang["tourney"]["details_defwin_warning"]}</div> {$lang["tourney"]["details_defwin_warning2"]}" : $defwin_warning = "";
					$dsp->AddSingleRow("<b>". $lang["tourney"]["details_times"] ."</b> $defwin_warning");
					$dsp->AddDoubleRow($lang["tourney"]["details_startat"], $func->unixstamp2date($tournament["starttime"], "datetime"));

					$dsp->AddDoubleRow($lang["tourney"]["details_round_duration"], str_replace("%MAX_GAMES%", $tournament["max_games"], str_replace("%GAME_DURATION%", $tournament["game_duration"] ."min", str_replace("%BREAK_DURATION%", $tournament["break_duration"] ."min", str_replace("%SUM%", ($tournament["max_games"] * $tournament["game_duration"] + $tournament["break_duration"]) ."min", $lang["tourney"]["details_round_duration_val"])))));

					$dsp->AddSingleRow("<b>". $lang["tourney"]["details_rules_misc"] ."</b>");

					if ($tournament["rules_ext"] != "") $dsp->AddDoubleRow($lang["tourney"]["details_rules"], "<a href=\"./ext_inc/tournament_rules/{$tournament['rules_ext']}\" target=\"_blank\">{$lang["tourney"]["details_openrules"]}({$tournament['rules_ext']})</a>");

					$dsp->AddDoubleRow($lang["tourney"]["details_comment"], $func->db2text2html($tournament["comment"]));

					$dsp->AddDoubleRow($lang["tourney"]["details_mapcycle"], $func->db2text2html($tournament["mapcycle"]));
				break;

				case 2:
					$waiting_teams = "";
					$completed_teams = "";
					$teams = $db->query("SELECT name, teamid, seeding_mark, disqualified FROM {$config["tables"]["t2_teams"]} WHERE (tournamentid = $tournamentid)");
					while($team = $db->fetch_array($teams)) {
						$members = $db->query_first("SELECT COUNT(*) AS members
							FROM {$config["tables"]["t2_teammembers"]}
							WHERE (teamid = {$team['teamid']})
							GROUP BY teamid
							");
						$team_out = $team["name"] . $tfunc->button_team_details($team['teamid'], $tournamentid);
						if (($tournament['mode'] == "single") or ($tournament['mode'] == "double")){
							if ($team["seeding_mark"]) $team_out .= " ". $lang["tourney"]["details_seeding_true"];
							if (($auth["type"] > 1) && ($tournament['status'] == "open")) {
								if ($team["seeding_mark"]) $team_out .= " <a href=\"index.php?mod=tournament2&action=details&step=11&tournamentid=$tournamentid&teamid={$team['teamid']}\">{$lang["tourney"]["details_seeding_unmark"]}</a>";
								else $team_out .= " <a href=\"index.php?mod=tournament2&action=details&step=10&tournamentid=$tournamentid&teamid={$team['teamid']}\">{$lang["tourney"]["details_seeding_mark"]}</a>";
							}
						}
/*  // Disquallifiy droped, due to errors
						if ($auth["type"] > 1 and $tournament['status'] == "process") {
							if ($team['disqualified']) $team_out .= " <font color=\"#ff0000\">{$lang["tourney"]["details_disqualifyed"]}</font> ". $dsp->FetchButton("index.php?mod=tournament2&action=disqualify&teamid={$team['teamid']}&step=10", "undisqualify");
							else $team_out .= " ". $dsp->FetchButton("index.php?mod=tournament2&action=disqualify&teamid={$team['teamid']}", "disqualify");
						}
*/
						$team_out .= HTML_NEWLINE;
						if(($members["members"] + 1) < $tournament['teamplayer'])  {
							$teamcount[0]++;
							$waiting_teams .= $team_out;
						} else {
							$teamcount[1]++;
							$completed_teams .= $team_out;
						}
					}
					$db->free_result($teams);

					$dsp->AddSingleRow(str_replace("%NUM_TEAMS%",($teamcount[0] + $teamcount[1]), str_replace("%MAX_TEAMS%", $tournament['maxteams'], $lang["tourney"]["details_registered"])));

					if ($completed_teams == "") $completed_teams = "<i>{$lang["tourney"]["none"]}</i>";
					$dsp->AddDoubleRow($lang["tourney"]["details_teamnames"], $completed_teams);

					if (($tournament['teamplayer'] > 1) && ($waiting_teams != "")){
						$dsp->AddSingleRow(str_replace("%NUM_TEAMS%", ($teamcount[0] + 0), $lang["tourney"]["details_uncompleded"]));
						$dsp->AddDoubleRow($lang["tourney"]["details_teamnames"], $waiting_teams);
					}
				break;
			} // END Switch($headermenuitem)

			$buttons="";
			switch($tournament["status"]) {
				case "open":
					$buttons .= $dsp->FetchButton("index.php?mod=tournament2&action=join&tournamentid=$tournamentid&step=2", "join"). " ";
					if ($auth["type"] > 1) $buttons .= $dsp->FetchButton("index.php?mod=tournament2&action=generate_pairs&step=2&tournamentid=$tournamentid", "generate"). " ";
				break;
				case "process":
					$buttons .= $dsp->FetchButton("index.php?mod=tournament2&action=games&step=2&tournamentid=$tournamentid", "games"). " ";
					$buttons .= $dsp->FetchButton("index.php?mod=tournament2&action=tree&step=2&tournamentid=$tournamentid", "tree"). " ";
					if ($auth["type"] > 1) $buttons .= $dsp->FetchButton("index.php?mod=tournament2&action=undo_generate&tournamentid=$tournamentid", "undo_generate"). " ";
				break;
				case "closed":
					$buttons .= $dsp->FetchButton("index.php?mod=tournament2&action=games&step=2&tournamentid=$tournamentid", "games"). " ";
					$buttons .= $dsp->FetchButton("index.php?mod=tournament2&action=tree&step=2&tournamentid=$tournamentid", "tree"). " ";
					if ($auth["type"] > 1) $buttons .= $dsp->FetchButton("index.php?mod=tournament2&action=undo_close&tournamentid=$tournamentid", "undo_close"). " ";
				break;
			} // END: switch status
			$dsp->AddDoubleRow("", $buttons);
			$dsp->AddBackButton("index.php?mod=tournament2", "tournament2/details"); 

			$dsp->AddContent();
		break;
	} // END: Switch Step

} // else
?>
