<?php

$smarty->assign('caption', t('Neue Wiki Einträge'));
$content = "";

$query = $db->qry('
  SELECT
    w.postid,
    w.name,
    MAX(UNIX_TIMESTAMP(v.date)) AS date,
    COUNT(*) AS refCount
  FROM %prefix%wiki_versions AS v
  LEFT JOIN %prefix%wiki AS w ON w.postid = v.postid
  GROUP BY v.postid
  ORDER BY date DESC
  LIMIT 0, %int%', $cfg['home_item_cnt_wiki']);

if ($db->num_rows($query) > 0) {
    while ($row = $db->fetch_array($query)) {
        $smarty->assign('link', 'index.php?mod=wiki&action=show&name='. urlencode($row['name']));
        $smarty->assign('text', $func->CutString($row['name'], 40));
        $smarty->assign('text2', ' ['. $row['refCount'] .']');
        if ($func->CheckNewPosts($row['date'], 'wiki', $row['postid'])) {
            $content .= $smarty->fetch('modules/home/templates/show_row_new.htm');
        } else {
            $content .= $smarty->fetch('modules/home/templates/show_row.htm');
        }
    }
} else {
    $content = "<i>". t('Keine Einträge vorhanden') ."</i>";
}
