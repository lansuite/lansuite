<?php

$ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2();

$ms2->query['from'] = "%prefix%board_forums AS f
    LEFT JOIN %prefix%board_threads AS t ON f.fid = t.fid
    LEFT JOIN %prefix%board_posts AS p ON t.tid = p.tid";
$ms2->query['where'] = 'f.need_type <= '. ((int)($auth['type'] + 1) .' AND (!f.need_group OR f.need_group = '. ((int)$auth['group_id']) .')');
$ms2->query['default_order_by'] = 'f.board_group, f.pos';

$ms2->AddSelect('f.description');
$ms2->AddSelect('f.board_group');
$ms2->AddResultField(t('Forum'), 'f.name', 'NameAndDesc');
$ms2->AddResultField(t('Beiträge'), 'COUNT(p.pid) AS posts');
$ms2->AddResultField(t('Letzter Beitrag'), 'UNIX_TIMESTAMP(MAX(p.date)) AS LastPost', 'LastPostDetailsShow');

$ms2->AddIconField('details', 'index.php?mod=board&action=forum&fid=', t('Details'));
if ($auth['type'] >= 2) {
    $ms2->AddIconField('edit', 'index.php?mod=board&action=add&var=change&fid=', t('Editieren'));
}
if ($auth['type'] >= 3) {
    $ms2->AddIconField('delete', 'index.php?mod=board&action=delete&step=2&fid=', t('Löschen'));
}
$ms2->PrintSearch('index.php?mod=board', 'f.fid');

// Statistics
$total_threads = $db->qry_first("SELECT COUNT(tid) as threads FROM %prefix%board_threads");
$total_posts = $db->qry_first("SELECT COUNT(pid) as posts FROM %prefix%board_posts");

$info_line = t('Es wurden bereits %1 Beiträge in %2 Threads geschrieben', array($total_posts['posts'], $total_threads['threads'])) .HTML_NEWLINE.
  '<a href="index.php?mod=board&action=forum&fid=&order_by=LastPost&order_dir=DESC">'. t('Die neusten Beiträge anzeigen') .'</a>';
if ($auth['login']) {
    $info_line .= HTML_NEWLINE . '<a href="index.php?mod=board&action=forum&fid=&search_input[2]='. $auth['username'] .'&order_by=LastPost&order_dir=DESC">'. t('Threads, in denen ich mitgeschrieben habe, anzeigen') .'</a>';
}
$dsp->AddSingleRow($info_line);
