<?php

function Update($id) {
  global $db;

  if ($_POST['poll_reset'] or !$_GET['pollid']) {
			$db->qry('DELETE FROM %prefix%polloptions WHERE pollid = %int%', $id);
			$db->qry('DELETE FROM %prefix%pollvotes WHERE pollid = %int%', $id);
      if ($_POST['poll_option']) foreach ($_POST['poll_option'] as $key => $val) {
      if (trim($val) != '') $db->qry('INSERT INTO %prefix%polloptions SET caption = %string%, pollid = %int%', $val, $id);
    }
  }
  
  return true;
}

$dsp->NewContent(t('Poll hinzufügen / ändern'), t('Um den Poll hinzuzufügen / zu ändern, füllen Sie bitte das folgende Formular vollständig aus.'));

include_once('inc/classes/class_masterform.php');
$mf = new masterform();

$mf->AddField(t('Name'), 'caption');
$mf->AddField(t('Bemerkung'), 'comment', '', LSCODE_ALLOWED, FIELD_OPTIONAL);
$mf->AddField(t('Anonym'), 'anonym', '', '', FIELD_OPTIONAL);
$mf->AddField(t('Mehrfachauswahl möglich'), 'multi', '', '', FIELD_OPTIONAL);
$mf->AddField(t('Zeitlich begrenzen'), 'endtime', '', '', FIELD_OPTIONAL);

$selections = array();
$selections[''] = t('Keine bestimmte Gruppe');
$res = $db->qry('SELECT group_id, group_name FROM %prefix%party_usergroups');
while ($row = $db->fetch_array($res)) {
  $selections[$row['group_id']] = $row['group_name'];
}
$db->free_result($res);
$mf->AddField(t('Benutzergruppe'), 'group_id', IS_SELECTION, $selections, FIELD_OPTIONAL);

// Poll Options
if ($_POST['poll_option']) foreach ($_POST['poll_option'] as $key => $val) $_POST["poll_option[$key]"] = $val;
elseif ($_GET['pollid'])  {
  $res = $db->qry('SELECT caption FROM %prefix%polloptions WHERE pollid = %int% ORDER BY polloptionid', $_GET['pollid']);
  for ($z = 1; $row = $db->fetch_array($res); $z++) if (!$_POST["poll_option[$z]"]) $_POST["poll_option[$z]"] = $row['caption'];
  $db->free_result($res);
}
if ($_GET['pollid']) $mf->AddField(t('Polloptionen ändern') .'|'. t('Achtung: Dies führt dazu, dass die Abstimmung zurückgesetzt wird!'), 'poll_reset', 'tinyint(1)', '', FIELD_OPTIONAL, '', 10);
for ($z = 1; $z <= 10; $z++) {
  ($z <= 2)? $optional = 0 : $optional = FIELD_OPTIONAL;
  $mf->AddField(t('Option') ." $z", "poll_option[$z]", 'varchar(80)', '', $optional);
}

$mf->AdditionalDBUpdateFunction = 'Update';
$mf->SendForm('index.php?mod=poll&action=change&step=2&pollid='. $_GET['pollid'], 'polls', 'pollid', $_GET['pollid']);
?>