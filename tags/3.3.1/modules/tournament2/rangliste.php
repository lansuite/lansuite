<?php
$step = (int)$vars["step"];
$tournamentid = (int)$vars["tournamentid"];

if (!$tournamentid) $func->error($lang['tourney']['teammgr_err_not'], '');
else {

  switch($step) {
  	case 1:
  	  include_once('modules/tournament2/search.inc.php');
  	break;
  
  
  	default:
  		$tournament = $db->query_first("SELECT name, mode, status FROM {$config["tables"]["tournament_tournaments"]} WHERE tournamentid = '$tournamentid'");
  
  		if ($tournament['mode'] == "single") $modus = $lang["tourney"]["se"];
  		if ($tournament['mode'] == "double") $modus = $lang["tourney"]["de"];
  		if ($tournament['mode'] == "liga") $modus = $lang["tourney"]["league"];
  		if ($tournament['mode'] == "groups") $modus = $lang["tourney"]["groups"];
  		if ($tournament['mode'] == "all") $modus = $lang["tourney"]["all"];
  
  		if (($tournament['status'] != "closed") && ($tournament['mode'] != "liga")) {
  			$func->information($lang["tourney"]["rang_err_still_running"], "index.php?mod=tournament2&action=rangliste&step=1");
  			break;
  		}
  
  		include_once("modules/tournament2/class_tournament.php");
  		$tfunc = new tfunc;
  		$ranking_data = $tfunc->get_ranking($tournamentid);
  
  		$dsp->NewContent(str_replace("%NAME%", $tournament['name'], str_replace("%MODE%", $modus, $lang["tourney"]["rang_caption"])), $lang["tourney"]["rang_subcaption"]);
  		if ($tournament['mode'] == "liga") {
  			$dsp->AddModTpl("tournament2", "res_liga_head");
  		}
  
  		$anz_elements = count($ranking_data->tid);
  		for ($i = 0; $i < $anz_elements; $i++) {
  			$akt_pos = $ranking_data->tid[$i];
  
  			($ranking_data->disqualified[$i])? $mark = "<font color=\"#ff0000\">" : $mark = "";
  			($ranking_data->disqualified[$i])? $mark2 = "</font>" : $mark2 = "";
  
  			if ($tournament['mode'] == "liga") {
  				$score_out = $ranking_data->score[$i] . " : " . $ranking_data->score_en[$i];
  
  				$tfunc->AddPentRow($lang["tourney"]["rang_ranking"] ." ". $ranking_data->pos[$i], $mark.$ranking_data->name[$i].$mark2 . $tfunc->button_team_details($akt_pos, $tournamentid), $ranking_data->win[$i], $ranking_data->score_dif[$i] ." ($score_out)", $ranking_data->games[$i]);
  			} else {
  				$dsp->AddDoubleRow($lang["tourney"]["rang_ranking"] ." ". $ranking_data->pos[$i], $mark.$ranking_data->name[$i].$mark2 . $tfunc->button_team_details($akt_pos, $tournamentid));
  			}
  		}
  
  		if ($func->internal_referer) $dsp->AddBackButton($func->internal_referer, "tournament2/rangliste");
  		else $dsp->AddBackButton("index.php?mod=tournament2&action=rangliste&step=1", "tournament2/rangliste");
  		$dsp->AddContent();
  	break;
  } // Switch
}
?>