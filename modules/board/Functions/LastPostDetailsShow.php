<?php

/**
 * @param string $date
 * @return string
 */
function LastPostDetailsShow($date)
{
    global $db, $line, $dsp, $cfg;

    if ($date) {
        $row = $db->qry_first("
          SELECT
            t.caption,
            p.userid,
            p.tid,
            p.pid
          FROM %prefix%board_posts AS p
          LEFT JOIN %prefix%board_threads AS t ON p.tid = t.tid
          WHERE
            UNIX_TIMESTAMP(p.date) = %string%
            AND t.fid = %int%", $date, $line['fid']);

        $row2 = $db->qry_first("
          SELECT COUNT(*) AS cnt
          FROM %prefix%board_posts AS p
          WHERE p.tid = %int%
          GROUP BY p.tid", $row['tid']);
        $page = floor(($row2['cnt'] - 1) / $cfg['board_max_posts']);

        if (strlen($row['caption']) > 18) {
            $row['caption'] = substr($row['caption'], 0, 16). '...';
        }
        return '<a href="index.php?mod=board&action=thread&tid='. $row['tid'] .'&posts_page='. $page .'#pid'. $row['pid'] .'" class="menu">'. $row['caption'] .'<br />'. date('d.m.y H:i', $date) .'</a> '. $dsp->FetchUserIcon($row['userid']);
    } else {
        return $dsp->FetchIcon('no', '', '-');
    }
}
