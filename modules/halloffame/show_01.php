<?php

// ****************************************
// ROBERT HUBER
// HALL OF FAME
// V 0.1
// ****************************************

include_once("modules/tournament2/class_tournament.php");

// ----------------------------
// WERTE SORTIERT NACH TURNIER
// ----------------------------

class cfame {
	var $tname;
	var $tmode;
	var $tranking;
	var $tteams;
}
/*
class ranking_data {
	var $id = array();
	var $pos = array();
	var $tid = array();
	var $name = array();
	var $win = array();
	var $score = array();
	var $score_en = array();
	var $score_dif = array();
	var $games = array();
	var $disqualified = array();
	var $reached_finales = array();
}
*/
// --------------------------
// WERTE SORTIERT NACH USER
// --------------------------

class cuser {
	var $name;
	var $turnier = array();
	var $platz = array();
	var $gpoints;
}

// -----------------------------------------------------------------
// AUSLESEN DER DATEN AUS DEM TURNIER MODUL
// -----------------------------------------------------------------

function rangliste ($tournamentid)
{
	global $db, $config, $func, $lang;

	$turnier = new cfame;

	if (!$tournamentid) return;

  	$tournament = $db->query_first("SELECT name, mode, status, teamplayer FROM {$config["tables"]["tournament_tournaments"]} WHERE tournamentid = '$tournamentid'");
  
  	if (($tournament['status'] != "closed") && ($tournament['mode'] != "liga")) return;
  	
	$tfunc = new tfunc;
  	$ranking_data = $tfunc->get_ranking($tournamentid);
  
        //if( $rankin_data->disqualified == 1 ) return;
  
  	$turnier->tname = $tournament['name'];
  	$turnier->tmode = $tournament['mode'];
        $turnier->tteams = $tournament['teamplayer'];
  
        $turnier->tranking = $ranking_data;

	return $turnier; 

}

// ---------------------------------------------------------------
// SUCHE NACH VORHANDENEM NAMEN UND GIB DIE STELLE ZURÜCK ODER -1
// ---------------------------------------------------------------

function search_name($username, $narray)
	{
		for($i = 0; $i < count($narray); $i++)
		{
			if( !strcmp($username, $narray[$i]->name)) return $i;
			
		}
		return -1;
	}

// ----------------------------------------
// SORTIERE USER NACH PUNKTEN
// ----------------------------------------

function sort_users($users)
{
	$tausch = 0;
	$temp = new cuser;

	while(1)
	{
		for($i=0; $i < (count($users)-1); $i++)
		{
			if( $users[$i]->gpoints < $users[$i+1]->gpoints)
			{
				$temp = $users[$i];
				$users[$i] = $users[$i+1];
				$users[$i+1] = $temp;
				$tausch ++;
			}
		}
		if ( $tausch == 0 ) break;
		else $tausch = 0;
	}
	return $users;
}

// -----------------------------------------------------------------------
// -------------- MAIN PROGRAMM 
// -----------------------------------------------------------------------

$turnier = array();
$users = array();

// ---------------------------
// TURNIERRANGLISTE ABFRAGEN
// ---------------------------

for($i=0; $i<20; $i++)			// <-- max Turniere ändern
{
	$turnier[$i] = rangliste($i);
}

// ---------------------------
// TEAMNAMEN ABFRAGEN
// ---------------------------

$tname = array();

$tdq = $db->query("SELECT teams.leaderid AS id, teams.name AS name FROM {$config["tables"]["t2_teams"]} AS teams");

while ($row = $db->fetch_array($tdq)) {
    $tname[] = $row[1];
    echo $row[0]." ".$row[1]."<br>";
}


// ---------------------------
// USERNAMEN ABFRAGEN
// ---------------------------

$uname = array();

$tdq = $db->query("SELECT user.userid, user.username FROM {$config["tables"]["user"]} AS user");

while ($row = $db->fetch_array($tdq)) {
    $uname[] = $row[1];
    echo $row[0]." ".$row[1]."<br>";
}

// --------------------------------
// TURNIERWERTE NACH USER SORIEREN
// --------------------------------

for($i=0; $i < count($turnier); $i++)
{
	if(!isset($turnier[$i]->tranking) || !isset($turnier[$i])) continue;           // Überspringe leere Truniere
        if( $turnier[$i]->tteams > 1) continue;

	for($j=0; $j<count($turnier[$i]->tranking->tid); $j++)
	{

		if( search_name($turnier[$i]->tranking->name[$j], $users) == -1 )
		{
			$st = count($users);
			$users[$st] = new cuser;
			$users[$st]->name = $turnier[$i]->tranking->name[$j];
			$users[$st]->turnier[] = $turnier[$i]->tname;
			$users[$st]->pos[] = $turnier[$i]->tranking->pos[$j];
		}
		else
		{	$st = search_name($turnier[$i]->tranking->name[$j], $users);
			$users[$st]->turnier[] = $turnier[$i]->tname;
			$users[$st]->pos[] = $turnier[$i]->tranking->pos[$j];
		}
	}
}

// --------------------------------------
// PUNKTE AUSWERTEN
// --------------------------------------

for($i=0; $i < count($users); $i++)
{
	$users[$i]->gpoints = 0;
	for($j=0; $j < count($users[$i]->turnier); $j++) 
	{
		if( $users[$i]->pos[$j] <= 5 ) $users[$i]->gpoints += 12 - 2*$users[$i]->pos[$j];
	}
}

// --------------------------------------
// USER NACH PUNTEN SORTIERN
// --------------------------------------

$users = sort_users($users);

// ---------------------------------------
// AUSGABE
// ---------------------------------------

for($i=0; $i < count($users); $i++)
{
	echo $users[$i]->name." Punkte:".$users[$i]->gpoints;
	for($j=0; $j < count($users[$i]->turnier); $j++) 
	{
		echo "<br>--- Turnier:".$users[$i]->turnier[$j]." Platz:".$users[$i]->pos[$j];
	}
	echo "<br>--------------------------<br>";
}

?>
