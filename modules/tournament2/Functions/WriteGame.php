<?php

/**
 * @return void
 */
function WriteGame()
{
    global $spieler1, $gameid1, $score1, $spieler1_id, $i, $tournamentid, $game, $dsp, $auth;

    if ($spieler1 == "") {
        $spieler1 = $game['name'];
        $gameid1 = $game['gameid'];
        $score1 = $game['score'];
        $spieler1_id = $game['leaderid'];
    } else {
        $i++;
        $spieler2 = $game['name'];
        $gameid2 = $game['gameid'];
        $score2 = $game['score'];
        $spieler2_id = $game['leaderid'];

        // Set Colour
        if (($spieler1_id == 0) && ($spieler2_id != 0)) {
            $spieler2 = "<font color=\"#006600\">$spieler2</font>";
        } elseif (($spieler2_id == 0) && ($spieler1_id != 0)) {
            $spieler1 = "<font color=\"#006600\">$spieler1</font>";
        } elseif (($score1 > 0) || ($score2 > 0)) {
            if ($score1 > $score2) {
                $spieler1 = "<font color=\"#006600\">$spieler1</font>";
                $spieler2 = "<font color=\"#660000\">$spieler2</font>";
            } elseif ($score1 < $score2) {
                $spieler1 = "<font color=\"#660000\">$spieler1</font>";
                $spieler2 = "<font color=\"#006600\">$spieler2</font>";
            }
        }

        // Mark own team
        if ($spieler1_id == $auth["userid"]) {
            $spieler1 = "<b>$spieler1</b>";
        }
        if ($spieler2_id == $auth["userid"]) {
            $spieler2 = "<b>$spieler2</b>";
        }

        $score_output = "";
        if (($spieler1_id != 0) && ($spieler2_id != 0)) {
            if (($score1 == 0) && ($score2 == 0)) {
                $score_output = "- : - ";
            } else {
                $score_output = "$score1 : $score2 ";
            }
            $score_output .= $dsp->FetchSpanButton(t('Details'), "index.php?mod=tournament2&action=submit_result&step=1&tournamentid=$tournamentid&gameid1=$gameid1&gameid2=$gameid2");
        }

        $dsp->AddDoubleRow(t('Paarung')." $i", "$spieler1 vs $spieler2&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$score_output");
        $spieler1 = "";
    }
}
