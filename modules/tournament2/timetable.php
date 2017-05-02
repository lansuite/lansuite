<?php

include_once("modules/tournament2/class_tournament.php");
$tfunc = new tfunc;


$dsp->NewContent(t('Turnier-Zeitplan'), t('Hier siehst du, welches Turnier zu welcher Zeit stattfindet.'));

// Generate Table-head
$mintime = 9999999999;
$maxtime = 0;
$tournaments = $db->qry("SELECT *, UNIX_TIMESTAMP(starttime) AS starttime
  FROM %prefix%tournament_tournaments
  WHERE party_id = %int% AND (%int% > 1 OR status != 'invisible')", $party->party_id, $auth['type']);
while ($tournament = $db->fetch_array($tournaments)) {
    // Calc Min-Time
    if ($tournament["starttime"] < $mintime) {
        $mintime = $tournament["starttime"];
    }

    // Calc Max-Time
    $team_anz = $tfunc->GetTeamAnz($tournament["tournamentid"], $tournament["mode"]);
    $max_round = 1;
    for ($z = $team_anz/2; $z >= 2; $z/=2) {
        $max_round++;
    }
    $endtime = $tfunc->GetGameEnd($tournament, $max_round);
    if ($endtime > $maxtime) {
        $maxtime = $endtime;
    }
}
$db->free_result($tournaments);

if ($maxtime > $mintime + 60 * 60 * 24 * 4) {
    $maxtime = $mintime + 60 * 60 * 24 * 4;
}

$head .= "<td><b>".t('Turnier')."</b></td>";
for ($z = $mintime; $z <= $maxtime; $z+= (60 * 60 * 2)) {
    $head .= "<td colspan = 4>". $func->unixstamp2date($z, "time")."</td>";
}
$smarty->assign('head', $head);

// Generate Table-foot
$rows = "";
$tournaments = $db->qry("SELECT *, UNIX_TIMESTAMP(starttime) AS starttime
  FROM %prefix%tournament_tournaments
  WHERE party_id = %int% AND (%int% > 1 OR status != 'invisible')", $party->party_id, $auth['type']);
while ($tournament = $db->fetch_array($tournaments)) {
    #	echo "Zeit {$tournament["starttime"]}<br>";

    $team_anz = $tfunc->GetTeamAnz($tournament["tournamentid"], $tournament["mode"]);
    $max_round = 1;
    for ($z = $team_anz/2; $z >= 2; $z/=2) {
        $max_round++;
    }
    $endtime = $tfunc->GetGameEnd($tournament, $max_round);

    $content = "<td nowrap>{$tournament["name"]}</td>";
    for ($z = $mintime; $z <= $maxtime; $z+= (60 * 30)) {
        if ($z > $tournament["starttime"] and $z <= $endtime) {
            $content .= "<td bgcolor=\"#00bb33\">&nbsp;</td>";
        } else {
            if (($z/(60 * 30)) % 2 == 0) {
                $content .= "<td bgcolor=\"#dddddd\">&nbsp;</td>";
            } else {
                $content .= "<td bgcolor=\"#aaaaaa\">&nbsp;</td>";
            }
        }
    }
    $smarty->assign('content', $content);
    $rows .= $smarty->fetch('modules/tournament2/templates/timetable_zeile.htm');
}
$db->free_result($tournaments);

$smarty->assign('rows', $rows);
$dsp->AddSmartyTpl('timetable', 'tournament2');


$dsp->AddSingleRow(t('Achtung: Der Zeitraum eines Turnieres kann sich verlängern, wenn sich weitere Teams zu diesem Turnier anmelden.'));
$dsp->AddBackButton("index.php?mod=tournament2", "tournament2/timetable");
$dsp->AddContent();
