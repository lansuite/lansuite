<?php

$gd = new \LanSuite\GD();

$tournamentid = $_GET["tournamentid"];
if ($_GET["group"] == "") {
    $_GET["group"] = 1;
}

$mail = new \LanSuite\Module\Mail\Mail();
$seat2 = new \LanSuite\Module\Seating\Seat2();

$tfunc = new \LanSuite\Module\Tournament2\TournamentFunction($mail, $seat2);

// Check if roundtime has exceeded and set awaiting scores randomly
$tfunc->CheckTimeExceed($tournamentid);


$tournament = $database->queryWithOnlyFirstRow("
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
    tournamentid = ?", [$tournamentid]);
$map = explode("\n", $tournament["mapcycle"]);
if ($map[0] == "") {
    $map[0] = t('unbekannt');
}

// Get number of teams
$team_anz = $tfunc->GetTeamAnz($tournamentid, $tournament["mode"], $_GET["group"]);

// If at least one team is present, and the tounrmanet is started
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

    $dg = 0;
    if (($tournament['mode'] == "single") || ($tournament['mode'] == "double")
        || (($tournament["mode"] == "groups") && ($_GET["group"] == 0))) {
        $akt_round = 0;
        write_pairs3("Winner", $team_anz);

        $akt_round = 1;
        if ($tournament['mode'] == "double") {
            $limit_round = 2;
        } else {
            $limit_round = 4;
        }
        for ($z = $team_anz/2; $z >= $limit_round; $z/=2) {
            write_pairs3("Winner", $z);
            if ($tournament['mode'] == "double") {
                $akt_round*=-1;
                $akt_round+=0.5;
                write_pairs3("Loser", $z);
                $akt_round-=0.5;
                write_pairs3("Loser", $z);
                $akt_round*=-1;
            }
            $akt_round++;
        }

        write_pairs3("Winner", 2);
    }

    if (($tournament['mode'] == "liga")
        || (($tournament["mode"] == "groups") && ($_GET["group"] > 0))) {
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
            $leader_array[]      = $leader["leaderid"];
            $leader_name_array[] = $leader["name"];
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
                $score = $database->queryWithOnlyFirstRow("
                  SELECT
                    games1.score AS s1,
                    games2.score AS s2,
                    games1.leaderid AS leader1
                  FROM %prefix%t2_games AS games1
                  INNER JOIN %prefix%t2_games AS games2 ON
                    (games1.tournamentid = games2.tournamentid)
                    AND (games1.round = games2.round)
                    AND (games1.group_nr = games2.group_nr)
                  WHERE
                    (games1.tournamentid = ?)
                    AND (games1.group_nr = ?)
                    AND ((games1.position + 1) = games2.position)
                    AND ((games1.position / 2) = FLOOR(games1.position / 2))
                    AND (
                      (
                        (games1.leaderid = ?)
                        AND (games2.leaderid = ?)
                      )
                    OR (
                      (games1.leaderid = ?)
                      AND (games2.leaderid = ?)
                      )
                    )", [$tournamentid, $_GET["group"], $leader_array[$x], $leader_array[$y-1], $leader_array[$y-1], $leader_array[$x]]);

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

    // Create PNG-Image
    $gd->PutImage("", "", false);
    if ($tournament["mode"] == "groups") {
        $gd->PutImage("ext_inc/tournament_trees/tournament_$tournamentid" . "_" . $_GET['group'] . ".png");
    } else {
        $gd->PutImage("ext_inc/tournament_trees/tournament_$tournamentid.png");
    }
}
