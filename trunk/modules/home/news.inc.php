<?php

$smarty->assign('caption', t('Aktuelle News') . ' <span class="small">[<a href="ext_inc/newsfeed/news.xml" class="menu" title="XML Newsfeed">RSS</a>]</span>');
$content = '';

$query = $db->qry("SELECT n.newsid, n.caption, n.priority, UNIX_TIMESTAMP(n.changedate) AS changedate, COUNT(c.relatedto_id) AS comments FROM %prefix%news AS n
  LEFT JOIN %prefix%comments AS c ON (c.relatedto_id = n.newsid AND (c.relatedto_item = 'news' OR c.relatedto_item IS NULL))
  GROUP BY n.newsid
  ORDER BY n.top DESC, n.date DESC
  LIMIT 0,%int%
  ", $cfg['home_item_count']);

if ($db->num_rows($query) > 0) {
	while ($row = $db->fetch_array($query)) {

    $newsid 	= $row["newsid"];
    $caption	= $row["caption"];
    $prio		= $row["priority"];

    $templ['home']['show']['row']['control']['link']	= "index.php?mod=news&action=comment&newsid=$newsid";
    $templ['home']['show']['row']['info']['text']		= $func->CutString($caption, 40) .' ['.$row['comments'].']';

    if ($prio == 1) $templ['home']['show']['row']['info']['text2']		= "<strong>!!!</strong>";

    if ($func->CheckNewPosts($row['changedate'], 'news', $row['newsid'])) $content	.= $dsp->FetchModTpl('home', 'show_row_new');
    else $content	.= $dsp->FetchModTpl('home', 'show_row');

		$templ['home']['show']['row']['info']['text'] = '';
		$templ['home']['show']['row']['info']['text2'] = '';
	}
}
else $content = "<i>". t('Keine News bisher vorhanden') ."</i>";
?>