<?php

$smarty->assign('caption', t('Aktuelle Umfragen'));
$content = "";

$query = $db->qry('
  SELECT
    UNIX_TIMESTAMP(p.endtime) AS endtime,
    p.pollid,
    p.caption,
    COUNT(v.polloptionid) AS votes,
    MAX(UNIX_TIMESTAMP(p.changedate)) AS changedate
  FROM %prefix%polls AS p
  LEFT JOIN %prefix%polloptions AS o on p.pollid = o.pollid
  LEFT JOIN %prefix%pollvotes AS v on o.polloptionid = v.polloptionid
  WHERE
    (
      !p.group_id
      OR p.group_id = %int%
    )
    AND (
      p.endtime <= \'1970-01-01 01:00:00\'
      OR p.endtime > %string%
    )
  GROUP BY p.pollid
  ORDER BY changedate DESC
  LIMIT 0, %int%', $auth['group_id'], date("Y-m-d 00:00:00"), $cfg['home_item_cnt_poll']);

if ($db->num_rows($query) > 0) {
    while ($row = $db->fetch_array($query)) {
        $smarty->assign('link', 'index.php?mod=poll&action=show&step=2&pollid='. $row['pollid']);
        $smarty->assign('text', $func->CutString($row['caption'], 40));

        $text2        = ' (Votes: '. $row['votes'] .')';
        if ($row["endtime"] and $row["endtime"] < time()) {
            $text2 .= ' <div class="infolink" style="display:inline"><img src="design/images/icon_locked.png" border="0" width="12" /><span class="infobox">'. t('Abstimmung wurde geschlossen') .'</span></div>';
        } elseif ($row["endtime"]) {
            $text2 .= ' ['. $func->unixstamp2date($row["endtime"], 'date') .']';
        }
        $smarty->assign('text2', $text2);

        if ($func->CheckNewPosts($row['changedate'], 'poll', $row['pollid'])) {
            $content .= $smarty->fetch('modules/home/templates/show_row_new.htm');
        } else {
            $content .= $smarty->fetch('modules/home/templates/show_row.htm');
        }
    }
} else {
    $content = "<i>". t('Keine Umfragen vorhanden') ."</i>";
}
