<?php

$smarty->assign('caption', t('Aktuelles im Board') . ' <span class="small">[<a href="index.php?mod=board&action=forum&fid=&order_by=LastPost&order_dir=DESC" title="Neusten Beiträge">mehr</a>]</span>');
$content = "";

$authtyp = $auth['type'] + 1;
$query = $db->qry("SELECT t.tid, MAX(p.pid) AS pid, t.caption, UNIX_TIMESTAMP(MAX(p.date)) AS LastPost, (COUNT(p.pid) - 1) AS posts, t.closed
	FROM %prefix%board_threads AS t
	LEFT JOIN %prefix%board_forums AS f ON t.fid = f.fid
	LEFT JOIN %prefix%board_posts AS p ON p.tid = t.tid
	WHERE (f.need_type <= %int% AND (!f.need_group OR f.need_group = %int%))
	GROUP BY t.tid
	ORDER BY LastPost DESC
	LIMIT 0, %int%", $authtyp, $auth['group_id'], $cfg['home_item_cnt_board']);

if ($db->num_rows($query) > 0) {
    while ($row = $db->fetch_array($query)) {
        $page = floor(($row['posts']) / $cfg['board_max_posts']);
        $smarty->assign('link', "index.php?mod=board&action=thread&tid={$row['tid']}&posts_page={$page}#pid{$row['pid']}");

        $text = $func->CutString($row['caption'], 40);
        $smarty->assign('text', $text);
  
        $text2 = ' ['. $row['posts'] .']';
        if ($row['closed']) {
            $text2 .= ' <div class="infolink" style="display:inline"><img src="design/images/icon_locked.png" border="0" width="12" alt="Closed" /><span class="infobox">'. t('Thread wurde geschlossen') .'</span></div>';
        }
        $smarty->assign('text2', $text2);

        if ($func->CheckNewPosts($row['LastPost'], 'board', $row['tid'])) {
            $content .= $smarty->fetch('modules/home/templates/show_row_new.htm');
        } else {
            $content .= $smarty->fetch('modules/home/templates/show_row.htm');
        }
    }
} else {
    $content = "<i>". t('Keine Beiträge vorhanden') ."</i>";
}
