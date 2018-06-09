<?php

/**
 * @param mixed $bracket
 * @param int $max_pos
 * @return void
 */
function write_pairs2($bracket, $max_pos)
{
    global $auth, $templ, $func, $t, $x_start, $height, $height_menu, $box_height, $box_width, $db, $tournamentid, $akt_round, $max_round, $dg, $img_height, $map, $tfunc;

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

            $templ['index']['info']['content'] .= "CreateText('". addslashes($spieler1) ."', ". ($xpos +4) .", ". ($ypos +11) .", '$link');";
            $templ['index']['info']['content'] .= "CreateText('$score1', ". ($xpos + $box_width - 16) .", ". ($ypos +11) .", '$link');";

            $templ['index']['info']['content'] .= "CreateText('vs', ". ($xpos + ($box_width / 2) - 10) .", ". ($ypos +23) .", '$link');";

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
