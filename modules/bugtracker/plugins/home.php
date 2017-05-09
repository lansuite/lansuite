<?php

$smarty->assign('caption', t('Neue Bugs und Feature Wünsche'));
$content = "";

$query = $db->qry("SELECT b.*, MAX(UNIX_TIMESTAMP(b.changedate)) AS changedate, COUNT(c.relatedto_id) AS comments FROM %prefix%bugtracker AS b
  LEFT JOIN %prefix%comments AS c ON (c.relatedto_id = b.bugid AND c.relatedto_item = 'BugEintrag')
  WHERE b.state <= 3 AND (!private OR ". (int)$auth['type'] ." >= 2)
  GROUP BY b.bugid
  ORDER BY changedate DESC
  LIMIT 0, %int%
  ", $cfg['home_item_cnt_bugtracker']);

if ($db->num_rows($query) > 0) {
    while ($row = $db->fetch_array($query)) {
        $smarty->assign('link', "index.php?mod=bugtracker&bugid={$row['bugid']}");
        $smarty->assign('text', $func->CutString($row['caption'], 40));
        $smarty->assign('text2', ' ['. $row['comments'] .']');
        if ($func->CheckNewPosts($row['changedate'], 'bugtracker', $row['bugid'])) {
            $content .= $smarty->fetch('modules/home/templates/show_row_new.htm');
        } else {
            $content .= $smarty->fetch('modules/home/templates/show_row.htm');
        }
    }
} else {
    $content = "<i>". t('Keine Einträge vorhanden') ."</i>";
}
