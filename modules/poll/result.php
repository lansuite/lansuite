<?php

    include_once('modules/poll/class_poll.php');
    $poll = new poll;

    $pollrow = $db->qry_first('SELECT caption, comment, UNIX_TIMESTAMP(endtime) AS endtime, multi, anonym, requirement FROM %prefix%polls
    WHERE	pollid = %int% AND (!group_id OR group_id = %int%)', $_GET['pollid'], $auth['group_id']);
    $dsp->NewContent(t('Poll') .': '. $pollrow["caption"], $func->text2html($pollrow['comment']));

    $voted = $db->qry_first('SELECT 1 AS found FROM %prefix%polloptions AS o
    INNER JOIN %prefix%pollvotes AS v ON o.polloptionid = v.polloptionid
    WHERE o.pollid = %int% AND v.userid = %int%', $_GET['pollid'], $auth['userid']);
    if (!$pollrow['caption']) {
        $func->error(t('Dieser Poll existiert nicht, oder du hast keine Berechtigung ihn zu sehen'), NO_LINK);
        $_GET['step'] = 1;
    }
    
    $func->SetRead('poll', $_GET['pollid']);

    $poll->ShowResult($_GET['pollid'], $pollrow['anonym']);
    
    $dsp->AddBackButton("index.php?mod=poll", "poll/vote");
