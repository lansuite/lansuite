<?php
include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2();

function GetTournamentName($name) {
	global $line, $auth, $lang;

	$return = '';
	// Game Icon
	if ($line['icon'] and $line['icon'] != 'none') $return .= "<img src=\"ext_inc/tournament_icons/{$line['icon']}\" title=\"Icon\" /> ";
	// Name
	$return .= $name;
	// WWCL Icon
	if ($line['wwcl_gameid']) $return .= " <img src=\"ext_inc/tournament_icons/leagues/wwcl.png\" title=\"WWCL Game\" />";
	// NGL Icon
	if ($line['ngl_gamename']) $return .= " <img src=\"ext_inc/tournament_icons/leagues/ngl.png\" title=\"NGL Game\" />";
	// Over 18 Icon
	if ($line['over18']) $return .= " <img src='design/".$auth["design"]."/images/fsk_18.gif' title=\"{$lang['ms']['cb_t_over18']}\" />";

	return $return;
}

function GetTournamentTeamAnz($maxteams) {
  global $line;
	return $line['teamanz'] .'/'. $maxteams;
}

function GetTournamentStatus($status) {
	global $lang;
	$status_descriptor["open"] 	= $lang['ms']['cb_ts_open'];
	$status_descriptor["process"] 	= $lang['ms']['cb_ts_progress'];
	$status_descriptor["closed"] 	= $lang['ms']['cb_ts_closed'];
	
	return $status_descriptor[$status];
}

$ms2->query['from'] = "{$config["tables"]["tournament_tournaments"]} AS t LEFT JOIN {$config["tables"]["t2_teams"]} AS teams ON t.tournamentid = teams.tournamentid";

$ms2->config['EntriesPerPage'] = 50;

$ms2->AddSelect('t.over18');
$ms2->AddSelect('t.icon');
$ms2->AddSelect('t.wwcl_gameid');
$ms2->AddSelect('t.ngl_gamename');
$ms2->AddSelect('COUNT(teams.tournamentid) AS teamanz');
$ms2->AddResultField($lang['tourney']['details_name'], 't.name', 'GetTournamentName');
$ms2->AddResultField($lang['tourney']['details_startat'], 't.starttime', 'MS2GetDate');
$ms2->AddResultField($lang['tourney']['team'], 't.maxteams', 'GetTournamentTeamAnz');
$ms2->AddResultField($lang['tourney']['details_state'], 't.status', 'GetTournamentStatus');

$ms2->AddIconField('details', 'index.php?mod=tournament2&action=details&tournamentid=', $lang['ms2']['details']);
$ms2->AddIconField('tree', 'index.php?mod=tournament2&action=tree&step=2&tournamentid=', $lang['ms2']['game_tree']);
$ms2->AddIconField('play', 'index.php?mod=tournament2&action=games&step=2&tournamentid=', $lang['ms2']['game_pairs']);
$ms2->AddIconField('ranking', 'index.php?mod=tournament2&action=rangliste&step=2&tournamentid=', $lang['ms2']['ranking']);
if ($auth['type'] >= 2) $ms2->AddIconField('edit', 'index.php?mod=tournament2&action=change&step=1&tournamentid=', $lang['ms2']['edit']);
if ($auth['type'] >= 3) $ms2->AddIconField('delete', 'index.php?mod=tournament2&action=delete&step=2&tournamentid=', $lang['ms2']['delete']);

if ($auth['type'] >= 3) $ms2->AddMultiSelectAction('Löschen', 'index.php?mod=tournament2&action=delete&step=10', 1);

$ms2->PrintSearch('index.php?mod=tournament2', 't.tournamentid');
?>