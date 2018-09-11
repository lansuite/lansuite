<?php
$dsp->NewContent(t('Bugtracker'), t('Hier kannst du Fehler melden, die bei der Verwendung dieses Systems auftreten, sowie Feature Wünsche äußern. Können die Admins dieser Webseite sie nicht selbst beheben, haben diese die Möglichkeit sie an das Lansuite-Team weiterzureichen.'));

$row = $db->qry_first('SELECT reporter FROM %prefix%bugtracker WHERE bugid = %int%', $_GET['bugid']);
if ($_GET['bugid'] and $auth['type'] < 2 and $row['reporter'] != $auth['userid']) {
    $func->error(t('Nur Admins und der Reporter dürfen Bug-Einträge im Nachhinein editieren'), 'index.php?mod=bugtracker');
} else {
    $mf = new \LanSuite\MasterForm();

    $mf->AddField(t('Überschrift'), 'caption');

    $selections = array();
    $selections[''] = t('Bitte auswählen');
    $selections['1'] = t('Feature Wunsch');
    $selections['2'] = t('Schreibfehler');
    $selections['3'] = t('Kleiner Fehler');
    $selections['4'] = t('Schwerer Fehler');
    $selections['5'] = t('Absturz');
    $mf->AddField(t('Typ'), 'type', \LanSuite\MasterForm::IS_SELECTION, $selections);

    $mf->AddDropDownFromTable(t('Betrifft Modul'), 'module', 'name', 'name', 'modules', t('Nicht Modul-spezifisch'));

    if ($_SERVER['SERVER_NAME'] == 'lansuite.orgapage.de') {
        $mf->AddField(t('Betrifft Version'), 'version');
    }

    $selections = array();
    for ($z = 5; $z >= -5; $z--) {
        $selections[$z] = $z;
        if ($z == 5) {
            $selections[$z] .= ' ('. t('Sehr hoch') .')';
        }
        if ($z == -5) {
            $selections[$z] .= ' ('. t('Sehr gering') .')';
        }
    }
    $mf->AddField(t('Priorität'), 'priority', \LanSuite\MasterForm::IS_SELECTION, $selections, \LanSuite\MasterForm::FIELD_OPTIONAL);

    // Assign bug
    if ($auth['type'] >= 2) {
        $mf->AddDropDownFromTable(t('Bearbeiter'), 'agent', 'userid', 'username', 'user', t('Keinem zugeordnet'), 'type >= 2');
        $mf->AddField(t('Preis'), 'price', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
        $mf->AddField(t('Bereits gespendet'), 'price_payed', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
    }

    if (!$_GET['bugid']) {
        $mf->AddFix('date', 'NOW()');
        if ($_SERVER['SERVER_NAME'] != 'lansuite.orgapage.de') {
            $mf->AddFix('version', LANSUITE_VERSION);
        }
        $mf->AddFix('url', $_SERVER['SERVER_NAME']);
        $mf->AddFix('reporter', $auth['userid']);
        $mf->AddFix('state', '0');
    } elseif ($auth['type'] >= 2) {
        $selections = array();
        $selections['0'] = t('Neu');
        $selections['1'] = t('Bestätigt');
        $selections['2'] = t('In Bearbeitung');
        $selections['3'] = t('Reporter-Antwort erforderlich');
        $selections['4'] = t('Behoben');
        $selections['5'] = t('Aufgeschoben');
        $selections['6'] = t('Geschlossen');
        $mf->AddField(t('Status'), 'state', \LanSuite\MasterForm::IS_SELECTION, $selections);
        $mf->AddField(t('Privat') .'|'. t('Nur Admins dürfen diesen Bugeintrag lesen.'), 'private', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
    }

    $mf->AddField(t('Text'), 'text', '', \LanSuite\MasterForm::LSCODE_BIG);
    if ($_SERVER['SERVER_NAME'] == 'lansuite.orgapage.de') {
        $mf->AddField(t('Bild / Datei anhängen'), 'file', \LanSuite\MasterForm::IS_FILE_UPLOAD, 'ext_inc/bugtracker_upload/', \LanSuite\MasterForm::FIELD_OPTIONAL);
    }

    $mf->SendForm('index.php?mod=bugtracker&action=add', 'bugtracker', 'bugid', $_GET['bugid']);

    $dsp->AddBackButton('index.php?mod=bugtracker');
}
