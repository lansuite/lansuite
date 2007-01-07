<?php
$LSCurFile = __FILE__;

function LastPostDetails($date) {
  global $db, $config, $line, $dsp, $templ;

  if ($date) {
    $row = $db->query_first("SELECT p.userid, p.pid, p.tid, u.username FROM {$config['tables']['board_posts']} AS p
      LEFT JOIN {$config['tables']['user']} AS u ON p.userid = u.userid
      WHERE p.date = $date AND p.tid = {$line['tid']}");

    $ret = '<a href="index.php?mod=board&action=thread&tid='. $row['tid'] .'&gotopid='. $row['pid'] .'#pid'. $row['pid'] .'" class="menu">'. date('d.m.y H:i', $date);
    if ($row['userid']) $ret .= '<br />'. $row['username'] .'</a> '. $dsp->FetchUserIcon($row['userid']);
    else $ret .= '<br />Gast_';
    return $ret;
     
  } else {
    $templ['ms2']['icon_name'] = 'no';
    $templ['ms2']['icon_title'] = '-';
    return $dsp->FetchModTpl('mastersearch2', 'result_icon');
  }
}

function FormatTitle($title) {
  global $dsp, $templ, $line;
  
  $icon = '';
  if ($line['closed']) {
    $templ['ms2']['icon_name'] = 'locked';
    $templ['ms2']['icon_title'] = 'Not Paid';
    $icon = $dsp->FetchModTpl('mastersearch2', 'result_icon'). ' ';
  }
  return $icon . $title;
}

function NewPosts($last_read) {
	global $db, $config, $auth, $line;

	// Delete old entries
	$db->query("DELETE FROM {$config["tables"]["board_read_state"]} WHERE last_read < ". (time() - 60 * 60 * 24 * 7));

	// Older, than one week
	if ($line['LastPost'] < (time() - 60 * 60 * 24 * 7)) return "<a class=\"menu\" href=\"index.php?mod=board&action=thread&fid={$_GET["fid"]}&tid={$line['tid']}\">Alt</a>";

	// No entry -> Thread completely new
	elseif (!$last_read) return "<a class=\"menu\" href=\"index.php?mod=board&action=thread&fid={$_GET["fid"]}&tid={$line['tid']}\">Neu</a>";

	// Entry exists
	else {

		// The posts date is newer than the mark -> New
		if ($last_read < $line['LastPost']) return "<a class=\"menu\" href=\"index.php?mod=board&action=thread&fid={$_GET["fid"]}&tid={$line['tid']}#pid{$line['last_pid']}\">Neu</a>";

		// The posts date is older than the mark -> Old
		else return "<a class=\"menu\" href=\"index.php?mod=board&action=thread&fid={$_GET["fid"]}&tid={$line['tid']}\">Alt</a>";
	}
}

if ($_GET['fid'] != '') {
  $row = $db->query_first("SELECT name, need_type FROM {$config["tables"]["board_forums"]} WHERE fid={$_GET["fid"]}");
  if ($row['need_type'] == 1 and $auth['login'] == 0) $new_thread = t('Sie müssen sich zuerst einloggen, um einen Thread in diesem Forum starten zu können');
  else $new_thread = $dsp->FetchIcon("index.php?mod=board&action=thread&fid={$vars["fid"]}", "add");

  // Board Headline
	$hyperlink = '<a href="%s" class="menu">%s</a>';
	$overview_capt = sprintf($hyperlink, "index.php?mod=board", t('Forum'));
	$dsp->NewContent($row['name'], "$overview_capt - {$row['name']}");
  $dsp->AddSingleRow($new_thread ." ". $dsp->FetchIcon("index.php?mod=board", "back"));
}

if ($_POST['search_input'][1] != '' or $_POST['search_input'][2] != '')  $dsp->AddSingleRow(t('Achtung: Sie haben als Suche einen Autor, bzw. Text angegeben. Die Ergebnis-Felder Antworten, sowie erster und letzter Beitrag beziehen sich daher nur noch auf Posts, in denen diese Eingaben gefunden wurden, nicht mehr auf den ganzen Thread!'));

include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2();

$ms2->query['from'] = "{$config['tables']['board_threads']} AS t
    LEFT JOIN {$config['tables']['board_forums']} AS f ON t.fid = f.fid
    LEFT JOIN {$config['tables']['board_posts']} AS p ON t.tid = p.tid
    LEFT JOIN {$config["tables"]["board_read_state"]} AS r ON t.tid = r.tid AND r.userid = ". (int)$auth['userid'] ."
    LEFT JOIN {$config["tables"]["user"]} AS u ON p.userid = u.userid
    LEFT JOIN {$config["tables"]["board_bookmark"]} AS b ON b.tid = t.tid AND b.userid = ". (int)$auth['userid'] ."
    ";
$ms2->query['where'] = 'f.need_type <= '. (int)($auth['type'] + 1);
if ($_GET['fid'] != '') $ms2->query['where'] .= ' AND t.fid = '. (int)$_GET['fid'];
if ($_GET['action'] == 'bookmark') $ms2->query['where'] .= ' AND b.bid IS NOT NULL';
$ms2->query['default_order_by'] = 'LastPost DESC';

if ($_GET['fid'] == '') {
  $ms2->AddTextSearchField(t('Titel'), array('t.caption' => 'like'));
  $ms2->AddTextSearchField(t('Text'), array('p.comment' => 'fulltext'));
  $ms2->AddTextSearchField(t('Autor'), array('u.username' => '1337', 'u.name' => 'like', 'u.firstname' => 'like'));

  $list = array();
  $list[''] = t('Alle');
  $res = $db->query("SELECT fid, name FROM {$config["tables"]["board_forums"]}");
  while ($row = $db->fetch_array($res)) $list[$row['fid']] = $row['name'];
  $ms2->AddTextSearchDropDown(t('Forum'), 'f.fid', $list);
  $db->free_result($res);
}

$ms2->AddSelect('t.closed');
if ($_GET['fid'] != '') $ms2->AddResultField(t('Thread'), 't.caption', 'FormatTitle');
else $ms2->AddResultField(t('Thread'), 'CONCAT(\'<b>\', f.name, \'</b><br />\', t.caption) AS ThreadName', 'FormatTitle');
$ms2->AddResultField(t('Neu'), 'r.last_read', 'NewPosts');
$ms2->AddResultField(t('Abrufe'), 't.views');
$ms2->AddResultField(t('Antworten'), '(COUNT(p.pid) - 1) AS posts');
$ms2->AddResultField(t('Erster Beitrag'), 'MIN(p.date) AS FirstPost', 'LastPostDetails');
$ms2->AddResultField(t('Letzter Beitrag'), 'MAX(p.date) AS LastPost', 'LastPostDetails');

$ms2->AddIconField('details', 'index.php?mod=board&action=thread&fid='. $_GET["fid"] .'&tid=', t('Details'));
if ($auth['type'] >= 3) $ms2->AddIconField('delete', 'index.php?mod=board&action=delete&step=11&tid=', t('Löschen'));
$ms2->PrintSearch('index.php?mod=board&action='. $_GET['action'] .'&fid='. $_GET['fid'], 't.tid');

if ($_GET['fid'] != '') $dsp->AddSingleRow($new_thread ." ". $dsp->FetchIcon("index.php?mod=board", "back"));
$dsp->AddContent();

// Generate Boardlist-Dropdown
$foren_liste = $db->query("SELECT fid, name FROM {$config["tables"]["board_forums"]} WHERE need_type <= ". (int)($auth['type'] + 1));
while ($forum = $db->fetch_array($foren_liste))
  $templ['board']['thread']['case']['control']['goto'] .= "<option value=\"index.php?mod=board&action=forum&fid={$forum["fid"]}\">{$forum["name"]}</option>";
$templ['board']['forum']['case']['info']['forum_choise'] = t('Bitte auswählen');
$dsp->AddDoubleRow(t('Gehe zu Forum'), $dsp->FetchModTpl('board', 'forum_dropdown'));
$dsp->AddContent();

?>