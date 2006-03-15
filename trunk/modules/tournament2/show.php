<?php

  include_once('modules/mastersearch2/class_mastersearch2.php');
  $ms2 = new mastersearch2();
  
  $ms2->query['from'] = "{$config["tables"]["tournament_tournaments"]} AS t LEFT JOIN {$config["tables"]["t2_teams"]} AS teams ON t.tournamentid = teams.tournamentid";

#										
										
  $ms2->config['EntriesPerPage'] = 50;
  
	$ms2->AddSelect('t.over18');
	$ms2->AddSelect('t.icon');
	$ms2->AddSelect('t.wwcl_gameid');
	$ms2->AddSelect('t.ngl_gamename');
	$ms2->AddSelect('COUNT(teams.tournamentid) AS teamanz');
  $ms2->AddResultField($lang['tourney']['details_name'], 't.name', '', '', 'GetTournamentName', 1);
  $ms2->AddResultField($lang['tourney']['details_startat'], 't.starttime', '', '', 'GetDate');
  $ms2->AddResultField($lang['tourney']['team'], 't.maxteams', '', '', 'GetTournamentTeamAnz', 1);
  $ms2->AddResultField($lang['tourney']['details_state'], 't.status', '', '', 'GetTournamentStatus');

  $ms2->AddIconField('details', 't.tournamentid', 'index.php?mod=tournament2&action=details&tournamentid=');
  $ms2->AddIconField('tree', 't.tournamentid', 'index.php?mod=tournament2&action=tree&step=2&tournamentid=');
  $ms2->AddIconField('play', 't.tournamentid', 'index.php?mod=tournament2&action=games&step=2&tournamentid=');
  $ms2->AddIconField('ranking', 't.tournamentid', 'index.php?mod=tournament2&action=rangliste&step=2&tournamentid=');
  if ($auth['type'] >= 2) $ms2->AddIconField('edit', 't.tournamentid', 'index.php?mod=tournament2&action=change&step=1&tournamentid=');
  if ($auth['type'] >= 3) $ms2->AddIconField('delete', 't.tournamentid', 'index.php?mod=tournament2&action=delete&step=2&tournamentid=');

  $ms2->PrintSearch('index.php?mod=tournament2', 't.tournamentid');

/*
	$mastersearch = new MasterSearch( $vars, "index.php?mod=tournament2", "index.php?mod=tournament2&action=details&tournamentid=", "" );
	$mastersearch->LoadConfig("tournament", $lang["tourney"]["ms_search"], $lang["tourney"]["ms_result"]);
	$mastersearch->PrintForm();
	$mastersearch->Search();
	$mastersearch->PrintResult();
	
	$templ['index']['info']['content'] .= $mastersearch->GetReturn();
*/
?>