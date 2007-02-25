<?php
$LSCurFile = __FILE__;

include_once('modules/bugtracker/class_bugtracker.php');
$bugtracker = new Bugtracker();

$types = array();
$types['1'] = t('Feature Wunsch');
$types['2'] = t('Schreibfehler');
$types['3'] = t('Kleiner Fehler');
$types['4'] = t('Schwerer Fehler');
$types['5'] = t('Absturz');

$colors = array();
$colors[0] = '#bc851b';
$colors[1] = '#dc5656';
$colors[2] = '#e19501';
$colors[3] = '#019ae1';
$colors[4] = '#67a900';
$colors[5] = '#aaaaaa';
$colors[6] = '#999999';

if ($_POST['action']) foreach ($_POST['action'] as $key => $val) {

  if ($auth['login']) {
    // Change state
    if ($_GET['state'] != '' and $_GET['state'] < 2) $bugtracker->SetBugState($key, $_GET['state']);
  }
  if ($auth['type'] >= 2) {
    // Change state
    if ($_GET['state'] != '' and $_GET['state'] >= 2) $bugtracker->SetBugState($key, $_GET['state']);

    // Assign to new user
    if ($_GET['userid'] != '') $bugtracker->AssignBugToUser($key, $_GET['userid']);
  }
}

if ($_GET['action'] == 'delete' and $auth['type'] >= 2) {
  if ($_GET['bugid'] != '') {
    include_once('inc/classes/class_masterdelete.php');
    $md = new masterdelete();
    $md->Delete('bugtracker', 'bugid', $_GET['bugid']);
  } else {
    include_once('inc/classes/class_masterdelete.php');
    $md = new masterdelete();
    $md->MultiDelete('bugtracker', 'bugid');
  }
}

function FetchState($state) {
  global $bugtracker;
  return $bugtracker->stati[$state];
}

function FetchType($type) {
  global $types;
  return $types[$type];
}

if (!$_GET['bugid'] or $_GET['action'] == 'delete') {
  $dsp->NewContent(t('Bugtracker'), t('Hier können Sie Fehler melden, die bei der Verwendung dieses Systems auftreten, sowie Feature Wünsche äußern. Können die Admins dieser Webseite sie nicht selbst beheben, haben diese die Möglichkeit sie an das Lansuite-Team weiterzureichen.'));

  include_once('modules/mastersearch2/class_mastersearch2.php');
  $ms2 = new mastersearch2('bugtracker');

  $ms2->query['from'] = "{$config["tables"]["bugtracker"]} AS b
    LEFT JOIN {$config["tables"]["user"]} AS r ON b.reporter = r.userid
    LEFT JOIN {$config["tables"]["user"]} AS a ON b.agent = a.userid
    LEFT JOIN {$config["tables"]["comments"]} AS c ON (c.relatedto_id = b.bugid AND c.relatedto_item = 'BugEintrag')
    ";
  $ms2->query['default_order_by'] = 'state ASC, date DESC';
  $ms2->config['EntriesPerPage'] = 50;

  $ms2->AddBGColor('state', $colors);

  $ms2->AddTextSearchField(t('Überschrift'), array('b.caption' => 'like'));
  $ms2->AddTextSearchField(t('Text'), array('b.text' => 'fulltext'));

  $ms2->AddResultField(t('Titel'), 'b.caption');
  $ms2->AddSelect('r.userid');
  $ms2->AddResultField(t('Typ'), 'b.type', 'FetchType');
  $ms2->AddResultField(t('Prio.'), 'b.priority');
  $ms2->AddResultField(t('Status'), 'b.state', 'FetchState');
  $ms2->AddResultField(t('Reporter'), 'r.username AS reporter', 'UserNameAndIcon');
  $ms2->AddResultField(t('Bearbeiter'), 'a.username AS agent');
  $ms2->AddResultField(t('Antw.'), 'COUNT(c.relatedto_id) AS comments');
  $ms2->AddResultField(t('Datum'), 'UNIX_TIMESTAMP(b.date) AS date', 'MS2GetDate');

  $ms2->AddIconField('details', 'index.php?mod=bugtracker&bugid=', t('Details'));
  if ($auth['type'] >= 2) $ms2->AddIconField('edit', 'index.php?mod=bugtracker&action=add&bugid=', t('Editieren'));
  if ($auth['type'] >= 3) $ms2->AddIconField('delete', 'index.php?mod=bugtracker&action=delete&bugid=', t('Löschen'));

  if ($auth['login']) {
    foreach($bugtracker->stati as $key => $val) if ($key < 2) $ms2->AddMultiSelectAction(t('Status') .' -> '. $val, 'index.php?mod=bugtracker&state='. $key);
  }
  if ($auth['type'] >= 2) {
    foreach($bugtracker->stati as $key => $val) if ($key >= 2) $ms2->AddMultiSelectAction(t('Status') .' -> '. $val, 'index.php?mod=bugtracker&state='. $key);

    $ms2->AddMultiSelectAction(t('Bearbeiter löschen'), 'index.php?mod=bugtracker&userid=0');
    $res = $db->query("SELECT userid, username FROM {$config['tables']['user']} WHERE type >= 2");
    while ($row = $db->fetch_array($res)) $ms2->AddMultiSelectAction(t('Bearbeiter') .' -> '. $row['username'], 'index.php?mod=bugtracker&userid='. $row['userid']);
    $db->free_result($res);

    $ms2->AddMultiSelectAction(t('Löschen'), 'index.php?mod=bugtracker&action=delete');
  }

  $ms2->PrintSearch('index.php?mod=bugtracker', 'b.bugid');

// Details page
} else {
  $row = $db->query_first("SELECT b.*, r.username AS reporter_name, a.username AS agent_name FROM {$config['tables']['bugtracker']} AS b
    LEFT JOIN {$config["tables"]["user"]} AS r ON b.reporter = r.userid
    LEFT JOIN {$config["tables"]["user"]} AS a ON b.agent = a.userid
    WHERE bugid=". (int)$_GET['bugid']
    );

  $dsp->NewContent($row['caption'], $types[$row['type']] .', '. t('Priorität') .': '. $row['priority']);

	$dsp->AddDoubleRow(t('Herkunft'), '<a href="'. $row['url'] .'" target="_blank">'. $row['url'] .'</a> Version('. $row['version'] .')');
	$dsp->AddDoubleRow(t('Reporter'), $row['reporter_name'] .' '. $dsp->FetchUserIcon($row['reporter']));
	$dsp->AddDoubleRow(t('Betrifft Modul'), $row['module']);
	$dsp->AddDoubleRow(t('Meldezeitpunkt'), $row['date']);

	$dsp->AddDoubleRow(t('Status'), $bugtracker->stati[$row['state']]);
	if ($row['agent']) $dsp->AddDoubleRow(t('Bearbeiter'), $row['agent_name'] .' '. $dsp->FetchUserIcon($row['agent']));
	else $dsp->AddDoubleRow(t('Bearbeiter'), t('Noch nicht zugeordnet'));

	$dsp->AddDoubleRow(t('Text'), $func->text2html($row['text']));
  if ($row['file']) {
    $FileEnding = strtolower(substr($row['file'], strrpos($row['file'], '.'), 5));
    if ($FileEnding == '.png' or $FileEnding == '.gif' or $FileEnding == '.jpg' or $FileEnding == '.jpeg') $attachment = '<img src="'. $row['file'] .'" />';
    else $templ['board']['thread']['case']['info']['post']['text'] .= $attachment = $dsp->FetchIcon($row['file'], 'download') .' ('. t('Angehängte Datei herunterladen').')';
    $dsp->AddDoubleRow(t('Anhang'), $attachment);
  }
	$dsp->AddDoubleRow('', $dsp->FetchButton('index.php?mod=bugtracker&action=add&bugid='.$row['bugid'], 'edit') . $dsp->FetchButton('index.php?mod=bugtracker', 'back'));

  if ($auth['type'] >= 2) {
    include_once('inc/classes/class_masterform.php');
    $mf = new masterform();
    $mf->ManualUpdate = 1;
    $mf->AddField(t('Status'), 'state', IS_SELECTION, $bugtracker->stati);

    if ($mf->SendForm('', 'bugtracker', 'bugid', $_GET['bugid'])) {
      $bugtracker->SetBugState($_GET['bugid'], $_POST['state']);
      $func->confirmation(t('Geändert'));
    }
  }

	include('inc/classes/class_mastercomment.php');
	new Mastercomment('BugEintrag', $_GET['bugid']);
	
	$dsp->AddFieldsetStart('Log');
  include_once('modules/mastersearch2/class_mastersearch2.php');
  $ms2 = new mastersearch2('bugtracker');

  $ms2->query['from'] = "{$config["tables"]["log"]} AS l LEFT JOIN {$config["tables"]["user"]} AS u ON l.userid = u.userid";
  $ms2->query['where'] = "(sort_tag = 'bugtracker' AND target_id = ". (int)$_GET['bugid'] .')';

  $ms2->AddResultField(t('Nachricht'), 'l.description');
  $ms2->AddSelect('u.userid');
  $ms2->AddResultField(t('Auslöser'), 'u.username', 'UserNameAndIcon');
  $ms2->AddResultField(t('Datum'), 'l.date', 'MS2GetDate');
  $ms2->PrintSearch('index.php?mod=bugtracker', 'logid');
	$dsp->AddFieldsetEnd('Log');
}

$dsp->AddContent();
?>