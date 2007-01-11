<?php
$LSCurFile = __FILE__;

switch ($_GET['step']) {
  // Activate
  case 10:
    foreach ($_POST['action'] AS $key => $val) $db->query("UPDATE {$config["tables"]["boxes"]} SET active = 1 WHERE boxid = ". (int)$key);
  break;
  
  // Deactivate
  case 11:
    foreach ($_POST['action'] AS $key => $val) $db->query("UPDATE {$config["tables"]["boxes"]} SET active = 0 WHERE boxid = ". (int)$key);
  break;
  
  // Edit
  case 20:
    include_once('inc/classes/class_masterform.php');
    $mf = new masterform();

    $mf->AddField(t('Titel'), 'name');
    $mf->AddField(t('Seite'), 'place');
    $mf->AddField(t('Position'), 'pos');
    $mf->AddField(t('Aktiv'), 'active');
    $selections = array();
    $selections['0'] = t('Egal');
    $selections['1'] = t('Nur in Intranet Version');
    $selections['2'] = t('Nur in Internet Verrsion');
    $mf->AddField(t('Nur online / offline'), 'internet', IS_SELECTION, $selections, FIELD_OPTIONAL);
    $selections = array();
    $selections['0'] = t('Egal');
    $selections['1'] = t('Nur für ausgeloggte');
    $selections['2'] = t('Nur für eingeloggte');
    $mf->AddField(t('Login benötigt'), 'login', IS_SELECTION, $selections, FIELD_OPTIONAL);
    $mf->AddField(t('Modul benötigt'), 'module');
    $mf->AddField(t('Quelldatei'), 'source');
    $mf->AddField(t('Callback'), 'callback');

    $mf->SendForm('index.php?mod=boxes&step=20', 'boxes', 'boxid', $_GET['boxid']);
  break;
  
  // Delete
#  case 30:
#  break;
}

$dsp->NewContent(t('Box-Manager'), t('Hier können Sie die Anzeige und Position der Boxen verwalten'));

include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2('');

$ms2->query['from'] = "{$config["tables"]["boxes"]} AS b";
$ms2->query['default_order_by'] = 'place ASC, pos ASC, name ASC';

$ms2->AddTextSearchDropDown(t('Internet-Modus'), 'p.internet', array('' => t('Egal'), '1' => t('Nur im Intranet-Modus'), '2' => t('Nur im Internet-Modus')));
$ms2->AddTextSearchDropDown(t('Login'), 'b.login', array('' => t('Egal'), '1' => t('Nur für ausgeloggte'), '2' => t('Nur für eingeloggte')));

function PlaceName($place) {
  if ($place == 0) return t('Linke Seite');
  elseif ($place == 1) return t('Rechte Seite');
}

$ms2->AddResultField(t('Titel'), 'b.name');
$ms2->AddResultField(t('Seite'), 'b.place', 'PlaceName');
$ms2->AddResultField(t('Position'), 'b.pos');
$ms2->AddResultField(t('Aktive'), 'b.active', 'TrueFalse');
$ms2->AddResultField(t('Quelldatei'), 'b.source');

if ($auth['type'] >= 2) $ms2->AddIconField('edit', 'index.php?mod=boxes&step=20&boxid=', t('Editieren'));
#if ($auth['type'] >= 3) $ms2->AddIconField('delete', 'index.php?mod=boxes&step=30&boxid=', t('Löschen'));

$ms2->AddMultiSelectAction(t('Aktivieren'), 'index.php?mod=boxes&step=10');
$ms2->AddMultiSelectAction(t('Deaktivieren'), 'index.php?mod=boxes&step=11');

$ms2->PrintSearch('index.php?mod=boxes', 'b.boxid');
$dsp->AddContent();
?>