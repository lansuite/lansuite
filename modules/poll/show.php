<?php

$poll = new LanSuite\Module\Poll\Poll();

if ($_GET['step'] >= 2) {
    $pollrow = $db->qry_first('
      SELECT
        caption,
        comment,
        UNIX_TIMESTAMP(endtime) AS endtime,
        multi,
        anonym,
        requirement
      FROM %prefix%polls
      WHERE
        pollid = %int%
        AND (
          !group_id
          OR group_id = %int%
        )', $_GET['pollid'], $auth['group_id']);
    $dsp->NewContent(t('Poll') .': '. $pollrow["caption"], $func->text2html($pollrow['comment']));

    $voted = $db->qry_first('
      SELECT
        1 AS found
      FROM %prefix%polloptions AS o
      INNER JOIN %prefix%pollvotes AS v ON o.polloptionid = v.polloptionid
      WHERE
        o.pollid = %int%
        AND v.userid = %int%', $_GET['pollid'], $auth['userid']);
    if (!$pollrow['caption']) {
        $func->error(t('Dieser Poll existiert nicht, oder du hast keine Berechtigung ihn zu sehen'), NO_LINK);
        $_GET['step'] = 1;
    }

    if ($_GET['step'] >= 3) {
        if ($pollrow['endtime'] and $pollrow['endtime'] < time()) {
            $func->information(t('Dieser Poll ist bereits beendet'));
            $_GET['step'] = 2;
        } elseif ($voted['found']) {
            $func->information(t('Du hast bereits gevoted'));
            $_GET['step'] = 2;
        } elseif ($pollrow['requirement'] == 1 and $auth['login'] == 0) {
            $func->information(t("Du musst eingeloggt sein um zu diesem Poll ihre Stimme abzugeben."));
            $_GET['step'] = 2;
        } elseif (!is_array($_POST['option']) and !is_string($_POST['option'])) {
            $func->information(t("Du hast keine Option ausgewählt!"));
            $_GET['step'] = 2;
        }
    }

    $framework->AddToPageTitle($pollrow["caption"]);
}

switch ($_GET['step']) {
    default:
        include_once('modules/poll/search.inc.php');
        break;

    case 2:
        $func->SetRead('poll', $_GET['pollid']);

        // Has voted? -> Show results
        if ($voted['found'] or ($pollrow['endtime'] and $pollrow['endtime'] < time())) {
            $poll->ShowResult($_GET['pollid'], $pollrow['anonym']);

        // Has not voted? -> Show form
        } else {
            $dsp->SetForm('index.php?mod=poll&action=show&step=3&pollid='. $_GET['pollid']);
  
            $res = $db->qry('SELECT polloptionid, caption FROM %prefix%polloptions WHERE pollid = %int% ORDER BY polloptionid', $_GET['pollid']);
            while ($row = $db->fetch_array($res)) {
                if ($pollrow['multi']) {
                    $dsp->AddCheckBoxRow('option[]', $row['caption'], '', '', '', '', '', $row['polloptionid']);
                } else {
                    $dsp->AddRadioRow("option", $row['caption'], $row['polloptionid']);
                }
            }
            $db->free_result($res);
  
            $dsp->AddFormSubmitRow(t('Abstimmen'));
        }
        $dsp->AddBackButton("index.php?mod=poll", "poll/vote");
        break;

    case 3:
        if ($pollrow['multi']) {
            foreach ($_POST['option'] as $option) {
                $db->qry('INSERT INTO %prefix%pollvotes SET userid = %int%, polloptionid = %int%', $auth['userid'], $option);
            }
        } else {
            $db->qry('INSERT INTO %prefix%pollvotes SET userid = %int%, polloptionid = %int%', $auth['userid'], $_POST['option']);
        }
            $func->confirmation(t('Deine Stimme wurde gezählt'), 'index.php?mod=poll&action=show&step=2&pollid='. $_GET['pollid']);
        break;
}
