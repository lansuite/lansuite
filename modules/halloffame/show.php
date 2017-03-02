<?php
// ****************************************
// Robert Huber, Michael Muck
// HALL OF FAME
// V 0.3
// ****************************************


include_once("modules/tournament2/class_tournament.php");


class cTurnier {
      var $id;     // Turnier ID [int]
      var $name;   // Turniername [string]
	  var $game;   // gespieltes Game
      
      var $sp;
      var $teams;  //Teams [ranking_data]
      
	  var $status;	// status of tournament
	  
      function ausgabe()
      {
      	echo "<b><br>".$this->id." ".$this->name."</b><br>";
        //$this->teams->ausgabe();
      }      
}


class ranking_adv_data {
	var $id = array();
	var $pos = array();
	var $tid = array();
	var $name = array();
	var $uid = array();
	var $uname = array();
	var $win = array();
	var $score = array();
	var $score_en = array();
	var $score_dif = array();
	var $games = array();
	var $disqualified = array();
	var $reached_finales = array();
	
	var $members = array();
	
	function ausgabe ()
	{
		for($i=0; $i<count($this->id); $i++) 
        {
			echo "- ".$this->id[$i]." ".$this->name[$i]." ".$this->pos[$i]."<br>-- ".$this->uid[$i]." ".$this->uname[$i]."<br>";
        	if( $this->members[$i] != 0 ) $this->members[$i]->ausgabe();
        }
    }
}


class cTurnierPlayer {
	  var $id = array();
      var $name = array();
      
      function ausgabe()
      {
      	for($i=0; $i<count($this->id); $i++) 
        {
        	echo "-- ".$this->id[$i]." ".$this->name[$i]."<br>";
        } 
      }
}


function get_adv_ranking ($tournamentid, $group_nr = NULL) {
	
		global $db, $config, $akt_round, $num, $cfg, $array_id;

		$ranking_adv_data = new ranking_adv_data;

		$tournament = $db->qry_first("SELECT mode FROM %prefix%tournament_tournaments WHERE tournamentid = $tournamentid");

		$games = $db->qry("SELECT gameid FROM %prefix%t2_games WHERE (tournamentid = $tournamentid) AND (round=0)");
		$team_anz = $db->num_rows($games);
		$db->free_result($games);

		$akt_round = 0;
		$num = 1;
		$array_id = 0;


		// Je nach Modus ergibt sich ein anderes Ranking
		switch ($tournament['mode']) {
			case 'all':
				$teams = $db->qry("SELECT teams.name, teams.teamid, teams.disqualified, games.leaderid, games.score, games.gameid, user.username, user.userid
						FROM %prefix%t2_games AS games
						LEFT JOIN %prefix%t2_teams AS teams ON (teams.leaderid = games.leaderid) AND (teams.tournamentid = games.tournamentid)
						LEFT JOIN %prefix%user AS user ON (teams.leaderid = user.userid)
						WHERE games.tournamentid = '$tournamentid'
						ORDER BY teams.disqualified ASC, games.score DESC, games.position ASC
						");
				while ($team = $db->fetch_array($teams)) {
					$array_id++;
					array_push ($ranking_adv_data->id, $array_id);
					array_push ($ranking_adv_data->tid, $team['teamid']);
					array_push ($ranking_adv_data->name, $team['name']);
					array_push ($ranking_adv_data->uid, $team['userid']);
					array_push ($ranking_adv_data->uname, $team['username']);
					array_push ($ranking_adv_data->pos, $num++);
					array_push ($ranking_adv_data->disqualified, $team['disqualified']);
				}
				$db->free_result($teams);
			break;

			case "single":
			case "double":
				// Array fuer Teams auslesen
				$teams = $db->qry("SELECT teams.teamid, teams.name, teams.disqualified, MAX(games.round) AS rounds, user.username, user.userid
					FROM %prefix%t2_games AS games
					LEFT JOIN %prefix%t2_teams AS teams ON (teams.leaderid = games.leaderid) AND (teams.tournamentid = games.tournamentid)
					LEFT JOIN %prefix%user AS user ON (teams.leaderid = user.userid)
					WHERE games.tournamentid = $tournamentid AND NOT ISNULL( teams.name )
					GROUP BY teams.teamid
					ORDER BY teams.disqualified ASC, rounds DESC, games.score DESC
					");
			
				// Bei Doublemodus die ersten 2 Plaetze auslesen und Array neu auslesen
				if($tournament['mode'] == "double"){
					for ($i = 0; $i < 2;$i++){
						$team = $db->fetch_array($teams);
						if ($team['teamid']){
							$array_id++;
							array_push ($ranking_adv_data->id, $array_id);
							array_push ($ranking_adv_data->tid, $team['teamid']);
							array_push ($ranking_adv_data->name, $team['name']);
							array_push ($ranking_adv_data->uid, $team['userid']);
							array_push ($ranking_adv_data->uname, $team['username']);
							array_push ($ranking_adv_data->pos, $num++);
							array_push ($ranking_adv_data->disqualified, $team['disqualified']);
						}
					}
					$db->free_result($teams);

					// Teams auslesen und in Array schreiben
					$teams = $db->qry("SELECT teams.teamid, teams.name, teams.disqualified, MIN(games.round) AS rounds, user.username, user.userid
					FROM %prefix%t2_games AS games
					LEFT JOIN %prefix%t2_teams AS teams ON (teams.leaderid = games.leaderid) AND (teams.tournamentid = games.tournamentid)
					LEFT JOIN %prefix%user AS user ON (teams.leaderid = user.userid)
					WHERE games.tournamentid = $tournamentid
					GROUP BY teams.teamid
					ORDER BY teams.disqualified ASC, rounds ASC, games.score DESC
					");
				}

				// Array schreiben
				while ($team = $db->fetch_array($teams)) if ($team['teamid'] && !in_array($team['teamid'],$ranking_adv_data->tid)){
					$array_id++;
					array_push ($ranking_adv_data->id, $array_id);
					array_push ($ranking_adv_data->tid, $team['teamid']);
					array_push ($ranking_adv_data->name, $team['name']);
					array_push ($ranking_adv_data->uid, $team['userid']);
					array_push ($ranking_adv_data->uname, $team['username']);
					array_push ($ranking_adv_data->pos, $num++);
					array_push ($ranking_adv_data->disqualified, $team['disqualified']);
				}
				$db->free_result($teams);

				/*array_multisort ($ranking_adv_data->disqualified, SORT_ASC, SORT_NUMERIC,
							$ranking_adv_data->id, SORT_ASC, SORT_NUMERIC,
							$ranking_adv_data->tid,
							$ranking_adv_data->name,
							$ranking_adv_data->pos);*/
			break;

			case "liga":
			case "groups":
	 			if ($group_nr == '') $group_nr = 1;
	 			
				// Beteiligte Teams in Array einlesen
				$teams = $db->qry("SELECT team.teamid AS teamid, team.name AS name, team.disqualified AS disqualified, user.username, user.userid
					FROM %prefix%t2_teams AS team
					LEFT JOIN %prefix%user AS user ON (team.leaderid = user.userid)
					WHERE (tournamentid = $tournamentid)
					GROUP BY teamid
					ORDER BY teamid
					");

				$i = 0;
				while ($team = $db->fetch_array($teams)){
					$i++;
					array_push ($ranking_adv_data->pos, $i);
					array_push ($ranking_adv_data->tid, $team["teamid"]);
					array_push ($ranking_adv_data->name, $team['name']);
					array_push ($ranking_adv_data->uid, $team['userid']);
					array_push ($ranking_adv_data->uname, $team['username']);
					array_push ($ranking_adv_data->disqualified, $team['disqualified']);
					array_push ($ranking_adv_data->reached_finales, 0);
					array_push ($ranking_adv_data->win, 0);
					array_push ($ranking_adv_data->score, 0);
					array_push ($ranking_adv_data->score_en, 0);
					array_push ($ranking_adv_data->games, 0);
					array_push ($ranking_adv_data->members, 0);
				}

				$scores = $db->qry("SELECT teams1.teamid AS tid1, teams2.teamid AS tid2,  games1.score AS s1, games2.score AS s2, games1.group_nr
					FROM %prefix%t2_games AS games1
					LEFT JOIN %prefix%t2_games AS games2 ON (games1.tournamentid = games2.tournamentid) AND (games1.round = games2.round) AND (games1.group_nr = games2.group_nr)
					LEFT JOIN %prefix%t2_teams AS teams1 ON (games1.leaderid = teams1.leaderid) AND (games1.tournamentid = teams1.tournamentid)
					LEFT JOIN %prefix%t2_teams AS teams2 ON (games2.leaderid = teams2.leaderid) AND (games2.tournamentid = teams2.tournamentid)
					WHERE (games1.tournamentid = $tournamentid)
					AND ((games1.position + 1) = games2.position)
					AND ((games1.position / 2) = FLOOR(games1.position / 2))
					AND ((games1.score != 0) OR (games2.score != 0))
					AND games1.group_nr = '$group_nr'
					");

				while ($score = $db->fetch_array($scores)){
					if ($tournament['mode'] == "groups" and $group_nr == 0){
						$ranking_adv_data->reached_finales[array_search($score['tid1'], $ranking_adv_data->tid)] = 1;
						$ranking_adv_data->reached_finales[array_search($score['tid2'], $ranking_adv_data->tid)] = 1;
					}
					$ranking_adv_data->score[array_search($score['tid1'], $ranking_adv_data->tid)] += $score['s1'];
					$ranking_adv_data->score[array_search($score['tid2'], $ranking_adv_data->tid)] += $score['s2'];
					$ranking_adv_data->score_en[array_search($score['tid1'], $ranking_adv_data->tid)] += $score['s2'];
					$ranking_adv_data->score_en[array_search($score['tid2'], $ranking_adv_data->tid)] += $score['s1'];

					$ranking_adv_data->games[array_search($score['tid1'], $ranking_adv_data->tid)] ++;
					$ranking_adv_data->games[array_search($score['tid2'], $ranking_adv_data->tid)] ++;

					if ($score['s1'] == $score['s2']) {
						$ranking_adv_data->win[array_search($score['tid1'], $ranking_adv_data->tid)] += 1;
						$ranking_adv_data->win[array_search($score['tid2'], $ranking_adv_data->tid)] += 1;
					} elseif ($score['s1'] > $score['s2']) {
						$ranking_adv_data->win[array_search($score['tid1'], $ranking_adv_data->tid)] += $cfg["t_league_points"];
					} elseif ($score['s1'] < $score['s2']) {
						$ranking_adv_data->win[array_search($score['tid2'], $ranking_adv_data->tid)] += $cfg["t_league_points"];
					}
				}
				$db->free_result($teams);

				// Sortieren
				$teams_array_tmp = $ranking_adv_data->tid;
				$i = 0;
				while (array_shift($teams_array_tmp)){
					array_push ($ranking_adv_data->score_dif, ($ranking_adv_data->score[$i] - $ranking_adv_data->score_en[$i]));
					$i++;
				}
				array_multisort ($ranking_adv_data->disqualified, SORT_ASC, SORT_NUMERIC,
					$ranking_adv_data->reached_finales, SORT_DESC, SORT_NUMERIC,
					$ranking_adv_data->win, SORT_DESC, SORT_NUMERIC,
					$ranking_adv_data->score_dif, SORT_DESC, SORT_NUMERIC,
					$ranking_adv_data->score, SORT_DESC, SORT_NUMERIC,
					$ranking_adv_data->score_en, SORT_ASC, SORT_NUMERIC,
					$ranking_adv_data->tid, SORT_ASC, SORT_NUMERIC,
					$ranking_adv_data->name, SORT_ASC, SORT_STRING,
					$ranking_adv_data->uid, SORT_ASC, SORT_NUMERIC,
					$ranking_adv_data->uname, SORT_ASC, SORT_STRING,
					$ranking_adv_data->games, SORT_ASC, SORT_NUMERIC,
					$ranking_adv_data->members, SORT_ASC, SORT_NUMERIC);
			break;
		}

		return $ranking_adv_data;
	}

// -----------------------------------------------------------------------------
// MAIN PROGRAM
// -----------------------------------------------------------------------------

//echo "<br><div align='center'><span style='font-size:180%'><b>__.::<<--- Hall of Fame --->>::.__</b></span></div><br><br>";
$dsp->NewContent("__.::<<--- Hall of Fame --->>::.__", "");

//echo "Klick auf ein Turnier um die zugehörige Rangliste anzeigen zu lassen:<br><br>";
$dsp->AddSingleRow("Klicke auf ein Turnier um die vollständige Rangliste anzeigen zu lassen!<br><br>");
	
// -----------------------------------------------
// ABFRAGE DER TURNIERe WO NUR IMMER EINER SPIELT
// -----------------------------------------------

//$result = $db->qry("SELECT tournamentid AS id, name, status, teamplayer 
//					  FROM {$config["tables"]["tournament_tournaments"]} 
//					  WHERE ( status = 'closed' OR mode = 'liga' ) 
//					  ");

// ABFRAGE DER TURNIERE, nach LAN sortiert

$result_partys = $db->qry("SELECT party_id, name FROM %prefix%partys
					ORDER BY party_id DESC");

include_once("modules/tournament2/class_tournament.php");

//$handle = fopen("/home/die-lega/domains/lansuite.die-lega.org/errorlog","w");
					
while( $partys_row = $db->fetch_array($result_partys)) {
	
	$party_id = $partys_row['party_id'];
	$party_name = $partys_row['name'];
	
	//echo "<table border='0'><thead><th colspan='2'>$party_name</th></thead><tbody>";
	
	$dsp->AddSingleRow("<br><strong><b>$party_name</b></strong>");
	
	$result_turniere = $db->qry("SELECT tournamentid, party_id, name, game, icon, status FROM %prefix%tournament_tournaments
							WHERE party_id = $party_id AND status NOT LIKE 'invisible'");
							
	while( $turniere_row = $db->fetch_array($result_turniere)) {
		
		$lan_turnier = new cTurnier();
		
		$lan_turnier->id = $turniere_row['tournamentid'];
		$lan_turnier->name = $turniere_row['name'];
		$lan_turnier->game = $turniere_row['game'];
		$lan_turnier->sp = ($turniere_row['teamplayer'] == 1);
		$lan_turnier->status = $turniere_row['status'];
				
		$tfunc = new tfunc();
  		$ranking_data = $tfunc->get_ranking($lan_turnier->id);

		$tournament_link = "<a href='index.php?mod=tournament2&action=details&tournamentid=" . $lan_turnier->id . "'>" .
				$lan_turnier->name .
				"</a>";
				
		/*		
		$akt_pos = $ranking_data->tid[0];
  		$dsp->AddDoubleRow($lang["tourney"]["rang_ranking"] ." ". $ranking_data->pos[$i], $mark.$ranking_data->name[$i].$mark2 . $tfunc->button_team_details($akt_pos, $tournamentid));
		*/

		if($lan_turnier->status == "open") {
			$dsp->AddDoubleRow($tournament_link, "<b><span style='color:orange'>anmeldung offen</span></b>");
		}
		else if($lan_turnier->status == "process") {
			$dsp->AddDoubleRow($tournament_link, "<b><span style='color:green'>am laufen</span></b>");
		}
		else if($ranking_data->name[0] == "") {
			$dsp->AddDoubleRow($tournament_link, "<b><span style='color:red'>ausgefallen</span></b>");
		}
		else {
			$verzeichnis = "urkunden/" . $party_name . "/" . $lan_turnier->name;
			$verz_enc = "urkunden/" . rawurlencode($party_name) . "/" . rawurlencode($lan_turnier->name);

			//fputs($handle, $verzeichnis . "\n");

			$ag = "";
			$prefix = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			
			if(is_dir($verzeichnis) == true) {
				
				$files_in_certificate_dir = scandir($verzeichnis);
				
				$any_certificate_found = false;
				
				for($p = 1; $p < 4; $p++) {
					
					$search_pattern = "/platz\s?$p/i";
					$search_pattern_2 = "/$p/";
					
					$matched_anything = false;
					
					foreach($files_in_certificate_dir as $filename) {
						
						if(preg_match($search_pattern, $filename)) {
							
							$ag = "" . $ag . "<a href='". $verz_enc . "/" . $filename . "'><span class='rng_platz$p'>$p.Platz</span></a>&nbsp;&nbsp;&nbsp;&nbsp;";
							
							$matched_anything = true;
						}
						else if(preg_match($search_pattern_2, $filename)) {

							$ag = "" . $ag . "<a href='". $verz_enc . "/" . $filename . "'><span class='rng_platz$p'>$p.Platz</span></a>&nbsp;&nbsp;&nbsp;&nbsp;";
							
							$matched_anything = true;
						}
					}
					
					if($matched_anything == false) {
						$ag = "" . $ag . "&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					}
					else {
						$any_certificate_found = true;
					}
				}
				
				if($any_certificate_found == true) {
					$ag = $prefix . "Urkunden:&nbsp;&nbsp;&nbsp;" . $ag;
				}
				else {
					$ag = "";
				}
			}
			
			if($ag == "") {
				$ag = $prefix . "<font style='color:#C4C4C4;'>Keine Urkunden gefunden</font>";
			}

			
			$dsp->AddTripleRow(
				"<a href='index.php?mod=tournament2&action=rangliste&step=2&tournamentid=" . $lan_turnier->id . "'>" .
				$lan_turnier->name .
				"</a>"
					,
				"<div style='width:300px'>Gewinner: <b>'" . $ranking_data->name[0] . "'</b></div>"
					,
					"nope"
					,
				"<div style='width:250px;text-align:left'> " . $ag . "</div>");
		}
		
		//echo "<tr><td>&nbsp;</td><td>". $lan_turnier->name . "</td></tr>";
	}
	
	$dsp->AddHRuleRow();
		
	//echo "</tbody></table><br><br>";
}

$dsp->AddSingleRow("&nbsp;");

$dsp->AddContent();

//fclose($handle);

/*
while ($row = $db->fetch_array($result))
{
    $st = count($turniere);
    $turniere[$st] = new cTurnier;
    $turniere[$st]->id = $row['id'];
    $turniere[$st]->name = $row['name'];
    $turniere[$st]->sp = ($row['teamplayer'] == 1);

    // -------------------------------------
    // ABFRAGE DER RANGLISTE DER TURNIERE
    // -------------------------------------

    $turniere[$st]->teams = get_adv_ranking($turniere[$st]->id);
    
    // -------------------------------------
    // ABFRAGE DER TEAMMITGLIEDER
    // -------------------------------------
    
    if($turniere[$st]->sp == FALSE)
    {   
    	for($i=0; $i < count($turniere[$st]->teams->id); $i++)
    	{
    	$turnier=$turniere[$st]->id;
    	$team=$turniere[$st]->teams->tid[$i];
    	$result2 = $db->qry("SELECT user.userid AS userid, user.username AS username 
					  FROM {$config["tables"]["t2_teammembers"]} AS member
					  LEFT JOIN {$config["tables"]["user"]} AS user
					  ON member.userid = user.userid
					  WHERE tournamentid = '$turnier' AND teamid = '$team'
					  ");
		$st2=0;
		$turniere[$st]->teams->members[$i] = new cTurnierPlayer;
		while ($row2 = $db->fetch_array($result2))
			{    			
    			$turniere[$st]->teams->members[$i]->id[$st2] = $row2['userid'];
    			$turniere[$st]->teams->members[$i]->name[$st2] = $row2['username'];
				$st2++;
			}
    	}
    }
    
    $turniere[$st]->ausgabe();
}
*/

?>
