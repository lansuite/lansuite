<?php

$gd = new gd();

$tournamentid = $_GET["tournamentid"];
if ($_GET["group"] == "") {
    $_GET["group"] = 1;
}

include_once("modules/tournament2/class_tournament.php");
$tfunc = new tfunc;

######## Check if roundtime has exceeded and set awaiting scores randomly
$tfunc->CheckTimeExceed($tournamentid);


$tournament = $db->qry_first("SELECT tournamentid, name, mode, UNIX_TIMESTAMP(starttime) AS starttime, break_duration, game_duration, max_games, status, mapcycle
  FROM %prefix%tournament_tournaments
  WHERE tournamentid = %int%
  ", $tournamentid);
$map = explode("\n", $tournament["mapcycle"]);
if ($map[0] == "") {
    $map[0] = t('unbekannt');
}


######## Get number of teams
$team_anz = $tfunc->GetTeamAnz($tournamentid, $tournament["mode"], $_GET["group"]);

#### If at least one team is present, and the tounrmanet is started
if ($team_anz != 0 and ($tournament['status'] == "process" or $tournament['status'] == "closed")) {
    $akt_round = 1;
    for ($z = $team_anz/2; $z >= 2; $z/=2) {
        $akt_round++;
    }
    $max_round = $akt_round;

    if (($tournament['mode'] == "liga")
        || (($tournament["mode"] == "groups") && ($_GET["group"] > 0))) {
        $x_start = 5;
        $x_len = 80;
        $y_start = 5;
        $y_len = 20;

        $width = ($team_anz + 1) * $x_len + $x_start;
        $img_height = ($team_anz + 1) * $y_len + $y_start;
    } else {
        $box_width = 120;
        $x_start = 10;
        $width = ($box_width + 10) * $max_round;
        if ($tournament['mode'] == "double") {
            $width = $width * 3;
            $x_start = $width * 2/3 - 2 * ($box_width + 10) + 5;
            $width -= ($box_width + 10);
        }
        $width += 10;

        $height = (($team_anz/2) * 50);
        $height_menu = 35;
        $box_height = 40;
        $img_height = $height + $height_menu;
    }

    $gd->NewImage($width, $img_height, 1);

    // Define Colors - Multiplicate by (-1) to turn off Antialiasing
    $color["background"] = imagecolorallocate($gd->img, 255, 255, 255);
    $color["text"] = imagecolorallocate($gd->img, 0, 0, 0);
    $color["error"] = imagecolorallocate($gd->img, 255, 0, 0);
    $color["spieler"] = imagecolorallocate($gd->img, 92, 12, 12);
    $color["winner"] = imagecolorallocate($gd->img, 0, 94, 0);
    $color["loser"] = imagecolorallocate($gd->img, 170, 0, 0);
    $color["freilos"] = imagecolorallocate($gd->img, 0, 0, 120);
    $color["unknown"] = imagecolorallocate($gd->img, 83, 83, 83);
    $color["frame"] = imagecolorallocate($gd->img, 0, 0, 0);
    $color["round_bg_1"] = imagecolorallocate($gd->img, 222, 216, 222);
    $color["round_bg_2"] = imagecolorallocate($gd->img, 206, 219, 255);

    // Fill background
    ImageFill($gd->img, 0, 0, $color["background"]);

    function write_pairs($bracket, $max_pos)
    {
        global $gd, $func, $tournament, $width, $x_start, $height, $height_menu, $box_height, $box_width, $dsp, $db, $tournamentid, $akt_round, $max_round, $color, $team_anz, $dg, $img_height, $lang, $map, $tfunc;

        $dg++;
        if ($akt_round > 0) {
            $xpos = $x_start + (($box_width + 10) * $akt_round);
        } else {
            $xpos = $x_start + ((2 * ($box_width + 10)) * $akt_round);
        }

        // Set the backgroundcolour for each round
        (floor($akt_round) % 2) ? $bg_color = $color["round_bg_2"] : $bg_color = $color["round_bg_1"];
        ImageFilledRectangle($gd->img, $xpos - 5, 0, $xpos + $box_width + 4, $img_height, $bg_color);
        $gd->Text($xpos, 5, $color["text"], t('Runde') .": $akt_round");

        $round_start = $func->unixstamp2date($tfunc->GetGameStart($tournament, $akt_round), "time");
        $round_end = $func->unixstamp2date($tfunc->GetGameEnd($tournament, $akt_round), "time");

        // Output time & map
        $gd->Text($xpos, 15, $color["text"], t('Zeit') .": ". $round_start ." - ". $round_end);
        $gd->Text($xpos, 25, $color["text"], t('Map') .": ". $map[(abs(floor($akt_round)) % count($map))]);

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

                // Create Game-Frame
                ImageRectangle($gd->img, $xpos, $ypos, $xpos+$box_width, $ypos+$box_height, $color["frame"]);
                ImageLine($gd->img, $xpos, $ypos + $box_height / 2, $xpos - 5, $ypos + $box_height / 2, $color["frame"]);
                ImageLine($gd->img, $xpos + $box_width, $ypos + $box_height / 2, $xpos + $box_width + 5, $ypos + $box_height / 2, $color["frame"]);
                if ($line_start) {
                    if ($akt_round <= 0) {
                        if ($akt_round == floor($akt_round)) {
                            ImageLine($gd->img, $xpos - 5, $line_start + $box_height / 2, $xpos - 5, $ypos + $box_height / 2, $color["frame"]);
                        }
                    }
                    if ($akt_round >= 0) {
                        ImageLine($gd->img, $xpos + $box_width + 4, $line_start + $box_height / 2, $xpos + $box_width + 4, $ypos + $box_height / 2, $color["frame"]);
                    }
                    $line_start = 0;
                } else {
                    $line_start = $ypos;
                }
                $gd->Text($xpos + ($box_width / 2) - 10, $ypos + 16, $color["text"], "vs");

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
                $gd->Text($xpos + 4, $ypos + 4, $t_color, $spieler1, 16);
                $gd->Text($xpos + $box_width - 16, $ypos + 4, $t_color, "$score1");

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
                $gd->Text($xpos + 4, $ypos + 28, $t_color, $spieler2, 16);
                $gd->Text($xpos + $box_width -16, $ypos + 28, $t_color, "$score2");

                // Specialtext: From WB
                if (($akt_round < 0) && ($akt_round == floor($akt_round))) {
                    if (floor($akt_round / 2) == $akt_round / 2) {
                        $from_round = (floor($akt_pos / 2) + 1);
                    } else {
                        $from_round = (($max_pos / 2) - floor($akt_pos / 2));
                    }
                    $gd->Text($xpos, $ypos - 26, $color["text"], str_replace("%ROUND%", (abs($akt_round)), str_replace("%GAME%", $from_round, t('Verlierer aus
Runde %ROUND% Partie %GAME%'))));
                }

                // Specialtext: Final
                if ($akt_round == $max_round) {
                    $gd->Text($xpos, $ypos+$box_height + 4, $color["text"], str_replace("%ROUND%", ($akt_round * (-1) + 1), str_replace("%GAME%", (floor($akt_pos / 2) + 1), t('Der Gewinner aus
Runde %ROUND% Partie %GAME%
muss hier 2x siegen'))));
                }

                $spieler1 = "";
            }
        }
    }

    $dg = 0;
    if (($tournament['mode'] == "single") || ($tournament['mode'] == "double")
        || (($tournament["mode"] == "groups") && ($_GET["group"] == 0))) {
        $akt_round = 0;
        write_pairs("Winner", $team_anz);

        $akt_round = 1;
        if ($tournament['mode'] == "double") {
            $limit_round = 2;
        } else {
            $limit_round = 4;
        }
        for ($z = $team_anz/2; $z >= $limit_round; $z/=2) {
            write_pairs("Winner", $z);
            if ($tournament['mode'] == "double") {
                $akt_round*=-1;
                $akt_round+=0.5;
                write_pairs("Loser", $z);
                $akt_round-=0.5;
                write_pairs("Loser", $z);
                $akt_round*=-1;
            }
            $akt_round++;
        }

        write_pairs("Winner", 2);
    }

    if (($tournament['mode'] == "liga")
        || (($tournament["mode"] == "groups") && ($_GET["group"] > 0))) {
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
            $gd->Text($x_start + $x_len * $y, $y_start, $color["text"], $leader_name_array[$y-1], 12);
            $gd->Text($x_start, $y_start + $y_len * $y, $color["text"], $leader_name_array[$y-1], 12);
            ImageLine(
                $gd->img,
                $x_start + $x_len * $y - 7,
                0,
                $x_start + $x_len * $y - 7,
                $img_height - 8,
                $color["frame"]
            );
            ImageLine(
                $gd->img,
                0,
                $y_start + $y_len * $y - 7,
                $width - 8,
                $y_start + $y_len * $y - 7,
                $color["frame"]
            );

            ImageFilledRectangle(
                $gd->img,
                $x_start + $x_len * $y - 6,
                $y_start + $y_len * $y - 6,
                $x_start + $x_len * $y + $x_len - 8,
                $y_start + $y_len * $y + $y_len - 8,
                $color["round_bg_1"]
            );

            for ($x = 0; $x < $y-1; $x++) {
                $score = $db->qry_first("SELECT games1.score AS s1, games2.score AS s2, games1.leaderid AS leader1
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

                ImageFilledRectangle(
                    $gd->img,
                    $x_start + $x_len * ($x+1) - 6,
                    $y_start + $y_len * $y - 6,
                    $x_start + $x_len * ($x+2) - 8,
                    $y_start + $y_len * ($y+1) - 8,
                    $color["round_bg_2"]
                );
                $gd->Text($x_start + $x_len*($x+1), $y_start + $y_len*$y, $color["text"], $game_score);
            }
        }
    }

    #### Create PNG-Image
    $gd->PutImage("", "", false);
    if ($tournament["mode"] == "groups") {
        $gd->PutImage("ext_inc/tournament_trees/tournament_$tournamentid" . "_" . $_GET['group'] . ".png");
    } else {
        $gd->PutImage("ext_inc/tournament_trees/tournament_$tournamentid.png");
    }
}
