<?php
$step = (int)$vars["step"];
$tournamentid = (int)$vars["tournamentid"];
$group = (int)$vars["group"];

if (!$tournamentid) $func->error($lang['tourney']['teammgr_err_not'], '');
else {
  
  switch($step) {
  case 1:
    include_once('modules/tournament2/search.inc.php');
  break;
  
  
  case 2:
  	$tournament = $db->query_first("SELECT name, mode FROM {$config["tables"]["tournament_tournaments"]} WHERE tournamentid = '$tournamentid'");
  
  	if ($tournament['mode'] == "single") $modus = $lang["tourney"]["se"];
  	if ($tournament['mode'] == "double") $modus = $lang["tourney"]["de"];
  	if ($tournament['mode'] == "liga") $modus = $lang["tourney"]["league"];
  	if ($tournament['mode'] == "groups") $modus = $lang["tourney"]["groups"];
  	if ($tournament['mode'] == "all") $modus = $lang["tourney"]["all"];
  
  /*	$games = $db->query("SELECT gameid FROM {$config["tables"]["t2_games"]} WHERE (tournamentid = '$tournamentid') AND (round=0)");
  	$team_anz = $db->num_rows($games);
  	$db->free_result($games);*/
  	include_once("modules/tournament2/class_tournament.php"); 
  	$tfunc = new tfunc; 
  	$team_anz = $tfunc->GetTeamAnz($tournamentid, $tournament['mode'], $vars["group"]);  
  
  	$dsp->NewContent(str_replace("%NAME%", $tournament['name'], str_replace("%MODE%", $modus, $lang["tourney"]["tree_caption"])), $lang["tourney"]["tree_subcaption"]);
  
  	if ($team_anz == 0) {
  		$func->information($lang["tourney"]["games_pairs_unknown"], "index.php?mod=tournament2&action=tree&step=1");
  		break;
  	} elseif ($tournament['mode'] == "all") {
  		$func->information($lang["tourney"]["tree_wrong_mode"], "index.php?mod=tournament2&action=games&step=2&tournamentid=$tournamentid");
  		break;
  	} else {
  		if ($tournament['mode'] == "liga") {
  			$height = $team_anz * 20 + 30;
  		} else {
  			$height = (($team_anz/2) * 50) + 60;
  		}
  
  		if (($tournament["mode"] == "groups") && ($group == "")) {
  			$teams = $db->query_first("SELECT MAX(group_nr) AS max_group_nr
  				FROM {$config["tables"]["t2_games"]}
  				WHERE (tournamentid = '$tournamentid') AND (round = 0)
  				");
  
  			$t_array = array("<option value=\"0\">{$lang["tourney"]["tree_group_select_final"]}</option>");
  			for ($i = 1; $i <= $teams["max_group_nr"]; $i++) array_push ($t_array, "<option value=\"$i\">{$lang["tourney"]["tree_group_select_group"]} $i</option>");
  
  			$dsp->SetForm("index.php?mod=tournament2&action=tree&step=2&tournamentid=$tournamentid");
  			$dsp->AddDropDownFieldRow("group", $lang["tourney"]["tree_group_select"], $t_array, "");
  			$dsp->AddFormSubmitRow("next");
  
  		} else {
  			$dsp->AddSingleRow("<iframe src=\"index.php?mod=tournament2&action=tree_frame&design=base&tournamentid=$tournamentid&group=$group\" width=\"99%\" height=\"$height\"><a href=\"index.php?mod=tournament2&action=tree_frame&design=base&tournamentid=$tournamentid&group=$group\">Tree</a></iframe>");
  			
  			if ($tournament["mode"] == "groups"){
  				if(!file_exists("ext_inc/tournament_trees/tournament_" . $tournamentid . "_" . $group . ".png")){
  					$cronjob->load_job("cron_tmod");
  					$cronjob->loaded_class->add_job($_GET["tournamentid"],$group);
  				}
  				$dsp->AddDoubleRow("", "<a href=\"ext_inc/tournament_trees/tournament_" . $tournamentid . "_" . $group . ".png\">{$lang["tourney"]["tree_download"]}</a>");
  			}else{
  				if(!file_exists("ext_inc/tournament_trees/tournament_" . $tournamentid . ".png")){
  					$cronjob->load_job("cron_tmod");
  					$cronjob->loaded_class->add_job($_GET["tournamentid"],"");
  				}
  				$dsp->AddDoubleRow("", "<a href=\"ext_inc/tournament_trees/tournament_$tournamentid.png\">{$lang["tourney"]["tree_download"]}</a>");
  			}
  		}
  
  		
  		if ($func->internal_referer) $dsp->AddBackButton($func->internal_referer, "tournament2/games");
  		else $dsp->AddBackButton("index.php?mod=tournament2&action=tree&step=1", "tournament2/games");
  		$dsp->AddContent();
  	}
  } // Switch
}
?>