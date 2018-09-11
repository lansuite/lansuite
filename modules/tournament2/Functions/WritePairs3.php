<?php

/**
 * @param mixed $bracket
 * @param int $max_pos
 * @return void
 */
function write_pairs3($bracket, $max_pos)
{
    global $gd, $func, $tournament, $x_start, $height, $height_menu, $box_height, $box_width, $db, $tournamentid, $akt_round, $max_round, $color, $dg, $img_height, $map, $tfunc;

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
        $game = $db->qry_first("
              SELECT
                teams.name,
                teams.teamid,
                games.leaderid,
                games.gameid,
                games.score
              FROM %prefix%t2_games AS games
              LEFT JOIN %prefix%t2_teams AS teams ON
                (games.tournamentid = teams.tournamentid)
                AND (games.leaderid = teams.leaderid)
              WHERE
                (games.tournamentid = %int%)
                AND (games.group_nr = 0)
                AND (games.round = %string%)
                AND (games.position = %string%)
              GROUP BY games.gameid", $tournamentid, $akt_round, $akt_pos);

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
