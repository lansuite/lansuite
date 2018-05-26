<?php

$mail = new \LanSuite\Module\Mail\Mail();
$seat2 = new \LanSuite\Module\Seating\Seat2();

$tfunc = new \LanSuite\Module\Tournament2\TournamentFunction($mail, $seat2);

$tournamentid = $_GET["tournamentid"];
$fullscreen   = $_SESSION['lansuite']['fullscreen'];
if ($_GET['group'] == '') {
    $_GET['group'] = 1;
}

$t = $db->qry_first('
  SELECT
    tournamentid,
    name,
    mode,
    UNIX_TIMESTAMP(starttime) AS starttime,
    break_duration,
    game_duration,
    max_games,
    status,
    mapcycle
  FROM %prefix%tournament_tournaments
  WHERE
    tournamentid = %int%', $tournamentid);

$team_anz = $tfunc->GetTeamAnz($tournamentid, $t["mode"], $_GET["group"]);

// Check Generated
if ($t['status'] != "process" and $t['status'] != "closed") {
    $func->information(t('Dieses Turnier wurde noch nicht generiert. Die Paarungen sind noch nicht bekannt.'), 'index.php?mod=tournament2&action=tree&step=1');

// Check Teams
} elseif ($team_anz == 0) {
    $func->information(t('Es sind keine Teams zu diesem Turnier angemeldet'), 'index.php?mod=tournament2&action=tree&step=1');
} else {
    // Check if roundtime has exceeded and set awaiting scores randomly
    $tfunc->CheckTimeExceed($tournamentid);

    $map = explode("\n", $t["mapcycle"]);
    if ($map[0] == "") {
        $map[0] = t('unbekannt');
    }

    $akt_round = 1;
    for ($z = $team_anz/2; $z >= 2; $z/=2) {
        $akt_round++;
    }
    $max_round = $akt_round;

    // Text-Tree
    if ($cfg['t_text_tree']) {
        $ret = '';

        $t2 = new \LanSuite\Module\Tournament2\LanSuiteTree($tournamentid, $team_anz, $db);
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
  
        if ($t['mode'] == "liga" || ($t["mode"] == "groups"  and $_GET["group"] > 0)) {
            $leader_array = array();
            $leader_name_array = array();
            $leaders = $db->qry("
              SELECT
                teams.leaderid,
                teams.name
              FROM %prefix%t2_teams AS teams
              LEFT JOIN %prefix%t2_games AS games ON
                (teams.tournamentid = games.tournamentid)
                AND (teams.leaderid = games.leaderid)
              WHERE
                (teams.tournamentid = %int%)
                AND (games.group_nr = %string%)
              GROUP BY teams.leaderid
              ORDER BY teams.leaderid", $tournamentid, $_GET["group"]);
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
                    $score = $db->qry_first("
                      SELECT
                        games1.score AS s1,
                        games2.score AS s2,
                        games1.leaderid AS leader1,
                        games1.gameid AS gameid1,
                        games2.gameid AS gameid2
                      FROM %prefix%t2_games AS games1
                      INNER JOIN %prefix%t2_games AS games2 ON
                        (games1.tournamentid = games2.tournamentid)
                        AND (games1.round = games2.round)
                        AND (games1.group_nr = games2.group_nr)
                      WHERE
                        (games1.tournamentid = %int%)
                        AND (games1.group_nr = %string%)
                        AND ((games1.position + 1) = games2.position)
                        AND ((games1.position / 2) = FLOOR(games1.position / 2))
                        AND (
                          ((games1.leaderid = %string%)
                          AND (games2.leaderid = %string%))
                          OR (
                            (games1.leaderid = %string%)
                            AND (games2.leaderid = %string%)
                          )
                        )", $tournamentid, $_GET["group"], $leader_array[$x], $leader_array[$y-1], $leader_array[$y-1], $leader_array[$x]);

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
