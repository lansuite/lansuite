<?php
include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2();

function GetTournamentName($name)
{
    global $line, $auth, $lang;

    $return = '';
    // Game Icon
    if ($line['icon'] and $line['icon'] != 'none' and file_exists("ext_inc/tournament_icons/{$line['icon']}")) {
        $return .= "<img src=\"ext_inc/tournament_icons/{$line['icon']}\" title=\"Icon\" border=\"0\" /> ";
    }
    // Name
    $return .= $name;
    // WWCL Icon
    if ($line['wwcl_gameid']) {
        $return .= ' <img src="ext_inc/tournament_icons/leagues/wwcl.png" title="WWCL Game\" border="0" />';
    }
    // NGL Icon
    if ($line['ngl_gamename']) {
        $return .= ' <img src="ext_inc/tournament_icons/leagues/ngl.png" title="NGL Game" border="0" />';
    }
    // LGZ Icon
    if ($line['lgz_gamename']) {
        $return .= ' <img src="ext_inc/tournament_icons/leagues/lgz.png" title="LGZ Game" border="0" />';
    }
    // Over 18 Icon
    if ($line['over18']) {
        $return .= " <img src='design/".$auth["design"]."/images/fsk_18.gif' title=\"".t('cb_t_over18')."\" border=\"0\" />";
    }

    return $return;
}

function GetTournamentUserIcon($userid)
{
    global $db,$dsp;
    $user = $db->qry_first("SELECT userid, username FROM %prefix%user WHERE userid = %int%", $userid);
    if ($userid == 0) {
        return '-';
    } else {
        return $dsp->FetchUserIcon($user['userid'], $user['username']);
    }
}
function GetTournamentTeamAnz($maxteams)
{
    global $line;
    return $line['teamanz'] .'/'. $maxteams;
}

function GetTournamentStatus($status)
{
    global $lang;
    $status_descriptor["open"]    = t('Anmeldung offen');
    $status_descriptor["locked"]    = t('Anmeldung geschlossen');
    $status_descriptor["invisible"]    = t('Unsichtbar');
    $status_descriptor["process"]    = t('Wird gespielt');
    $status_descriptor["closed"]    = t('Beendet');
    
    return $status_descriptor[$status];
}

function IfGenerated($tid)
{
    global $line;

    if ($line['status'] == 'process' or $line['status'] == 'closed') {
        return true;
    } else {
        return false;
    }
}

function IfNotGenerated($tid)
{
    global $line;

    if ($line['status'] == 'open' or $line['status'] == 'locked' or $line['status'] == 'invisible') {
        return true;
    } else {
        return false;
    }
}

function IfFinished($tid)
{
    global $line;

    if ($line['status'] == 'closed') {
        return true;
    } else {
        return false;
    }
}

$dsp->NewContent(t('Turnierübersicht'), t('Hier findest du eine Übersicht aller angebotenen Turniere.'));


$ms2->query['from'] = "%prefix%tournament_tournaments AS t LEFT JOIN %prefix%t2_teams AS teams ON t.tournamentid = teams.tournamentid";
$ms2->query['where'] = "(t.status != 'invisible' OR ". (int)$auth['type'] ." > 1) AND t.party_id = ". (int)$party->party_id;
$ms2->query['default_order_by'] = 't.name';

$ms2->config['EntriesPerPage'] = 50;

$ms2->AddSelect('t.over18');
$ms2->AddSelect('t.icon');
$ms2->AddSelect('t.wwcl_gameid');
$ms2->AddSelect('t.ngl_gamename');
$ms2->AddSelect('t.lgz_gamename');
$ms2->AddSelect('COUNT(teams.tournamentid) AS teamanz');
$ms2->AddResultField(t('Turniername'), 't.name', 'GetTournamentName');
$ms2->AddResultField(t('Admin'), 'tournamentadmin', 'GetTournamentUserIcon');
$ms2->AddResultField(t('Tech'), 'techadmin', 'GetTournamentUserIcon');
$ms2->AddResultField(t('Startzeit'), 'UNIX_TIMESTAMP(t.starttime) AS starttime', 'MS2GetDate');
$ms2->AddResultField(t('Team'), 't.maxteams', 'GetTournamentTeamAnz');
$ms2->AddResultField(t('Status'), 't.status', 'GetTournamentStatus');

$ms2->AddIconField('details', 'index.php?mod=tournament2&action=details&tournamentid=', t('Details'));
$ms2->AddIconField('tree', 'index.php?mod=tournament2&action=tree&step=2&tournamentid=', t('Spielbaum'), 'IfGenerated');
$ms2->AddIconField('play', 'index.php?mod=tournament2&action=games&step=2&tournamentid=', t('Paarungen'), 'IfGenerated');
$ms2->AddIconField('ranking', 'index.php?mod=tournament2&action=rangliste&step=2&tournamentid=', t('Rangliste'), 'IfFinished');
if ($auth['type'] >= 2) {
    $ms2->AddIconField('generate', 'index.php?mod=tournament2&action=generate_pairs&step=2&tournamentid=', t('Generieren'), 'IfNotGenerated');
}
if ($auth['type'] >= 2) {
    $ms2->AddIconField('edit', 'index.php?mod=tournament2&action=change&step=1&tournamentid=', t('Editieren'));
}
if ($auth['type'] >= 3) {
    $ms2->AddIconField('delete', 'index.php?mod=tournament2&action=delete&step=2&tournamentid=', t('Löschen'));
}

if ($auth['type'] >= 3) {
    $ms2->AddMultiSelectAction('Anmeldung &ouml;ffnen', 'index.php?mod=tournament2&action=changestat&step=open', 1);
}
if ($auth['type'] >= 3) {
    $ms2->AddMultiSelectAction('Anmeldung sperren', 'index.php?mod=tournament2&action=changestat&step=lock', 1);
}
if ($auth['type'] >= 3) {
    $ms2->AddMultiSelectAction('L&ouml;schen', 'index.php?mod=tournament2&action=delete&step=10', 1);
}

$ms2->PrintSearch('index.php?mod=tournament2', 't.tournamentid');
