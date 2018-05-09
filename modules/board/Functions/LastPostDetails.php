<?php

/**
 * @param $date
 * @return string
 */
function LastPostDetails($date)
{
    global $db, $line, $dsp, $cfg;

    if ($date) {
        $row = $db->qry_first("SELECT p.userid, p.pid, p.tid, u.username FROM %prefix%board_posts AS p
      LEFT JOIN %prefix%user AS u ON p.userid = u.userid
      WHERE UNIX_TIMESTAMP(p.date) = %string% AND p.tid = %int%", $date, $line['tid']);

        $row2 = $db->qry_first(
            "SELECT COUNT(*) AS cnt FROM %prefix%board_posts AS p
        WHERE p.tid = %int%
        GROUP BY p.tid",
            $line['tid']
        );
        $page = floor(($row2['cnt'] - 1) / $cfg['board_max_posts']);

        $ret = '<a href="index.php?mod=board&action=thread&tid='. $row['tid'] .'&posts_page='. $page .'#pid'. $row['pid'] .'" class="menu">'. date('d.m.y H:i', $date);
        if ($row['userid']) {
            $ret .= '<br /></a> '. $dsp->FetchUserIcon($row['userid'], $row['username']);
        } else {
            $ret .= '<br />Gast_';
        }
        return $ret;
    } else {
        return $dsp->FetchIcon('no', '', '-');
    }
}
