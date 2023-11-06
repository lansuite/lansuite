<?php
// Forum posts
$dsp->AddDoubleRow(t('Board Posts'), $count_rows['count']);

// Threads
$get_board_threads = $db->qry("
  SELECT
    b.tid,
    UNIX_TIMESTAMP(b.date) AS date,
    t.caption
  FROM %prefix%board_posts AS b
    LEFT JOIN %prefix%board_threads AS t ON b.tid = t.tid
    LEFT JOIN %prefix%board_forums AS f ON t.fid = f.fid
  WHERE
    b.userid = %int%
    AND (
      f.need_type <= %int%
      OR f.need_type = '1'
    )
  GROUP BY b.tid
  ORDER BY date DESC
  LIMIT 20", $_GET['userid'], $auth['type']);

$threads = '';
while ($row_threads = $db->fetch_array($get_board_threads)) {
    $threads .= $func->unixstamp2date($row_threads['date'], "datetime")." - <a href=\"index.php?mod=board&action=thread&tid={$row_threads['tid']}\">{$row_threads['caption']}</a>". HTML_NEWLINE;
}
$db->free_result($get_board_threads);
$dsp->AddDoubleRow(t('Letzte 20 Threads'), $threads);
$dsp->AddSingleRow('');
