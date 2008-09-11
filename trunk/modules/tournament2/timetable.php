<?php

include_once("modules/tournament2/class_tournament.php");
$tfunc = new tfunc;


$dsp->NewContent(t('Turnier-Zeitplan'), t('Hier sehen Sie, welches Turnier zu welcher Zeit stattfindet.'));

// Generate Table-head
$mintime = 9999999999;
$maxtime = 0;
$tournaments = $db->qry("SELECT *, UNIX_TIMESTAMP(starttime) AS starttime FROM %prefix%tournament_tournaments WHERE party_id = %int%", $party->party_id);
while ($tournament = $db->fetch_array($tournaments)) {
	// Calc Min-Time
	if ($tournament["starttime"] < $mintime) $mintime = $tournament["starttime"];

	// Calc Max-Time
	$team_anz = $tfunc->GetTeamAnz($tournament["tournamentid"], $tournament["mode"]);
	$max_round = 1;
	for ($z = $team_anz/2; $z >= 2; $z/=2) $max_round++;
	$endtime = $tfunc->GetGameEnd($tournament, $max_round);
	if ($endtime > $maxtime) $maxtime = $endtime;
}
$db->free_result($tournaments);

if ($maxtime > $mintime + 60 * 60 * 24 * 4) $maxtime = $mintime + 60 * 60 * 24 * 4;

$templ['timetable']['head'] .= "<td><b>".t('Turnier')."</b></td>";
for ($z = $mintime; $z <= $maxtime; $z+= (60 * 60 * 2)) $templ['timetable']['head'] .= "<td colspan = 4>". $func->unixstamp2date($z, "time")."</td>";


// Generate Table-foot
$templ['timetable']['zeilen'] = "";
$tournaments = $db->qry("SELECT *, UNIX_TIMESTAMP(starttime) AS starttime FROM %prefix%tournament_tournaments WHERE party_id = %int%", $party->party_id);
while ($tournament = $db->fetch_array($tournaments)) {
#	echo "Zeit {$tournament["starttime"]}<br>";

	$team_anz = $tfunc->GetTeamAnz($tournament["tournamentid"], $tournament["mode"]);
	$max_round = 1;
	for ($z = $team_anz/2; $z >= 2; $z/=2) $max_round++;
	$endtime = $tfunc->GetGameEnd($tournament, $max_round);

	$templ['timetable']['inhalt'] = "<td nowrap>{$tournament["name"]}</td>";
	for ($z = $mintime; $z <= $maxtime; $z+= (60 * 30)) {
		if ($z > $tournament["starttime"] and $z <= $endtime) $templ['timetable']['inhalt'] .= "<td bgcolor=\"#00bb33\">&nbsp;</td>";
		else {
			if (($z/(60 * 30)) % 2 == 0) $templ['timetable']['inhalt'] .= "<td bgcolor=\"#dddddd\">&nbsp;</td>";
			else $templ['timetable']['inhalt'] .= "<td bgcolor=\"#aaaaaa\">&nbsp;</td>";
		}
	}
	$templ['timetable']['zeilen'] .= $dsp->FetchModTpl("tournament2", "timetable_zeile");
}
$db->free_result($tournaments);

$dsp->AddModTpl("tournament2", "timetable");


$dsp->AddSingleRow(t('Achtung: Der Zeitraum eines Turnieres kann sich verlängern, wenn sich weitere Teams zu diesem Turnier anmelden.')); 
$dsp->AddBackButton("index.php?mod=tournament2", "tournament2/timetable"); 
$dsp->AddContent();

?>