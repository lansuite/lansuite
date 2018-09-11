<?php

// This File is a Part of the LS-Pluginsystem. It will be included in
// modules/usrmgr/details.php to generate Modulspezific Headermenue
// for Userdetails

// League IDs
$dsp->AddFieldsetStart(t('Ligen'));
    $wwcl = '';
if ($user_data['wwclid']) {
    $wwcl .= $user_data['wwclid'] .' ';
}
if ($user_data['wwclclanid']) {
    $wwcl .= '('. $user_data['wwclclanid'] .')';
}

$dsp->AddDoubleRow(t('WWCL-ID'). ' (Clan-ID)', $wwcl);
$ngl = '';
if ($user_data['nglid']) {
    $ngl .= $user_data['nglid'] .' ';
}
if ($user_data['nglclanid']) {
    $ngl .= '('. $user_data['nglclanid'] .')';
}

$dsp->AddDoubleRow(t('NGL-ID'). ' (Clan-ID)', $ngl);
$lgz = '';
if ($user_data['lgzid']) {
    $lgz .= $user_data['lgzid'] .' ';
}
if ($user_data['lgzclanid']) {
    $lgz .= '('. $user_data['lgzclanid'] .')';
}

$dsp->AddDoubleRow(t('LGZ-ID').' (Clan-ID)', $lgz);
$dsp->AddFieldsetEnd();

$mail = new \LanSuite\Module\Mail\Mail();
$seat2 = new \LanSuite\Module\Seating\Seat2();

$tfunc = new \LanSuite\Module\Tournament2\TournamentFunction($mail, $seat2);

// Own Teams
$dsp->AddFieldsetStart(t('Benutzer hat folgende Teams er&ouml;ffnet'));
$leader_teams = $db->qry("
  SELECT
    t.name,
    t.tournamentid AS tid,
    team.name AS teamname,
    team.teamid 
  FROM %prefix%t2_teams AS team
  LEFT JOIN %prefix%tournament_tournaments AS t ON t.tournamentid = team.tournamentid
  WHERE
    team.leaderid = %int%
    AND t.party_id = %int%", $_GET['userid'], $cfg['signon_partyid']);

if ($db->num_rows($leader_teams) == 0) {
    $dsp->AddSingleRow('<i>-'. t('Keine') .'-</i>');
} else {
    while ($leader_team = $db->fetch_array($leader_teams)) {
        $dsp->AddDoubleRow('<a href="index.php?mod=tournament2&action=details&tournamentid='. $leader_team['tid']. '">'. $leader_team['name'] .'</a>', $leader_team['teamname'] .' '. $tfunc->button_team_details($leader_team['teamid'], $leader_team['tid']));
    }
}
$dsp->AddFieldsetEnd();

// Teammember
$dsp->AddFieldsetStart(t('Benutzer ist in folgenden Teams Mitglied'));
$member_teams = $db->qry("
  SELECT
    t.name,
    t.tournamentid AS tid,
    team.name AS teamname,
    team.teamid 
  FROM %prefix%t2_teams AS team
  LEFT JOIN %prefix%tournament_tournaments AS t ON t.tournamentid = team.tournamentid
  LEFT JOIN %prefix%t2_teammembers AS m ON team.teamid = m.teamid
  WHERE
    m.userid = %int%
    AND t.party_id = %int%", $_GET['userid'], $cfg['signon_partyid']);
if ($db->num_rows($member_teams) == 0) {
    $dsp->AddSingleRow('<i>-'. t('Keine') .'-</i>');
} else {
    while ($member_team = $db->fetch_array($member_teams)) {
        $dsp->AddDoubleRow('<a href="index.php?mod=tournament2&action=details&tournamentid='. $member_team['tid']. '">'. $member_team['name'] .'</a>', $member_team['teamname'] .' '. $tfunc->button_team_details($member_team['teamid'], $member_team['tid']));
    }
}
$dsp->AddFieldsetEnd();
