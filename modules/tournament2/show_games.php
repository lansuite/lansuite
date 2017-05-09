<?php

function array_to_table($a)
{
    if (!empty($a)) {
        function remove($var)
        {
            if ($var=='cellstyle') {
                return false;
            } else {
                return true;
            }
        }
        $colums=array_filter(array_keys($a[0]), "remove");
        $t='<div overflow:auto;"><table style="width:100%;" border="0" cellspacing="0" cellpadding="2">';
        $t.='<tr><th class="mastersearch2_result_row_key" style="border-bottom: 1px solid #000000;">'.implode('</th><th class="mastersearch2_result_row_key" style="border-bottom: 1px solid #000000;">', $colums).'</th></tr>';
        foreach ($a as $row) {
            $cellstyle = $row['cellstyle'];
            unset($row['cellstyle']);
            $t.= '<tr><td style="border-bottom: 1px solid #000000;'.$cellstyle.'">'.implode('</td><td style="border-bottom: 1px solid #000000;'.$cellstyle.'">', $row).'</td></tr>';
        }
        $t.='</table></div>';
    } else {
        $t = '';
    }
    return $t;
}

include_once("modules/seating/class_seat.php");
$seat2 = new seat2();

$tgames = $db->qry("SELECT games1.gameid AS gid1, games2.gameid AS gid2, teams1.name AS name1, 
        teams2.name AS name2, teams1.leaderid AS leaderid1, teams2.leaderid AS leaderid2, t.name AS tuname, 
        t.mode AS modus , t.tournamentid AS tid,
        IF (games1.lastchange>games2.lastchange,games1.lastchange,games2.lastchange) AS lastactivity,
        games1.round, t.game_duration, t.break_duration,
        IF (games1.round=0,(t.game_duration*60),((t.game_duration+t.break_duration)*60)) AS overtime, (UNIX_TIMESTAMP(CURRENT_TIMESTAMP())-UNIX_TIMESTAMP(IF(games1.lastchange>games2.lastchange,games1.lastchange,games2.lastchange))) AS overtime2
FROM %prefix%t2_games AS games1 
INNER JOIN %prefix%t2_games AS games2 ON (games1.tournamentid = games2.tournamentid) AND (games1.round = games2.round) 
LEFT JOIN %prefix%tournament_tournaments AS t ON (t.tournamentid = games1.tournamentid) 
LEFT JOIN %prefix%t2_teams AS teams1 ON (games1.leaderid = teams1.leaderid) AND (t.tournamentid = teams1.tournamentid) 
LEFT JOIN %prefix%t2_teams AS teams2 ON (games2.leaderid = teams2.leaderid) AND (t.tournamentid = teams2.tournamentid) 
LEFT JOIN %prefix%t2_teammembers AS memb1 ON (teams1.teamid = memb1.teamid) LEFT JOIN %prefix%t2_teammembers AS 
memb2 ON (teams2.teamid = memb2.teamid) 
WHERE ((games1.position / 2) = FLOOR(games1.position / 2)) 
    AND (games1.score = 0) 
    AND (games1.leaderid != 0) 
    AND ((games1.position + 1) = games2.position) 
    AND (games2.score = 0) 
    AND (games2.leaderid !=0) 
    AND (teams1.disqualified = '0') 
    AND (teams2.disqualified = '0') 
    AND (t.party_id = %int%) 
    AND (t.status = 'process') 
    AND NOT (mode = 'all')
    GROUP BY games1.gameid, games2.gameid 
    ORDER BY lastactivity ASC 
", $party->party_id);

while ($tgamesrow = $db->fetch_array($tgames, 1, MYSQLI_ASSOC)) {
    //d($tgamesrow);
    //$outputrow['Begegnung'] = "<b>".$tgamesrow['name1']."</b> vs <b>".$tgamesrow['name2']."</b>";
    if (!($tgamesrow['modus']=="single" and $tgamesrow['round']<0) and !($tgamesrow['modus']=="all")) { // Workaround wegen Looserbraketeinträgen bei SingleElimination
        $outall[] = $tgamesrow;
        //$outputrow['Spieler 1'] = $tgamesrow['name1'];
        $outputrow['Spieler/Team 1'] = $dsp->FetchUserIcon($tgamesrow['leaderid1'], "<b>".$tgamesrow['name1']."</b>") . " </br>". $seat2->SeatNameLink($tgamesrow['leaderid1'], '', '') ."";
        //$outputrow['Spieler 2'] = $tgamesrow['name2'];
        $outputrow['Spieler/Team 2'] = $dsp->FetchUserIcon($tgamesrow['leaderid2'], "<b>".$tgamesrow['name2']."</b>") . " </br>". $seat2->SeatNameLink($tgamesrow['leaderid2'], '', '') ."";
        $outputrow['Turnier'] = "<a href=\"?mod=tournament2&action=details&tournamentid=".$tgamesrow['tid']."\"><b>".$tgamesrow['tuname']."</b></a>";
        //$outputrow['Startzeit'] = $tgamesrow['lastactivity'];
        $tage = array("So", "Mo", "Di", "Mi", "Do", "Fr", "Sa");
        $outputrow['Startzeit'] = $tage[date('w', strtotime($tgamesrow['lastactivity']))]." ".date('H:i', strtotime($tgamesrow['lastactivity']));
        $outputrow['Spielzeit+</br>Pause'] = $tgamesrow[game_duration]."+".$tgamesrow[break_duration]."min";
        $delay = (($tgamesrow['overtime2'])-($tgamesrow['overtime']))/60;
        if ($delay>=120) {
            $outputrow['cellstyle'] = "background-color:#FF1000;";
            $outputrow['Überfällig'] = (floor($delay/60))."Std ".(floor($delay%60))."Min";
        } elseif ($delay>=0 and $delay<120) {
            $outputrow['cellstyle'] = "background-color:#BD7C85;";
            $outputrow['Überfällig'] = (floor($delay/60))."Std ".(floor($delay%60))."Min";
        } else {
            $outputrow['Überfällig'] = (ceil($delay/60))."Std ".(ceil($delay%60))."Min";
            $outputrow['cellstyle'] = "background-color:#00814A;";
        }
        $outputrow['Akt.'] ="<div style=\"text-align:right;\">".$dsp->AddIcon('search', 'index.php?mod=tournament2&action=submit_result&step=1&tournamentid='.$tgamesrow['tid'].'&gameid1='.$tgamesrow['gid1'].'&gameid2='.$tgamesrow['gid2'])."</div>";
        $tgamestable[] = $outputrow;
    }
}

$dsp->NewContent(t('Aktuelle Turnierbegegnungen'), t('Aktuelle Turnierbegegnungen sortiert nach Zeit. Überfällige werden Rot markiert.'));

if ($tgamestable==null) {
    $dsp->AddSingleRow("Aktuell keine Paarungen vorhanden.");
} else {
    $dsp->AddSingleRow(array_to_table($tgamestable));
}

$db->free_result($tgames);
