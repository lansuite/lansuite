<?php

include_once("modules/tournament2/class_tournament.php");
$tfunc = new tfunc;

$tournamentid        = $_GET["tournamentid"];
$fullscreen        = $_SESSION['lansuite']['fullscreen'];
if ($_GET['group'] == '') {
    $_GET['group'] = 1;
}

$t = $db->qry_first('SELECT tournamentid, name, mode, UNIX_TIMESTAMP(starttime) AS starttime, break_duration, game_duration, max_games, status, mapcycle
  FROM %prefix%tournament_tournaments WHERE tournamentid = %int%', $tournamentid);

$team_anz = $tfunc->GetTeamAnz($tournamentid, $t["mode"], $_GET["group"]);
/*
// Calculate TeamCount
if (($t['mode'] == "groups") && ($_GET['group'] > 0)) {
    $games = $db->qry_first("SELECT COUNT(*) AS anz
  FROM %prefix%t2_games
  WHERE (tournamentid = %int%) AND (round = 0) AND (group_nr = %string%) AND (leaderid != 0)
  GROUP BY round
  ", $tournamentid, $_GET['group']);
    $team_anz = $games['anz'];
} elseif (($t['mode'] == "groups") && ($_GET['group'] == 0)) {
    $game = $db->qry("SELECT gameid
  FROM %prefix%t2_games
  WHERE (tournamentid = %int%) AND (group_nr > 0) AND (round = 0)
  GROUP BY group_nr
  ", $tournamentid);
    $team_anz = 2 * $db->num_rows($game);
    $db->free_result($game);
} elseif ($t["mode"] == "liga"){
    $games = $db->qry_first("SELECT COUNT(*) AS anz
  FROM %prefix%t2_games
  WHERE (tournamentid = %int%) AND (round = 0) AND (leaderid != 0)
  GROUP BY round
  ", $tournamentid);
    $team_anz = $games['anz'];
} else {
    $games = $db->qry_first("SELECT COUNT(*) AS anz
  FROM %prefix%t2_games
  WHERE (tournamentid = %int%) AND (round = 0) AND (group_nr = 0)
  GROUP BY round
  ", $tournamentid);
    $team_anz = $games['anz'];
}
*/

// Check Generated
if ($t['status'] != "process" and $t['status'] != "closed") {
    $func->information(t('Dieses Turnier wurde noch nicht generiert. Die Paarungen sind noch nicht bekannt.'), 'index.php?mod=tournament2&action=tree&step=1');
} // Check Teams
elseif ($team_anz == 0) {
    $func->information(t('Es sind keine Teams zu diesem Turnier angemeldet'), 'index.php?mod=tournament2&action=tree&step=1');
} else {
  ######## Check if roundtime has exceeded and set awaiting scores randomly
    $tfunc->CheckTimeExceed($tournamentid);

    $map = explode("\n", $t["mapcycle"]);
    if ($map[0] == "") {
        $map[0] = t('unbekannt');
    }


  ######## Get number of teams
  #$team_anz = $tfunc->GetTeamAnz($tournamentid, $t["mode"], $_GET["group"]);

    $akt_round = 1;
    for ($z = $team_anz/2; $z >= 2; $z/=2) {
        $akt_round++;
    }
    $max_round = $akt_round;

  // Text-Tree
    if ($cfg['t_text_tree']) {
        $ret = '';

        include_once("modules/tournament2/tree.class.php");
        include_once("modules/tournament2/sp_tree.class.php");
        $t2 = new lansuiteTree($tournamentid, $team_anz, $db);
        $t2->prepareWB();

        if ($t['mode'] == "double") {
            $t2->prepareLB();
        }

        $t2->mkTree();
        $ret .= '<h3>Winner-Bracket</h3>';
        $ret .= $t2->getWBString();

        if ($t['mode'] == "double") {
            $ret .= '<br><br><h3>Lower-Bracket</h3>';
            $ret .= $t2->getLBString();
        }
        $templ['index']['info']['content'] = $ret;
    } else {
        if ($t['mode'] == "liga" or ($t["mode"] == "groups" and $_GET["group"] > 0)) {
            $x_start = 5;
            $x_len = 100;
            $y_start = 5;
            $y_len = 20;
  
            $width = ($team_anz + 1) * $x_len + $x_start;
            $img_height = ($team_anz + 1) * $y_len + $y_start;
        } else {
            $box_width = 140;
            $x_start = 10;
            $width = ($box_width + 10) * $max_round;
            if ($t['mode'] == "double") {
                $width = $width * 3;
                $x_start = $width * 2/3 - 2 * ($box_width + 10) + 5;
                $width -= ($box_width + 10);
            }
            $width += 10;
  
            $height = (($team_anz/2) * 50);
            $height_menu = 40;
            $box_height = 40;
            $img_height = $height + $height_menu;
        }

          $templ['index']['info']['content'] .= '<div id="content" style="width:'. (int)$width .'px; height:'. (int)$img_height .'px"></div>
      <script src="ext_scripts/SVG2VMLv1_1.js"></script>
      <script src="ext_scripts/ls_svg2vml.js"></script>
  		<script>
  			function go() {
  				vectorModel = new VectorModel();
  				container = document.getElementById("content");
  				mySvg = vectorModel.createElement("svg");
        	mySvg.setAttribute("width", "'. (int)$width .'");
          mySvg.setAttribute("height", container.getAttribute("height"));
          mySvg.setAttribute("id", "SVGSeating");
  				container.appendChild(mySvg);
  				mySvg.setAttribute("version", "1.1");
  			  myG = vectorModel.createElement("g");
  				mySvg.appendChild(myG);
    ';
  

        function write_pairs2($bracket, $max_pos)
        {
            global $auth, $templ, $func, $t, $width, $x_start, $height, $height_menu, $box_height, $box_width, $dsp, $db, $tournamentid, $akt_round, $max_round, $color, $team_anz, $dg, $img_height, $lang, $map, $tfunc;
  
            $dg++;
            if ($akt_round > 0) {
                $xpos = $x_start + (($box_width + 10) * $akt_round);
            } else {
                $xpos = $x_start + ((2 * ($box_width + 10)) * $akt_round);
            }
  
            // Set the backgroundcolour for each round
            (floor($akt_round) % 2) ? $bg_color_svg = '#DEE2E6' : $bg_color_svg = '#EEE6E6';

            $round_start = $func->unixstamp2date($tfunc->GetGameStart($t, $akt_round), "time");
            $round_end = $func->unixstamp2date($tfunc->GetGameEnd($t, $akt_round), "time");
    
            $templ['index']['info']['content'] .= "CreateRect(". ($xpos - 5) .", 1, ". ($box_width + 10) .", $img_height, '$bg_color_svg', '#ffffff', '');";
            $templ['index']['info']['content'] .= "CreateText('". t('Runde') .": $akt_round" ."', $xpos, 16, '');";
            ($auth['type'] >= 2)? $link = 'index.php?mod=tournament2&action=breaks&tournamentid='. $tournamentid : $link = '';
            $templ['index']['info']['content'] .= "CreateText('". t('Zeit') .": ". $round_start ." - ". $round_end ."', $xpos, 26, '". $link ."');";
            $templ['index']['info']['content'] .= "CreateText('". t('Map') .": ". addslashes(trim($map[(abs(floor($akt_round)) % count($map))])) ."', $xpos, 36, '');";
    
            $spieler1 = "";
            $i = 0;
            $line_start = 0;
            for ($akt_pos = 0; $akt_pos <= $max_pos-1; $akt_pos++) {
                $game = $db->qry_first("SELECT teams.name, teams.teamid, games.leaderid, games.gameid, games.score
       FROM %prefix%t2_games AS games
       LEFT JOIN %prefix%t2_teams AS teams ON (games.tournamentid = teams.tournamentid) AND (games.leaderid = teams.leaderid)
       WHERE (games.tournamentid = %int%) AND (games.group_nr = 0)
       AND (games.round = %string%) AND (games.position = %string%)
       GROUP BY games.gameid
       ", $tournamentid, $akt_round, $akt_pos);
  
                if ($spieler1 == "") {
                    if ($game == 0) {
                        $game['name'] = t('Noch Unbekannt');
                        $known_game1 = 0;
                    } else {
                          $known_game1 = 1;
                        if ($game['leaderid'] == 0) {
                            $game['name'] = t('Freilos');
                        }
                    }
                        $spielerid1 = $game['leaderid'];
                        $spieler1 = $game['name'];
                        $gameid1 = $game['gameid'];
                        $score1 = $game['score'];
                } else {
                      $i++;
                    if ($game == 0) {
                        $game['name'] = t('Noch Unbekannt');
                        $known_game2 = 0;
                    } else {
                        $known_game2 = 1;
                        if ($game['leaderid'] == 0) {
                            $game['name'] = t('Freilos');
                        }
                    }
                        $spielerid2 = $game['leaderid'];
                        $spieler2 = $game['name'];
                        $gameid2 = $game['gameid'];
                        $score2 = $game['score'];
  
                    if (($score1 == 0) && ($score2 == 0)) {
                        $score1 = "";
                        $score2 = "";
                    }
  
                        $ypos = $akt_pos * ($height / $max_pos) + $height_menu - ($box_height / 2);
  
                        $link = '';
                    if ($gameid1 and $gameid2) {
                        $link = "index.php?mod=tournament2&action=submit_result&step=1&tournamentid=$tournamentid&gameid1=$gameid1&gameid2=$gameid2";
                    }
  
                        // Create Game-Frame
                        $templ['index']['info']['content'] .= "CreateRect($xpos, $ypos, $box_width, $box_height, '#eeeeee', '#000000', '$link');";
                        $templ['index']['info']['content'] .= "CreateLine($xpos, ". ($ypos + $box_height / 2) .", ". ($xpos - 5) .", ". ($ypos + $box_height / 2) .", '#000000');";
                        $templ['index']['info']['content'] .= "CreateLine(". ($xpos + $box_width) .", ". ($ypos + $box_height / 2) .", ". ($xpos + $box_width + 5) .", ". ($ypos + $box_height / 2) .", '#000000');";
                    if ($line_start) {
                        if ($akt_round <= 0) {
                            if ($akt_round == floor($akt_round)) {
                                $templ['index']['info']['content'] .= "CreateLine(". ($xpos - 5) .", ". ($line_start + $box_height / 2) .", ". ($xpos - 5) .", ". ($ypos + $box_height / 2) .", '#000000');";
                            }
                        }
                        if ($akt_round >= 0) {
                            $templ['index']['info']['content'] .= "CreateLine(". ($xpos + $box_width + 4) .", ". ($line_start + $box_height / 2) .", ". ($xpos + $box_width + 4) .", ". ($ypos + $box_height / 2) .", '#000000');";
                        }
                        $line_start = 0;
                    } else {
                        $line_start = $ypos;
                    }
  
                        // Write Player1
                    if (!$known_game1) {
                          $t_color = $color["unknown"];
                    } elseif ($spielerid1 == 0) {
                        $t_color = $color["freilos"];
                    } elseif ($score1 > $score2 and $known_game2) {
                        $t_color = $color["winner"];
                    } elseif ($score1 < $score2 and $known_game2) {
                        $t_color = $color["loser"];
                    } elseif ($spielerid2 == 0 and $known_game2) {
                        $t_color = $color["winner"];
                    } else {
                        $t_color = $color["text"];
                    }
  
                        $templ['index']['info']['content'] .= "CreateText('". addslashes($spieler1) ."', ". ($xpos +4) .", ". ($ypos +11) .", '$link');";
                        $templ['index']['info']['content'] .= "CreateText('$score1', ". ($xpos + $box_width - 16) .", ". ($ypos +11) .", '$link');";
  
                        $templ['index']['info']['content'] .= "CreateText('vs', ". ($xpos + ($box_width / 2) - 10) .", ". ($ypos +23) .", '$link');";
  
                        // Write Player2
                    if (!$known_game2) {
                        $t_color = $color["unknown"];
                    } elseif ($spielerid2 == 0) {
                        $t_color = $color["freilos"];
                    } elseif ($score1 < $score2 and $known_game1) {
                        $t_color = $color["winner"];
                    } elseif ($score1 > $score2 and $known_game1) {
                        $t_color = $color["loser"];
                    } elseif ($spielerid1 == 0 and $known_game1) {
                        $t_color = $color["winner"];
                    } else {
                        $t_color = $color["text"];
                    }
                        $templ['index']['info']['content'] .= "CreateText('". addslashes($spieler2) ."', ". ($xpos +4) .", ". ($ypos +36) .", '$link');";
                        $templ['index']['info']['content'] .= "CreateText('$score2', ". ($xpos + $box_width -16) .", ". ($ypos +36) .", '$link');";
  
                        // Specialtext: From WB
                    if (($akt_round < 0) && ($akt_round == floor($akt_round))) {
                        if (floor($akt_round / 2) == $akt_round / 2) {
                            $from_round = (floor($akt_pos / 2) + 1);
                        } else {
                            $from_round = (($max_pos / 2) - floor($akt_pos / 2));
                        }
                          $templ['index']['info']['content'] .= "CreateText('". t('Verlierer aus Runde %1', abs($akt_round)) ."', $xpos, ". ($ypos - 16) .", '');";
                          $templ['index']['info']['content'] .= "CreateText('". t('Partie %1', $from_round) ."', $xpos, ". ($ypos - 4) .", '');";
                    }
  
                        // Specialtext: Final
                    if ($akt_round == $max_round) {
                        $templ['index']['info']['content'] .= "CreateText('". t('Der Gewinner aus') ."', $xpos, ". ($ypos+$box_height + 10) .", '');";
                        $templ['index']['info']['content'] .= "CreateText('". t('Runde %1 Partie %2', ($akt_round * (-1) + 1), (floor($akt_pos / 2) + 1)) ."', $xpos, ". ($ypos+$box_height + 20) .", '');";
                        $templ['index']['info']['content'] .= "CreateText('". t('muss hier 2x siegen') ."', $xpos, ". ($ypos+$box_height + 30) .", '');";
                    }

                        // Specialtext: Single Elemination Final & 3rd Place Final
                    if ($akt_round == $max_round - 1 and $t['mode'] == 'single') {
                        if ($akt_pos == 1) {
                            $templ['index']['info']['content'] .= "CreateText('". t('Finale') ."', $xpos, ". ($ypos+$box_height + 10) .", '');";
                        } elseif ($akt_pos == 3) {
                            $templ['index']['info']['content'] .= "CreateText('". t('Spiel um Platz 3') ."', $xpos, ". ($ypos+$box_height + 10) .", '');";
                        }
                    }
                    
                        $spieler1 = "";
                }
            }
        }
  
          $dg = 0;
        if ($t['mode'] == "single" or $t['mode'] == "double" or ($t["mode"] == "groups" and $_GET["group"] == 0)) {
            $akt_round = 0;
            write_pairs2("Winner", $team_anz);
  
            $akt_round = 1;
            if ($t['mode'] == "double") {
                $limit_round = 2;
            } else {
                $limit_round = 4;
            }
            for ($z = $team_anz/2; $z >= $limit_round; $z/=2) {
                write_pairs2("Winner", $z);
                if ($t['mode'] == "double") {
                    $akt_round*=-1;
                    $akt_round+=0.5;
                    write_pairs2("Loser", $z);
                    $akt_round-=0.5;
                    write_pairs2("Loser", $z);
                    $akt_round*=-1;
                }
                $akt_round++;
            }
  
            if ($t['mode'] == "single") {
                write_pairs2("Winner", 4);
            } else {
                write_pairs2("Winner", 2);
            }
        }
  
        if ($t['mode'] == "liga" or ($t["mode"] == "groups"  and $_GET["group"] > 0)) {
            $leader_array = array();
            $leader_name_array = array();
            $leaders = $db->qry("SELECT teams.leaderid, teams.name
     FROM %prefix%t2_teams AS teams
     LEFT JOIN %prefix%t2_games AS games ON (teams.tournamentid = games.tournamentid) AND (teams.leaderid = games.leaderid)
     WHERE (teams.tournamentid = %int%) AND (games.group_nr = %string%)
     GROUP BY teams.leaderid
     ORDER BY teams.leaderid
     ", $tournamentid, $_GET["group"]);
            while ($leader = $db->fetch_array($leaders)) {
                  array_push($leader_array, $leader["leaderid"]);
                  array_push($leader_name_array, $leader["name"]);
            }
            $db->free_result($leaders);

            for ($y = 1; $y <= $team_anz; $y++) {
                  // Draw Frame and write captions
                  $templ['index']['info']['content'] .= "CreateText('". $leader_name_array[$y-1] ."', ". ($x_start + $x_len * $y) .", ". (7 + $y_start) .", '$link');";
                  $templ['index']['info']['content'] .= "CreateText('". $leader_name_array[$y-1] ."', $x_start, ". (7 + $y_start + $y_len * $y) .", '$link');";

                  $templ['index']['info']['content'] .= "CreateLine(". ($x_start + $x_len * $y - 7) .", 0, ". ($x_start + $x_len * $y - 7) .", ". ($img_height - 8) .", '#000000');";
                  $templ['index']['info']['content'] .= "CreateLine(0, ". ($y_start + $y_len * $y - 7) .", ". ($width - 8) .", ". ($y_start + $y_len * $y - 7) .", '#000000');";
  
                  $templ['index']['info']['content'] .= "CreateRect(". ($x_start + $x_len * $y - 6) .", ". ($y_start + $y_len * $y - 6) .", ". ($x_len - 2) .", ". ($y_len - 2) .", '#DEE2E6', '#000000', '$link');";
  
                for ($x = 0; $x < $y-1; $x++) {
                    $score = $db->qry_first("SELECT games1.score AS s1, games2.score AS s2, games1.leaderid AS leader1, games1.gameid AS gameid1, games2.gameid AS gameid2
       FROM %prefix%t2_games AS games1
       INNER JOIN %prefix%t2_games AS games2 ON (games1.tournamentid = games2.tournamentid) AND (games1.round = games2.round) AND (games1.group_nr = games2.group_nr)
       WHERE (games1.tournamentid = %int%) AND (games1.group_nr = %string%)
       AND ((games1.position + 1) = games2.position)
       AND ((games1.position / 2) = FLOOR(games1.position / 2))
       AND (((games1.leaderid = %string%) AND (games2.leaderid = %string%))
       OR ((games1.leaderid = %string%) AND (games2.leaderid = %string%)))
       ", $tournamentid, $_GET["group"], $leader_array[$x], $leader_array[$y-1], $leader_array[$y-1], $leader_array[$x]);

                    if (($score['s1'] == 0) && ($score['s2'] == 0)) {
                              $game_score = "- : -";
                    } elseif ($score['leader1'] == $leader_array[$x]) {
                                $game_score = $score['s2']. " : " .$score['s1'];
                    } else {
                                $game_score = $score['s1']. " : " .$score['s2'];
                    }
  
                      $link = '';
                    if ($score['gameid1'] and $score['gameid2']) {
                                $link = "index.php?mod=tournament2&action=submit_result&step=1&tournamentid=$tournamentid&gameid1={$score['gameid1']}&gameid2={$score['gameid2']}";
                    }

                        $templ['index']['info']['content'] .= "CreateRect(". ($x_start + $x_len * ($x+1) - 6) .", ". ($y_start + $y_len * $y - 6) .", ". ($x_len - 2) .", ". ($y_len - 2) .", '#EEE6E6', '#000000', '$link');";
                        $templ['index']['info']['content'] .= "CreateText('$game_score', ". ($x_start + $x_len * ($x + 1)) .", ". (7 + $y_start + $y_len * $y) .", '$link');";
                }
            }
        }

          $templ['index']['info']['content'] .= '
    			}
    			go();
    		</script>
    ';
    }

    if ($_SESSION["lansuite"]["fullscreen"]) {
        $templ['index']['info']['content'] .= "<script type=\"text/javascript\">\r\n<!--\r\n";
        $templ['index']['info']['content'] .= "var x=0;\r\n";
        $templ['index']['info']['content'] .= "setInterval(\"x = x + 1; window.scrollTo(x,0)\", 50);\r\n";
        $templ['index']['info']['content'] .= "//-->\r\n";
        $templ['index']['info']['content'] .= "</script>\r\n";
    }
}

$dsp->AddSingleRow($templ['index']['info']['content']);
