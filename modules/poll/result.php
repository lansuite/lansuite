<?php

$poll = new LanSuite\Module\Poll\Poll();

$pollrow = $database->queryWithOnlyFirstRow('
  SELECT
    caption,
    comment,
    UNIX_TIMESTAMP(endtime) AS endtime,
    multi,
    anonym,
    requirement
  FROM %prefix%polls
  WHERE
    pollid = ?
    AND (
      !group_id
      OR group_id = ?
    )', [$_GET['pollid'], $auth['group_id']]);
$dsp->NewContent(t('Poll') .': '. $pollrow["caption"], $func->text2html($pollrow['comment']));

$voted = $database->queryWithOnlyFirstRow('
  SELECT
    1 AS found
    FROM %prefix%polloptions AS o
  INNER JOIN %prefix%pollvotes AS v ON o.polloptionid = v.polloptionid
  WHERE
    o.pollid = ?
    AND v.userid = ?', [$_GET['pollid'], $auth['userid']]);

if (!$pollrow['caption']) {
    $func->error(t('Dieser Poll existiert nicht, oder du hast keine Berechtigung ihn zu sehen'), NO_LINK);
    $_GET['step'] = 1;
}

$func->SetRead('poll', $_GET['pollid']);
$poll->ShowResult($_GET['pollid'], $pollrow['anonym']);
$dsp->AddBackButton("index.php?mod=poll", "poll/vote");
