<?php

function LastPostDetails($date, $jump_to_pid = 'last') {
  global $db, $config, $line, $dsp;

  $row = $db->query_first("SELECT p.userid, p.pid, p.tid, u.username FROM {$config['tables']['board_posts']} AS p
    LEFT JOIN {$config['tables']['user']} AS u ON p.userid = u.userid
    WHERE p.date = $date AND p.tid = {$line['tid']}");

  return '<a href="index.php?mod=board&action=thread&tid='. $row['tid'] .'&pid='. $jump_to_pid .'#pid'. $row['pid'] .'" class="menu">'. date('d.m.y H:i', $date) .'<br />'. $row['username'] .'</a> '. $dsp->FetchUserIcon($row['userid']);
}

function FirstPostDetails($date) {
  return LastPostDetails($date, '');
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
  $row = $db->query_first("SELECT need_type FROM {$config["tables"]["board_forums"]} WHERE fid={$_GET["fid"]}");
  if ($row['need_type'] == 1 and $auth['login'] == 0) $new_thread = $lang['board']['only_loggedin_post'];
  else $new_thread = $dsp->FetchButton("index.php?mod=board&action=post&fid={$vars["fid"]}", "new_thread");

  $dsp->AddSingleRow($new_thread ." ". $dsp->FetchButton("index.php?mod=board", "back"));
}

if ($_POST['search_input'][1] != '' or $_POST['search_input'][2] != '')  $dsp->AddSingleRow($lang['board']['search_hint']);

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
$ms2->query['default_order_by'] = 'MAX(p.date) DESC';

$ms2->AddTextSearchField($lang['board']['subject'], array('t.caption' => 'like'));
$ms2->AddTextSearchField($lang['board']['thread_text'], array('p.comment' => 'fulltext'));
$ms2->AddTextSearchField($lang['board']['author'], array('u.username' => '1337', 'u.name' => 'like', 'u.firstname' => 'like'));

if ($_GET['fid'] != '') $ms2->AddResultField($lang['board']['subject'], 't.caption');
else $ms2->AddResultField($lang['board']['subject'], 'CONCAT(\'<b>\', f.name, \'</b><br />\', t.caption) AS ThreadName');
$ms2->AddResultField($lang['board']['new'], 'r.last_read', 'NewPosts');
$ms2->AddResultField($lang['board']['clicks'], 't.views');
$ms2->AddResultField($lang['board']['replys'], '(COUNT(p.pid) - 1) AS posts');
$ms2->AddResultField($lang['board']['first_post'], 'MIN(p.date) AS FirstPost', 'FirstPostDetails');
$ms2->AddResultField($lang['board']['last_post'], 'MAX(p.date) AS LastPost', 'LastPostDetails');

$ms2->AddIconField('details', 'index.php?mod=board&action=thread&fid='. $_GET["fid"] .'&tid=', $lang['ms2']['details']);
$ms2->AddIconField('send_mail', 'index.php?mod=board&action=post&fid='. $_GET["fid"] .'&tid=', $lang['ms2']['reply']);
if ($auth['type'] >= 3) $ms2->AddIconField('delete', 'index.php?mod=board&action=edit&mode=delete&tid=', $lang['ms2']['delete']);
$ms2->PrintSearch('index.php?mod=board&action='. $_GET['action'] .'&fid='. $_GET['fid'], 't.tid');

if ($_GET['fid'] != '') $dsp->AddSingleRow($new_thread ." ". $dsp->FetchButton("index.php?mod=board", "back"));
$dsp->AddContent();
?>