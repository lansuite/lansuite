<?php

$xml = new xml();

function CheckModeForWWCLLeague($league)
{
    if ($league and $_POST['mode'] != 'single' and $_POST['mode'] != 'double' and $_POST['mode'] != 'groups') {
        return t('WWCL-Turniere müssen im SE, DE oder Gruppenspiele Modus ausgetragen werden');
    } else {
        return false;
    }
}

function CheckModeForLeague($league)
{
    if ($league and $_POST['mode'] != 'single' and $_POST['mode'] != 'double') {
        return t('Diese Liga ist nur im SE und DE Modus möglich');
    } else {
        return false;
    }
}

function CheckDateInFuture($date)
{
    global $lang, $func, $mf;

    if (!$mf->isChange and $func->str2time($date) < time()) {
        return t('Dieses Datum liegt in der Vergangenheit');
    } else {
        return false;
    }
}

function CheckModeChangeAllowed($mode)
{
    global $mf, $db, $lang;

    $t = $db->qry_first('SELECT mode, status FROM %prefix%tournament_tournaments WHERE tournamentid = %int%', $_GET['tournamentid']);
    if ($mf->isChange and $t['status'] != 'open' and $t['mode'] != $mode) {
        if ($t['mode'] == 'single' or $t['mode'] == 'double') {
            if ($mode != 'single' and $mode != 'double') {
                return t('Bei bereits generierten Turnieren darf der Modus nur noch zwischen Single-Elimintation und Double-Elimination geändert werden');
            }
        } else {
            return t('Bei bereits generierten Turnieren ist das ändern des Modus nur noch bei Single-Elimintation und Double-Elimination erlaubt');
        }
    }
    return false;
}

function CheckStateChangeAllowed($state)
{
    if ($state == 'process') {
        return t('Dieser Status kann nicht manuell gesetzt werden. Zum setzen, bitte "Generieren" verwenden');
    }
    if ($state == 'closed') {
        return t('Dieser Status kann nicht manuell gesetzt werden. Er wird automatisch gesetzt, sobald das letzte Ergebnis im Turnier eingetragen wurde');
    }
    return false;
}

$mf = new masterform();

// Name
$mf->AddField(t('Turniername'), 'name');
$mf->AddField(t('Spiel'), 'game');
$mf->AddField(t('Version'), 'version', '', '', FIELD_OPTIONAL);
$mf->AddDropDownFromTable(t('Turniermanagement'), 'tournamentadmin', 'userid', 'username', 'user', t('Keinem zugeordnet'), 'type >= 2');
$mf->AddDropDownFromTable(t('Technik/Server'), 'techadmin', 'userid', 'username', 'user', t('Keinem zugeordnet'), 'type >= 2');

$t_state = $db->qry_first('SELECT status FROM %prefix%tournament_tournaments WHERE tournamentid=%int%', $_GET['tournamentid']);

if ($t_state['status'] == 'process') {
    $mf->AddField(t('Status'), '', IS_TEXT_MESSAGE, t('Turnier wird gerade gespielt'));
} elseif ($t_state['status'] == 'closed') {
    $mf->AddField(t('Status'), '', IS_TEXT_MESSAGE, t('Turnier wurde beendet'));
} else {
    $selections = array();
    if ($_POST['status'] == '') {
        $_POST['status'] = 'open';
    }
    $selections['invisible'] = t('Unsichtbar (nur Admins können das Turnier sehen)');
    $selections['locked'] = t('Anmeldung geschlossen (Turnier ist sichtbar, jedoch kann sich keiner anmelden)');
    $selections['open'] = t('Anmeldung geöffnet');
    $selections['process'] = t('Turnier wird gerade gespielt (Status wird automatisch durch Klick auf "Generieren" gesetzt)');
    $selections['closed'] = t('Turnier beendet (Diese Option schaltet die Rangliste frei)');
    $mf->AddField(t('Status'), 'status', IS_SELECTION, $selections, '', 'CheckStateChangeAllowed');
}
$mf->AddGroup('Allgemein');


// Mode
$selections = array();
for ($i = 1; $i <= 20; $i++) {
    $selections[$i] = $i;
}
$mf->AddField(t('Spieler pro Team'), 'teamplayer', IS_SELECTION, $selections);

$selections = array();
if ($_POST['maxteams'] == '') {
    $_POST['maxteams'] = 1024;
}
for ($i = 8; $i <= 1024; $i*=2) {
    $selections[$i] = $i;
}
$mf->AddField(t('Maximale Teamanzahl'), 'maxteams', IS_SELECTION, $selections);

$selections = array();
if ($_POST['mode'] == '') {
    $_POST['mode'] = 'double';
}
$selections['single'] = t('Single-Elimination');
$selections['double'] = t('Double-Elimination');
$selections['liga'] = t('Liga');
$selections['groups'] = t('Gruppenspiele + KO');
$selections['all'] = t('Alle in einem');
$mf->AddField(t('Spiel-Modus'), 'mode', IS_SELECTION, $selections, '', 'CheckModeChangeAllowed');

$mf->AddField(t('Blind Draw').'|'.t('Teammitglieder werden zugelost'), 'blind_draw', '', '', FIELD_OPTIONAL);
$mf->AddGroup(t('Turniermodus'));


// Limits
$selections = array();
$selections[0] = t('Keine');
for ($i = 1; $i <= 20; $i++) {
    $selections[$i] = $i;
}
$mf->AddField(t('Turniergruppe'), 'groupid', IS_SELECTION, $selections, FIELD_OPTIONAL);

$selections = array();
for ($i = 0; $i <= 10; $i++) {
    $selections[$i] = t('Teilnahme kostet') .' '. $i .' '. t('Coins');
}
$mf->AddField(t('Coin-Kosten'), 'coins', IS_SELECTION, $selections, FIELD_OPTIONAL);

$mf->AddField(t('U18-Sperre').'|'.t('Keine Spieler aus Unter-18-Sitzblöcken zulassen'), 'over18', '', '', FIELD_OPTIONAL);
$mf->AddGroup(t('Anmeldeeinschränkungen'));


// Times
if (!$_POST['starttime']) {
    $_POST['starttime'] = date('Y-m-d H:i', $_SESSION['party_info']['partybegin']);
}
$mf->AddField(t('Turnier beginnt um'), 'starttime', '', '', '', CheckDateInFuture);

$selections = array();
if ($_POST['game_duration'] == '') {
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
$mf->AddField(t('Maximale Spiele pro Runde'), 'max_games', IS_SELECTION, $selections);

$selections = array();
if ($_POST['break_duration'] == '') {
    $_POST['break_duration'] = '30';
}
$mf->AddField(t('Pause nach jeder Runde (Min.)'), 'break_duration');

$mf->AddField(t('Keine Zeitüberschreitung').'|'.t('Bei Zeitüberschreitung (Beginn der Pause) wird der Gewinner automatisch gelost'), 'defwin_on_time_exceed', '', 1, FIELD_OPTIONAL);
$mf->AddGroup(t('Zeiten'));
$mf->AddPage(t('Haupteinstellungen'));

// League + Misc
$mf->AddField(t('Icon'), 'icon', IS_PICTURE_SELECT, 'ext_inc/tournament_icons', FIELD_OPTIONAL);

// WWCL-Spiel Auswahl
$xml_file = "";
$file = "ext_inc/tournament_rules/gameini.xml";
$handle = fopen($file, "rb");
$xml_file = fread($handle, filesize($file));
fclose($handle);

$selections = array();
$game_ids = $xml->get_tag_content_array("id", $xml_file);
$game_namen = $xml->get_tag_content_array("name", $xml_file);
while ($akt_game_id = array_shift($game_ids)) {
    $akt_game_name = array_shift($game_namen);
    $selections[$akt_game_id] = $akt_game_name;
}
asort($selections);
$selections = array('0' => t('Kein WWCL-Support für dieses Turnier')) + $selections;
$mf->AddField(t('WWCL-Spiel'), 'wwcl_gameid', IS_SELECTION, $selections, FIELD_OPTIONAL, 'CheckModeForWWCLLeague');

// NGL-Spiel auswahl
$xml_file = "";
$file = "ext_inc/tournament_rules/games.xml";
$handle = fopen($file, "rb");
$xml_file = fread($handle, filesize($file));
fclose($handle);

$selections = array();
# and $cfg["sys_country"] != "at" and $cfg["sys_country"] != "ch"
if ($cfg["sys_country"] != "de") {
    $mf->AddField(t('NGL-Support ist nur für Partys in Deutschland möglich. Das Land deiner Party kannst du auf der Adminseite einstellen'), 'ngl_gamename', IS_TEXT_MESSAGE, t('NGL-Support ist nur in Deutschland, Österreich, oder der Schweiz möglich. Das Land deiner Party kannst du auf der Adminseite einstellen'));
} else {
    $country_xml = $xml->get_tag_content("country short=\"{$cfg["sys_country"]}\"", $xml_file);
    $liga_xml = $xml->get_tag_content_array("league", $xml_file);
    while ($akt_liga = array_shift($liga_xml)) {
        $info_xml = $xml->get_tag_content_array("info", $akt_liga);
        while ($akt_info = array_shift($info_xml)) {
            $info_title = $xml->get_tag_content("title", $akt_info);
        }

        $game_xml = $xml->get_tag_content_array("game", $akt_liga);

        if (is_array($game_xml)) {
            while ($game_xml_id = array_shift($game_xml)) {
                $akt_game_id = $xml->get_tag_content("short", $game_xml_id);
                $akt_game_name = $xml->get_tag_content("title", $game_xml_id);
                $selections[$akt_game_id] = $info_title .' - '. $akt_game_name;
            }
        }
    }
    asort($selections);
    $selections = array('' => t('Kein NGL-Support für dieses Turnier')) + $selections;
    $mf->AddField(t('NGL-Spiel'), 'ngl_gamename', IS_SELECTION, $selections, FIELD_OPTIONAL, 'CheckModeForLeague');
}

// LGZ-Spiel auswahl
$xml_file = "";
$file = "ext_inc/tournament_rules/xml_games.xml";
$handle = fopen($file, "rb");
$xml_file = fread($handle, filesize($file));
fclose($handle);

$selections = array();
$games = $xml->get_tag_content_array("game", $xml_file);
foreach ($games as $game) {
    $akt_game_name = $xml->get_tag_content("contest", $game) .' - '. $xml->get_tag_content("name", $game);
    $syscode = $xml->get_tag_content("syscode", $game);
    $selections[$syscode] = $akt_game_name;
}
asort($selections);
$selections = array('' => t('Kein LGZ-Support für dieses Turnier')) + $selections;
$mf->AddField(t('LGZ-Spiel'), 'lgz_gamename', IS_SELECTION, $selections, FIELD_OPTIONAL, 'CheckModeForLeague');

// Rules (Extern)
$selections = array();
$verz = opendir('ext_inc/tournament_rules/');
while ($file_name = readdir($verz)) {
    if (!is_dir('ext_inc/tournament_rules/'.$file_name) and $file_name != 'gameini.xml'
    and $file_name != 'games.xml' and $file_name != 'info.txt' and $file_name != 'xml_games.xml') {
        $selections[$file_name] = $file_name;
    }
}
closedir($verz);
asort($selections);
$selections = array('' => t('Keines')) + $selections;
$mf->AddField(t('Externes Regelwerk'), 'rules_ext', IS_SELECTION, $selections, FIELD_OPTIONAL);

$mf->AddField(t('Bemerkung / Zusätzliche Regeln'), 'comment', '', HTML_ALLOWED, FIELD_OPTIONAL);
$mf->AddField(t('Mapcycle (Maps durch Zeilenumbruch trennen)'), 'mapcycle', '', '', FIELD_OPTIONAL);
$mf->AddGroup(t('Liga-Support, Regeln und Mapcycle'));
$mf->AddPage(t('Liga-Support, Regeln und Mapcycle'));

if (!$_GET['tournamentid']) {
    $mf->AddFix('party_id', (int)$party->party_id);
}

if ($mf->SendForm('index.php?mod=tournament2&action='. $_GET['action'], 'tournament_tournaments', 'tournamentid', $_GET['tournamentid'])) {
    $func->log_event(t('Das Turnier %1 wurde eingetragen', $_POST["name"]), 1, t('Turnier Verwaltung'));
}
