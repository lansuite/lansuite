<?php
$dsp->NewContent(t('Bugtracker'), t('Hier können Sie Fehler melden, die bei der Verwendung dieses Systems auftreten, sowie Feature Wünsche äußern. Können die Admins dieser Webseite sie nicht selbst beheben, haben diese die Möglichkeit sie an das Lansuite-Team weiterzureichen.'));

$row = $db->qry_first('SELECT reporter FROM %prefix%bugtracker WHERE bugid = %int%', $_GET['bugid']);
if ($_GET['bugid'] and $auth['type'] < 2 and $row['reporter'] != $auth['userid']) $func->error(t('Nur Admins und der Reporter dürfen Bug-Einträge im Nachhinein editieren'), 'index.php?mod=bugtracker');
else {
  include_once('inc/classes/class_masterform.php');
  $mf = new masterform();

  $mf->AddField(t('Überschrift'), 'caption');

  $selections = array();
  $selections[''] = t('Bitte auswählen');
  $selections['1'] = t('Feature Wunsch');
  $selections['2'] = t('Schreibfehler');
  $selections['3'] = t('Kleiner Fehler');
  $selections['4'] = t('Schwerer Fehler');
  $selections['5'] = t('Absturz');
  $mf->AddField(t('Typ'), 'type', IS_SELECTION, $selections);

  $selections = array();
  $selections[''] = t('Nicht Modul-spezifisch');
  $res = $db->query("SELECT name FROM {$config['tables']['modules']}");
  while ($row = $db->fetch_array($res)) $selections[$row['name']] = $row['name'];
  $db->free_result($res);
  $mf->AddField(t('Betrifft Modul'), 'module', IS_SELECTION, $selections, FIELD_OPTIONAL);

  if ($_SERVER['SERVER_NAME'] == 'lansuite.orgapage.de') $mf->AddField(t('Betrifft Version'), 'version');

  $selections = array();
  for ($z = 5; $z >= -5; $z--) {
    $selections[$z] = $z;
    if ($z == 5) $selections[$z] .= ' ('. t('Sehr hoch') .')';
    if ($z == -5) $selections[$z] .= ' ('. t('Sehr gering') .')';
  }
  $mf->AddField(t('Priorität'), 'priority', IS_SELECTION, $selections, FIELD_OPTIONAL);

  // Assign bug
  if ($auth['type'] >= 2) {
    $selections = array();
    $selections['0'] = t('Keinem zugeordnet');
    $res = $db->query("SELECT userid, username FROM {$config['tables']['user']} WHERE type >= 2");
    while ($row = $db->fetch_array($res)) $selections[$row['userid']] = $row['username'];
    $db->free_result($res);
    $mf->AddField(t('Bearbeiter'), 'agent', IS_SELECTION, $selections, FIELD_OPTIONAL);
  }

  if (!$_GET['bugid']) {
    $mf->AddFix('date', 'NOW()');
    if ($_SERVER['SERVER_NAME'] != 'lansuite.orgapage.de') $mf->AddFix('version', $config['lansuite']['version']);
    $mf->AddFix('url', $_SERVER['SERVER_NAME']);
    $mf->AddFix('reporter', $auth['userid']);
    $mf->AddFix('state', 0);
  } else {
    $selections = array();
    $selections['0'] = t('Neu');
    $selections['1'] = t('Bestätigt');
    $selections['2'] = t('In Bearbeitung');
    $selections['3'] = t('Reporter-Antwort erforderlich');
    $selections['4'] = t('Behoben');
    $selections['5'] = t('Aufgeschoben');
    $selections['6'] = t('Geschlossen');
    $mf->AddField(t('Status'), 'state', IS_SELECTION, $selections);
  }

  $mf->AddField(t('Text'), 'text', '', LSCODE_BIG);
  if ($_SERVER['SERVER_NAME'] == 'lansuite.orgapage.de') $mf->AddField(t('Bild / Datei anhängen'), 'file', IS_FILE_UPLOAD, 'ext_inc/bugtracker_upload/', FIELD_OPTIONAL);

  $mf->SendForm('index.php?mod=bugtracker&action=add', 'bugtracker', 'bugid', $_GET['bugid']);

  $dsp->AddBackButton('index.php?mod=bugtracker');
}
?>