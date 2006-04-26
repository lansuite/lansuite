<?php

function NameAndDesc($name) {
  global $line, $auth;

  return '<img src="/design/'. $auth['design'] .'/images/arrows_forum.gif" hspace="3" align="left" border="0"><b>'. $name .'</b><br />' .$line['description'];
}

function LastPostDetails($date) {
  global $db, $config, $line, $dsp;

  if ($date) {
    $row = $db->query_first("SELECT t.caption, p.userid, p.tid, p.pid FROM {$config['tables']['board_posts']} AS p
      LEFT JOIN {$config['tables']['board_threads']} AS t ON p.tid = t.tid
      WHERE p.date = $date AND t.fid = {$line['fid']}");

    if (strlen($row['caption']) > 18) $row['caption'] = substr($row['caption'], 0, 16). '...';
    return '<a href="index.php?mod=board&action=thread&tid='. $row['tid'] .'&pid=last#pid'. $row['pid'] .'" class="menu">'. $row['caption'] .'<br />'. date('d.m.y H:i', $date) .'</a> '. $dsp->FetchUserIcon($row['userid']);
  } else {
    $templ['ms2']['icon_name'] = 'no';
    $templ['ms2']['icon_title'] = '-';
    return $dsp->FetchModTpl('mastersearch2', 'result_icon');
  }
}

include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2();

$ms2->query['from'] = "{$config['tables']['board_forums']} AS f
    LEFT JOIN {$config['tables']['board_threads']} AS t ON f.fid = t.fid
    LEFT JOIN {$config['tables']['board_posts']} AS p ON t.tid = p.tid";
$ms2->query['where'] = 'f.need_type <= '. (int)($auth['type'] + 1);

$ms2->AddSelect('f.description');
$ms2->AddResultField($lang['board']['board'], 'f.name', 'NameAndDesc');
$ms2->AddResultField($lang['board']['posts'], 'COUNT(p.pid) AS posts');
$ms2->AddResultField($lang['board']['last_post'], 'MAX(p.date) AS LastPost', 'LastPostDetails');

$ms2->AddIconField('details', 'index.php?mod=board&action=forum&fid=', $lang['ms2']['details']);
if ($auth['type'] >= 2) $ms2->AddIconField('edit', 'index.php?mod=board&action=add&var=change&fid=', $lang['ms2']['edit']);
if ($auth['type'] >= 3) $ms2->AddIconField('delete', 'index.php?mod=board&action=delete&step=2&fid=', $lang['ms2']['delete']);
$ms2->PrintSearch('index.php?mod=board', 'f.fid');


// Statistics
$total_threads = $db->query_first("SELECT COUNT(tid) as threads FROM {$config['tables']['board_threads']}");
$dsp->AddSingleRow($lang['board']['topics_total'] .' '. $total_threads['threads']);
 
$total_posts = $db->query_first("SELECT COUNT(pid) as posts FROM {$config['tables']['board_posts']}");
$dsp->AddSingleRow($lang['board']['posts_total'] .' '. $total_posts['posts']);

$dsp->AddContent();
?>