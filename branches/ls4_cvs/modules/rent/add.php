<?php
$dsp->NewContent(t('Verleih'), t('Neuen Artikel zum Verleih eintragen'));

include_once('inc/classes/class_masterform.php');
$mf = new masterform();

$mf->AddField(t('Bezeichnung'), 'caption');
$mf->AddField(t('Beschreibung'), 'comment', '', '', FIELD_OPTIONAL);
$mf->AddField(t('Menge'), 'quantity');

$selections = array();
$selections['0'] = t('Keinem zugeordnet');
$res = $db->query("SELECT userid, username FROM {$config['tables']['user']} WHERE type >= 2");
while ($row = $db->fetch_array($res)) $selections[$row['userid']] = $row['username'];
$db->free_result($res);
$mf->AddField(t('Besitzer'), 'ownerid', IS_SELECTION, $selections, FIELD_OPTIONAL);

$mf->SendForm('index.php?mod=rent&action=add', 'rentstuff', 'stuffid', $_GET['stuffid']);

$dsp->AddBackButton('index.php?mod=rent');
$dsp->AddContent();
?>