<?php
$ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2();

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
