<?php
function ShowSpielpaarungen() {
	global $db, $config;

// Templates laden

$handle 	= fopen ("modules/beamer/templates/t_games.htm", "rb");
$temp_main	= fread ($handle, filesize ("modules/beamer/templates/t_games.htm"));
fclose ($handle);
$temp_main = str_replace("\"","\\\"", $temp_main);

$handle 	= fopen ("modules/beamer/templates/t_games_tournament.htm", "rb");
$temp_tourn	= fread ($handle, filesize ("modules/beamer/templates/t_games_tournament.htm"));
fclose ($handle);
$temp_tourn = str_replace("\"","\\\"", $temp_tourn);

$handle 	= fopen ("modules/beamer/templates/t_games_row.htm", "rb");
$temp_row	= fread ($handle, filesize ("modules/beamer/templates/t_games_row.htm"));
fclose ($handle);
$temp_row = str_replace("\"","\\\"", $temp_row);

// Alle laufenden Turniere auslesen
$sql = "SELECT tournamentid, name, game FROM {$config["tables"]["tournament_tournaments"]} WHERE status='process'";
$qid = $db->query($sql);

while($turnier = $db->fetch_array($qid)) {

// Template Array
$temp['tournament']['name'] = $turnier['name'];
$temp['tournament']['game'] = $turnier['game'];

$sql = "SELECT team.name, t.position, t.round FROM {$config["tables"]["t2_games"]} AS t LEFT JOIN {$config["tables"]["t2_teams"]} AS team ON team.tournamentid=t.tournamentid AND team.leaderid=t.leaderid WHERE t.tournamentid='{$turnier["tournamentid"]}' AND t.score='0' AND t.position%2=0 ORDER BY t.round, t.gameid";

$qid2 = $db->query($sql);
$temp_tourns = "";
while($team2 = $db->fetch_array($qid2)) {

$position = $team2['position'] + 1;

$sql = "SELECT team.name FROM {$config["tables"]["t2_games"]} AS t LEFT JOIN {$config["tables"]["t2_teams"]} AS team ON team.tournamentid=t.tournamentid AND team.leaderid=t.leaderid  WHERE t.tournamentid='{$turnier["tournamentid"]}' AND t.score='0' AND t.position='$position' AND t.round='{$team2["round"]}'";
$qid3 = $db->query($sql);
$team1 = $db->fetch_array($qid3);

// Template Array
$temp['tournament']['team1'] = $team1['name'];
$temp['tournament']['team2'] = $team2['name'];


eval("\$temp_rows .= \"" .$temp_row. "\";");	
}
$temp['tournament']['rows'] = $temp_rows;
eval("\$temp_tourns .= \"" .$temp_tourn. "\";");	
}
$temp['tournament']['content'] = $temp_tourns;
// Templates ausgeben
eval("\$output .= \"" .$temp_main. "\";");		
		
echo $output;
}
?>