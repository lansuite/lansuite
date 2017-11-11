<?php

include_once("modules/tournament2/class_tournament.php");
$tfunc = new tfunc;

if (!$_GET["teamid"]) {
    $func->error(t('Es wurde kein Team ausgewählt! Die Anzeige der Team-Details ist daher nicht möglich'));
} else {
    $dsp->NewContent(t('Turnier Verwaltung'), t('Mit Hilfe des folgenden Formulars kannst du ein Turnier erstellen / ändern'));

    // Get Data
    $team = $db->qry_first("SELECT teams.name, teams.comment, teams.disqualified, teams.banner, users.username, users.userid
   FROM %prefix%t2_teams AS teams
   LEFT JOIN %prefix%user AS users ON (teams.leaderid = users.userid)
   WHERE (teams.teamid = %int%)
   ", $_GET["teamid"]);

    // Teamname
    $dsp->AddDoubleRow(t('Teamame'), $team['name']);

    // Disqualified
    if ($team['disqualified']) {
        $dsp->AddDoubleRow("", "<font color=\"#ff0000\">".t('Disqualifiziert')."</font>");
    }

    // Banner
    if ($team['banner']) {
        $dsp->AddSingleRow("<img src=\"ext_inc/team_banners/{$team['banner']}\" alt=\"{$team['banner']}\">");
    }

    // Leader
    include_once("modules/seating/class_seat.php");
    $seat2 = new seat2();
    $dsp->AddDoubleRow(t('Teamleiter'), $dsp->FetchUserIcon($team['userid'], $team['username']) . " (Platz: ". $seat2->SeatNameLink($team['userid'], '', '') .")");

    // Members
    $dsp->AddDoubleRow(t('Mitglieder'), $tfunc->GetMemberList($_GET["teamid"]));

    // Stats
    $game_anz = 0;
    $won = 0;
    $lost = 0;

    $games = $db->qry("SELECT g1.score AS s1, g2.score AS s2, g1.leaderid
   FROM %prefix%t2_games AS g1
   LEFT JOIN %prefix%t2_games AS g2 ON (g1.tournamentid = g2.tournamentid) AND (g1.round = g2.round) AND ((g1.position + 1) = g2.position)
   WHERE ((g1.score != 0) OR (g2.score != 0))
   AND ((g1.position / 2) = FLOOR(g1.position / 2))
   AND ((g1.leaderid = %int%) OR (g2.leaderid = %int%))
   ", $team['userid'], $team['userid']);
    while ($game = $db->fetch_array($games)) {
        $game_anz++;
        if ($game['leaderid'] == $team['userid']) {
            if ($game[s1] > $game[s2]) {
                $won++;
            } else {
                $lost++;
            }
        } else {
            if ($game[s1] > $game[s2]) {
                $lost++;
            } else {
                $won++;
            }
        }
    }
    $db->free_result($games);

    $stats2 = t('Spiele gewonnen') .": $won" . HTML_NEWLINE . t('Spiele verloren') .": $lost" . HTML_NEWLINE . t('Spiele insgesamt') .": $game_anz";
    if ($game_anz > 0) {
        $stats2 .= HTML_NEWLINE . t('Gewinnquote') .": ". ($won / $game_anz * 100) ."%";
    }
    $dsp->AddDoubleRow(t('Statistiken'), $stats2);

    // Comment
    $dsp->AddDoubleRow(t('Kommentar'), $func->text2html($team['comment']));

    // Output
    $dsp->AddBackButton($func->internal_referer, "tournament2/team_details");
    $dsp->AddContent();
}
