<?php

$smarty->assign('caption', t('Aktuelles im Board') . ' <span class="small">[<a href="index.php?mod=board&action=forum&fid=&order_by=LastPost&order_dir=DESC" title="Neusten Beiträge">mehr</a>]</span>');
$content = "";

$authtyp = $auth['type'] + 1;
$query = $db->qry("SELECT f.fid, t.tid, MAX(p.pid) AS pid, t.caption, MAX(p.date) AS LastPost, (COUNT(p.pid) - 1) AS posts, t.closed
	FROM %prefix%board_threads AS t
	LEFT JOIN %prefix%board_forums AS f ON t.fid = f.fid
	LEFT JOIN %prefix%board_posts AS p ON p.tid = t.tid
	WHERE (f.need_type <= %int% AND (!f.need_group OR f.need_group = %int%))
	GROUP BY t.tid
	ORDER BY LastPost DESC
	LIMIT 0, %int%", $authtyp, $auth['group_id'], $cfg['home_item_count']);

if ($db->num_rows($query) > 0) while($row = $db->fetch_array($query)) {
  $templ['home']['show']['row']['control']['link']	= "index.php?mod=board&action=thread&fid={$row['fid']}&tid={$row['tid']}&gotopid={$row['pid']}#pid{$row['pid']}";

  $templ['home']['show']['row']['info']['text']		= $func->CutString($row['caption'], 40) .' ['. $row['posts'] .']';
  if ($row['closed']) $templ['home']['show']['row']['info']['text'] .= ' <div class="infolink" style="display:inline"><img src="design/images/icon_locked.png" border="0" width="12" /><span class="infobox">'. t('Thread wurde geschlossen') .'</span></div>';
  
  if ($func->CheckNewPosts($row['LastPost'], 'board', $row['tid'])) $content .= $dsp->FetchModTpl('home', 'show_row_new');
  else $content .= $dsp->FetchModTpl('home', 'show_row');
} else $content = "<i>". t('Keine Beiträge vorhanden') ."</i>";
?>