<?php

if (!$_GET['tournamentid']) $func->error(t('Sie haben kein Turnier ausgewählt!'), '');
else {

  switch($_GET['step']) {
  	case 1:
  	  include_once('modules/tournament2/search.inc.php');
  	break;
  
  
  	default:
  		$tournament = $db->query_first("SELECT name, mode, status FROM {$config["tables"]["tournament_tournaments"]} WHERE tournamentid = '{$_GET['tournamentid']}'");
  
  		if ($tournament['mode'] == "single") $modus = t('Single-Elimination');
  		if ($tournament['mode'] == "double") $modus = t('Double-Elimination');
  		if ($tournament['mode'] == "liga") $modus = t('Liga');
  		if ($tournament['mode'] == "groups") $modus = t('Gruppenspiele + KO');
  		if ($tournament['mode'] == "all") $modus = t('Alle in einem');
  
  		if (($tournament['status'] != "closed") && ($tournament['mode'] != "liga")) {
  			$func->information(t('Dieses Turnier wurde noch nicht beendet. Die Rangliste ist daher noch nicht bekannt.'), "index.php?mod=tournament2&action=rangliste&step=1");
  			break;
  		}
  
  		include_once("modules/tournament2/class_tournament.php");
  		$tfunc = new tfunc;
  		$ranking_data = $tfunc->get_ranking($_GET['tournamentid']);
  
  		$dsp->NewContent(t('Turnier %1 (%2) - Rangliste', $tournament['name'], $modus), t('Hier sehen Sie das Ergebnis dieses Turniers'));
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
  
  				$tfunc->AddPentRow(t('Platz') ." ". $ranking_data->pos[$i], $mark.$ranking_data->name[$i].$mark2 . $tfunc->button_team_details($akt_pos, $_GET['tournamentid']), $ranking_data->win[$i], $ranking_data->score_dif[$i] ." ($score_out)", $ranking_data->games[$i]);
  			} else {
  				$dsp->AddDoubleRow(t('Platz') ." ". $ranking_data->pos[$i], $mark.$ranking_data->name[$i].$mark2 . $tfunc->button_team_details($akt_pos, $_GET['tournamentid']));
  			}
  		}
  
  		if ($func->internal_referer) $dsp->AddBackButton($func->internal_referer, "tournament2/rangliste");
  		else $dsp->AddBackButton("index.php?mod=tournament2&action=rangliste&step=1", "tournament2/rangliste");
  		$dsp->AddContent();
  	break;
  } // Switch
}
?>
