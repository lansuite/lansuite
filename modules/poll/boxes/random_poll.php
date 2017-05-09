<?php

include_once('modules/poll/class_poll.php');
$poll = new poll;

$pollrow = $db->qry_first('SELECT pollid, caption, comment, UNIX_TIMESTAMP(endtime) AS endtime, multi, anonym FROM %prefix%polls
  WHERE (!group_id OR group_id = %int%)
  ORDER BY RAND() LIMIT 1', $auth['group_id']);
$voted = $db->qry_first('SELECT 1 AS found FROM %prefix%polloptions AS o
  INNER JOIN %prefix%pollvotes AS v ON o.polloptionid = v.polloptionid
  WHERE o.pollid = %int% AND v.userid = %int%', $pollrow['pollid'], $auth['userid']);

$box->DotRow('<b>'. $pollrow['caption'] .'</b>');

if ($voted['found'] or ($pollrow['endtime'] and $pollrow['endtime'] < time())) {
    $poll->ShowResult($pollrow['pollid'], $pollrow['anonym'], 1, 80);
} else {
    $res2 = $db->qry('SELECT polloptionid, caption FROM %prefix%polloptions WHERE pollid = %int%', $pollrow['pollid']);
    $out = '<form id="dsp_form2" name="dsp_form2" method="post" action="index.php?mod=poll&action=show&step=3&pollid='. $pollrow['pollid'] .'" >';
    while ($row2 = $db->fetch_array($res2)) {
        if ($pollrow['multi']) {
            $out .= '<input name="option[]" type="checkbox" class="form" value="'. $row2["polloptionid"] .'" /> <label for="option[]">'. $row2['caption'] .'</label><br />';
        } else {
            $out .= '<input name="option" type="radio" class="form" value="'. $row2["polloptionid"] .'" /> <label for="option">'. $row2['caption'] .'</label><br />';
        }
    }
    $out .= '<input type="submit" class="Button" name="imageField" value="Abstimmen" /></form>';
    $box->Row($out . "<br /><br />");
}
