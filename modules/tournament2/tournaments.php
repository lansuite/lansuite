<?php

$xml = new \LanSuite\XML();
$mf = new \LanSuite\MasterForm();

// Name
$mf->AddField(t('Turniername'), 'name');
$mf->AddField(t('Spiel'), 'game');
$mf->AddField(t('Version'), 'version', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
$mf->AddDropDownFromTable(t('Turniermanagement'), 'tournamentadmin', 'userid', 'username', 'user', t('Keinem zugeordnet'), 'type >= 2');
$mf->AddDropDownFromTable(t('Technik/Server'), 'techadmin', 'userid', 'username', 'user', t('Keinem zugeordnet'), 'type >= 2');

$tournamentIDParameter = $_GET['tournamentid'] ?? 0;
$t_state = $database->queryWithOnlyFirstRow('SELECT status FROM %prefix%tournament_tournaments WHERE tournamentid = ?', [$tournamentIDParameter]);

if (is_array($t_state) && $t_state['status'] == 'process') {
    $mf->AddField(t('Status'), '', \LanSuite\MasterForm::IS_TEXT_MESSAGE, t('Turnier wird gerade gespielt'));
} elseif (is_array($t_state) && $t_state['status'] == 'closed') {
    $mf->AddField(t('Status'), '', \LanSuite\MasterForm::IS_TEXT_MESSAGE, t('Turnier wurde beendet'));
} else {
    $selections = array();
    $statusParameter = $_POST['status'] ?? '';
    if ($statusParameter == '') {
        $_POST['status'] = 'open';
    }
    $selections['invisible'] = t('Unsichtbar (nur Admins können das Turnier sehen)');
    $selections['locked'] = t('Anmeldung geschlossen (Turnier ist sichtbar, jedoch kann sich keiner anmelden)');
    $selections['open'] = t('Anmeldung geöffnet');
    $selections['process'] = t('Turnier wird gerade gespielt (Status wird automatisch durch Klick auf "Generieren" gesetzt)');
    $selections['closed'] = t('Turnier beendet (Diese Option schaltet die Rangliste frei)');
    $mf->AddField(t('Status'), 'status', \LanSuite\MasterForm::IS_SELECTION, $selections, '', 'CheckStateChangeAllowed');
}
$mf->AddGroup('Allgemein');

// Mode
$selections = array();
for ($i = 1; $i <= 20; $i++) {
    $selections[$i] = $i;
}
$mf->AddField(t('Spieler pro Team'), 'teamplayer', \LanSuite\MasterForm::IS_SELECTION, $selections);

$selections = array();
$maxTeamsParameter = $_POST['maxteams'] ?? '';
if ($maxTeamsParameter == '') {
    $_POST['maxteams'] = 1024;
}
for ($i = 8; $i <= 1024; $i*=2) {
    $selections[$i] = $i;
}
$mf->AddField(t('Maximale Teamanzahl'), 'maxteams', \LanSuite\MasterForm::IS_SELECTION, $selections);

$selections = array();
$modeParameter = $_POST['mode'] ?? '';
if ($modeParameter == '') {
    $_POST['mode'] = 'double';
}
$selections['single'] = t('Single-Elimination');
$selections['double'] = t('Double-Elimination');
$selections['liga'] = t('Liga');
$selections['groups'] = t('Gruppenspiele + KO');
$selections['all'] = t('Alle in einem');
$mf->AddField(t('Spiel-Modus'), 'mode', \LanSuite\MasterForm::IS_SELECTION, $selections, '', 'CheckModeChangeAllowed');

$mf->AddField(t('Blind Draw').'|'.t('Teammitglieder werden zugelost'), 'blind_draw', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
$mf->AddGroup(t('Turniermodus'));

// Limits
$selections = array();
$selections[0] = t('Keine');
for ($i = 1; $i <= 20; $i++) {
    $selections[$i] = $i;
}
$mf->AddField(t('Turniergruppe'), 'groupid', \LanSuite\MasterForm::IS_SELECTION, $selections, \LanSuite\MasterForm::FIELD_OPTIONAL);

$selections = array();
for ($i = 0; $i <= 10; $i++) {
    $selections[$i] = t('Teilnahme kostet') .' '. $i .' '. t('Coins');
}
$mf->AddField(t('Coin-Kosten'), 'coins', \LanSuite\MasterForm::IS_SELECTION, $selections, \LanSuite\MasterForm::FIELD_OPTIONAL);

$mf->AddField(t('U18-Sperre').'|'.t('Keine Spieler aus Unter-18-Sitzblöcken zulassen'), 'over18', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
$mf->AddGroup(t('Anmeldeeinschränkungen'));

// Times
$startTimeParameter = $_POST['starttime'] ?? null;
if (!$startTimeParameter) {
    $_POST['starttime'] = date('Y-m-d H:i', $_SESSION['party_info']['partybegin']);
}
$mf->AddField(t('Turnier beginnt um'), 'starttime', '', '', '', 'CheckDateInFuture');

$selections = array();
$gameDurationParameter = $_POST['game_duration'] ?? '';
if ($gameDurationParameter == '') {
    $_POST['game_duration'] = '30';
}
$mf->AddField(t('Dauer eines Spieles (Min.)'), 'game_duration');

$selections = array();
if ($_POST['mode'] == '') {
    $_POST['mode'] = 'double';
}
$selections['1'] = '1';
$selections['2'] = '2';
$selections['3'] = '3 (Best Of 3)';
$selections['4'] = '4';
$selections['5'] = '5 (Best Of 5)';
$mf->AddField(t('Maximale Spiele pro Runde'), 'max_games', \LanSuite\MasterForm::IS_SELECTION, $selections);

$selections = array();
$breakDurationParameter = $_POST['break_duration'] ?? '';
if ($breakDurationParameter == '') {
    $_POST['break_duration'] = '30';
}
$mf->AddField(t('Pause nach jeder Runde (Min.)'), 'break_duration');

$mf->AddField(t('Keine Zeitüberschreitung').'|'.t('Bei Zeitüberschreitung (Beginn der Pause) wird der Gewinner automatisch gelost'), 'defwin_on_time_exceed', '', 1, \LanSuite\MasterForm::FIELD_OPTIONAL);
$mf->AddGroup(t('Zeiten'));
$mf->AddPage(t('Haupteinstellungen'));

// League + Misc
$mf->AddField(t('Icon'), 'icon', \LanSuite\MasterForm::IS_PICTURE_SELECT, 'ext_inc/tournament_icons', \LanSuite\MasterForm::FIELD_OPTIONAL);

// Rules (Extern)
$selections = array();
$verz = opendir('ext_inc/tournament_rules/');
while ($file_name = readdir($verz)) {
    if (!is_dir('ext_inc/tournament_rules/'.$file_name)
    and $file_name != 'info.txt' and $file_name != 'xml_games.xml') {
        $selections[$file_name] = $file_name;
    }
}
closedir($verz);
asort($selections);
$selections = array('' => t('Keines')) + $selections;
$mf->AddField(t('Externes Regelwerk'), 'rules_ext', \LanSuite\MasterForm::IS_SELECTION, $selections, \LanSuite\MasterForm::FIELD_OPTIONAL);

$mf->AddField(t('Bemerkung / Zusätzliche Regeln'), 'comment', '', \LanSuite\MasterForm::HTML_ALLOWED, \LanSuite\MasterForm::FIELD_OPTIONAL);
$mf->AddField(t('Mapcycle (Maps durch Zeilenumbruch trennen)'), 'mapcycle', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
$mf->AddGroup(t('Liga-Support, Regeln und Mapcycle'));
$mf->AddPage(t('Liga-Support, Regeln und Mapcycle'));

if (!$tournamentIDParameter) {
    $mf->AddFix('party_id', (int)$party->party_id);
}

if ($mf->SendForm('index.php?mod=tournament2&action='. $_GET['action'], 'tournament_tournaments', 'tournamentid', $tournamentIDParameter)) {
    $func->log_event(t('Das Turnier %1 wurde eingetragen', $_POST["name"]), 1, t('Turnier Verwaltung'));
}
