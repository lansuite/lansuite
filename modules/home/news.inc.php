<?php

$smarty->assign('caption', t('Aktuelle News') . ' <span class="small">[<a href="ext_inc/newsfeed/news.xml" class="menu" title="XML Newsfeed">RSS</a>]</span>');
$content = '';

$query = $db->qry("SELECT n.newsid, n.caption, n.priority, UNIX_TIMESTAMP(n.changedate) AS changedate, COUNT(c.relatedto_id) AS comments FROM %prefix%news AS n
  LEFT JOIN %prefix%comments AS c ON (c.relatedto_id = n.newsid AND (c.relatedto_item = 'news' OR c.relatedto_item IS NULL))
  GROUP BY n.newsid
  ORDER BY n.top DESC, n.date DESC
  LIMIT 0,%int%
  ", $cfg['home_item_count']);

if ($db->num_rows($query) > 0) while ($row = $db->fetch_array($query)) {
  $smarty->assign('link', "index.php?mod=news&action=comment&newsid={$row["newsid"]}");
  if ($cfg['news_comments_allowed'])
  	$smarty->assign('text', $func->CutString($row["caption"], 40) .' ['.$row['comments'].']');
  else
    $smarty->assign('text', $func->CutString($row["caption"], 40));
  if ($row["priority"] == 1)   $smarty->assign('text2', "<strong>!!!</strong>");
  else $smarty->assign('text2', '');

  if ($func->CheckNewPosts($row['changedate'], 'news', $row['newsid'])) $content .= $smarty->fetch('modules/home/templates/show_row_new.htm');
  else $content .= $smarty->fetch('modules/home/templates/show_row.htm');
}
else $content = "<i>". t('Keine News bisher vorhanden') ."</i>";
?>