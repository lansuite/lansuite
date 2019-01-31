<?php

// Exec Admin-Functions
if ($auth['type'] >= 2) {
    switch ($_GET['step']) {
        // Close Thread
        case 10:
            $db->qry("UPDATE %prefix%board_threads SET closed = 1 WHERE tid = %int%", $_GET['tid']);
            break;

        // Open Thread
        case 11:
            $db->qry("UPDATE %prefix%board_threads SET closed = 0 WHERE tid = %int%", $_GET['tid']);
            break;
    }
}

$tid = (int)$_GET["tid"];
$list_type = $auth['type'] + 1;

// Show Thread or create new
if ($tid) {
    $thread = $db->qry_first("
      SELECT
        t.fid,
        t.caption,
        t.closed,
        f.name AS ForumName,
        f.need_type,
        f.need_group
      FROM %prefix%board_threads AS t
        LEFT JOIN %prefix%board_forums AS f ON t.fid = f.fid
      WHERE
        t.tid=%int%
        AND f.need_type <= %string%
        AND (
          !f.need_group
          OR f.need_group = %int%
        )", $tid, $list_type, $auth['group_id']);
      
    if ($_GET['pid'] != '') {
        $current_post = $db->qry_first("SELECT userid FROM %prefix%board_posts WHERE pid = %int%", $_GET['pid']);
    }
} else {
    $thread = $db->qry_first("SELECT need_type, need_group FROM %prefix%board_forums WHERE fid = %int%", $_GET['fid']);
}

if (!$thread and $tid) {
    $func->information(t('Keine Beiträge vorhanden'));
} elseif ($thread['caption'] != '') {
    $framework->AddToPageTitle($thread['caption']);
    $framework->AddToPageTitle(t('Seite') .' '. ((int)$_GET['posts_page'] + 1));
    $fid = $thread["fid"];

    // Mark thread read
    $func->SetRead('board', $tid);

    // Tread Headline
    $hyperlink = '<a href="%s" class="menu">%s</a>';
    $overview_capt = '<b>'.sprintf($hyperlink, "index.php?mod=board", t('Forum')).'</b>';
    $forum_capt = '<b>'.sprintf($hyperlink, "index.php?mod=board&action=forum&fid=$fid", $thread['ForumName']).'</b>';
    $dsp->NewContent($thread["caption"], "<br/>".t('Du bist hier » ').$overview_capt.' » '.$forum_capt.' » '.$thread["caption"]);

    // Generate Thread-Buttons
    $buttons = '';
    if ($auth["type"] > 1) {
        if ($thread['closed']) {
            $buttons .= ' '. $dsp->FetchIcon("unlocked", "index.php?mod=board&action=thread&step=11&tid=$tid");
        } else {
            $buttons .= ' '. $dsp->FetchIcon("locked", "index.php?mod=board&action=thread&step=10&tid=$tid");
        }
        $buttons .= ' '. $dsp->FetchIcon("delete", "index.php?mod=board&action=delete&tid=$tid");
    }

    $query = $db->qry("
      SELECT
        pid,
        comment,
        userid,
        UNIX_TIMESTAMP(date) AS date,
        INET6_NTOA(ip) AS ip,
        file
      FROM %prefix%board_posts
      WHERE tid=%int%
      ORDER BY date", $tid);
    $count_entrys = $db->num_rows($query);
    
    // Page select
    if ($count_entrys > $cfg['board_max_posts']) {
        $pages = $func->page_split($_GET['posts_page'], $cfg['board_max_posts'], $count_entrys, "index.php?mod=board&action=thread&tid=$tid", "posts_page");
        $query = $db->qry("
          SELECT
            pid,
            comment,
            userid,
            UNIX_TIMESTAMP(date) AS date,
            UNIX_TIMESTAMP(changedate) AS changedate,
            changecount,
            INET6_NTOA(ip) AS ip,
            file
          FROM %prefix%board_posts
          WHERE tid=%int%
          ORDER BY date %plain%", $tid, $pages['sql']);
    }
    $dsp->AddSingleRow($buttons.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$pages['html']);

    $z = 0;
    while ($row = $db->fetch_array($query)) {
        $pid = $row["pid"];

        $smarty->assign('pid', $pid);

        $date = $func->unixstamp2date($row["date"], "datetime");
        if ($row['changecount'] > 0) {
            $date .= '<br />'. t('Geändert') .': '. $row['changecount'] .'x';
            $date .= '<br />'. $func->unixstamp2date($row['changedate'], 'datetime');
        }
        $smarty->assign('date', $date);

        $text = $func->text2html($row["comment"]);
        if ($row['file']) {
            $text .= $dsp->FetchAttachmentRow($row['file']);
        }
        $smarty->assign('text', $text);

        if ($row['userid'] == 0) {
            preg_match("@<!--(.*)-->@", $row['comment'], $tmp);
            $userdata['username'] = t('Gast') . "_" . trim($tmp[1]);
            $userdata['type'] = t('Gast');
            $userdata["avatar"] = "";
            $userdata["rank"] =  t('Gast');
            $userdata["posts"] = "";
            $userdata["signature"] = "";
        } else {
            $userdata = GetUserInfo($row["userid"]);
        }

        $smarty->assign('username', $dsp->FetchUserIcon($row['userid'], $userdata["username"]));

        $type = $userdata["type"];
        if ($auth['type'] >= 2) {
            $type .= '<br />IP: <a href="https://dnsquery.org/ipwhois/'. $row['ip'] .'" target="_blank">'. $row['ip'] .'</a>';
        }
        $smarty->assign('type', $userdata["type"]);

        $smarty->assign('posts', t('Beiträge') . ': <a href="index.php?mod=board&action=ranking">'. $userdata['posts'] .'</a>');
        $smarty->assign('avatar', $userdata["avatar"]);
    
        if ($cfg['board_ranking']) {
            $smarty->assign('rank', t('Rang') . ': <a href="index.php?mod=board&action=ranking">'. $userdata['rank'] .'</a>');
        }

        $signature = '';
        if ($userdata["signature"]) {
            $signature = '<hr size="1" width="100%" color="cccccc">'.$func->text2html($userdata["signature"]);
        }
        $smarty->assign('signature', $signature);

        $edit = '';
        if ($auth['type'] > 1) {
            $edit .= $dsp->FetchIcon("delete", "index.php?mod=board&action=delete&pid=$pid&posts_page=" . $_GET['posts_page'], '', '', 'right');
        }
        if ($auth['type'] > 1 or $row["userid"] == $auth["userid"]) {
            $edit .= $dsp->FetchIcon("edit", "index.php?mod=board&action=thread&fid=$fid&tid=$tid&pid=$pid&posts_page=" . $_GET['posts_page'], '', '', 'right');
        }
        $edit .= $dsp->FetchIcon("quote", "javascript:InsertCode(document.dsp_form1.comment, '[quote]" . str_replace("\n", "\\n", addslashes(str_replace('"', '', $row["comment"]))) . "[/quote]')", '', '', 'right');
        ;
        $smarty->assign('edit', $edit);

        ($z % 2 == 0)? $highlighted = '' : $highlighted = '_highlighted';
        $smarty->assign('highlighted', $highlighted);
    
        $dsp->AddSmartyTpl('board_thread_row', 'board');
        $z++;
    }

    if ($_SESSION['threadview'] != $tid) {
        $db->qry("UPDATE %prefix%board_threads SET views=views+1 WHERE tid=%int%", $tid);
    }
    $_SESSION['threadview'] = $tid;

    $dsp->AddSingleRow($buttons.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$pages['html']);
}

if ($thread['closed']) {
    $func->information(t('Dieser Thread wurde geschlossen. Es können keine Antworten mehr geschrieben werden'), NO_LINK);
} elseif ($thread['need_type'] >= 1 and !$auth['login'] and !$_GET['tid']) {
    $func->information(t('Du musst dich zuerst einloggen, um einen Thread in diesem Forum starten zu können'), NO_LINK);
} elseif ($thread['need_type'] >= 1 and !$auth['login'] and $_GET['tid']) {
    $func->information(t('Um in diesem Board zu posten zu antworten, logge dich bitte zuerst ein.'), NO_LINK);
} elseif ($thread['need_type'] > (int)($auth['type'] + 1)) {
    $func->information(t('Um in diesem Board zu posten, musst du Admin sein.'), NO_LINK);
} elseif ($thread['need_group'] and $auth['group_id'] != $thread['need_group'] and $_GET['tid']) {
    $func->information(t('Du gehörst nicht der richtigen Gruppe an, um auf diese Beiträge zu antworten.'), NO_LINK);
} elseif ($thread['need_group'] and $auth['group_id'] != $thread['need_group'] and !$_GET['tid']) {
    $new_thread = t('Du gehörst nicht der richtigen Gruppe an, um einen Thread in diesem Forum starten zu können');
} elseif ($_GET['pid'] != '' and $auth['type'] <= 1 and $current_post['userid'] != $auth['userid']) {
    $func->error('Du darfst nur deine eigenen Beiträge editieren!', NO_LINK);
} elseif ($thread) {
    // Topic erstellen oder auf Topic antworten
    if ($_GET['tid']) {
        $dsp->AddFieldsetStart(t('Antworten - Der Beitrag kann anschließend noch editiert werden'));
    } else {
        $dsp->AddFieldsetStart(t('Thread erstellen'));
    }

    $mf = new \LanSuite\MasterForm();
  
    if ($thread['caption'] == '') {
        $mf->AddField(t('Überschrift'), 'caption', 'varchar(255)');
    }
    $mf->AddField(t('Text'), 'comment', '', \LanSuite\MasterForm::LSCODE_BIG);
    $mf->AddField(t('Bild / Datei anhängen'), 'file', \LanSuite\MasterForm::IS_FILE_UPLOAD, 'ext_inc/board_upload/', \LanSuite\MasterForm::FIELD_OPTIONAL);
  
    $mf->AddFix('tid', $_GET['tid']);
    if ($_GET['pid'] == '') {
        $mf->AddFix('date', 'NOW()');
        $mf->AddFix('userid', $auth['userid']);
        $mf->AddFix('ip', $_SERVER['REMOTE_ADDR']);
    } else {
        $mf->AddFix('changedate', 'NOW()');
        $mf->AddFix('changecount', '++');
    }
  
    if ($pid = $mf->SendForm('index.php?mod=board&action=thread&fid='. $_GET['fid'] .'&tid='. $_GET['tid'].'&posts_page='.$_GET['posts_page'], 'board_posts', 'pid', $_GET['pid'])) {
        $tid = (int)$_GET['tid'];
  
        // Update thread-table, if new thread
        if (!$_GET['tid'] and $_POST['caption'] != '') {
            $db->qry("INSERT INTO %prefix%board_threads SET
                fid = %int%,
                caption = %string%
                ", $_GET['fid'], $_POST['caption']);
                $tid = $db->insert_id();
  
                // Assign just created post to this new thread
                $db->qry("UPDATE %prefix%board_posts SET tid = %int% WHERE pid = %int%", $tid, $pid);
        }

        // Send email-notifications to thread-subscribers
        $path = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], "index.php"));

        $mail = new \LanSuite\Module\Mail\Mail();

        if (!$_GET['fid']) {
            $_GET['fid'] = $thread['fid'];
        }
        // Internet-Mail
        $subscribers = $db->qry("
          SELECT
            b.userid,
            u.firstname,
            u.name,
            u.email
          FROM %prefix%board_bookmark AS b
            LEFT JOIN %prefix%user AS u ON b.userid = u.userid
          WHERE
            b.email = 1
            AND (
              b.tid = %int%
              OR b.fid = %int%
            )", $tid, $_GET['fid']);
        while ($subscriber = $db->fetch_array($subscribers)) {
            if ($subscriber['userid'] != $auth['userid']) {
                $mail->create_inet_mail($subscriber["firstname"]." ".$subscriber["name"], $subscriber["email"], $cfg["board_subscribe_subject"], str_replace("%URL%", "http://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}{$path}index.php?mod=board&action=thread&tid=$tid", $cfg["board_subscribe_text"]), $cfg["sys_party_mail"]);
            }
        }
        $db->free_result($subscribers);
  
        // Sys-Mail
        $subscribers = $db->qry("
          SELECT userid
          FROM %prefix%board_bookmark AS b
          WHERE
            b.sysemail = 1
            AND (
              b.tid = %int%
              OR b.fid = %int%
            )", $tid, $_GET['fid']);
        while ($subscriber = $db->fetch_array($subscribers)) {
            if ($subscriber['userid'] != $auth['userid']) {
                $mail->create_sys_mail($subscriber["userid"], $cfg["board_subscribe_subject"], str_replace("%URL%", "http://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}{$path}index.php?mod=board&action=thread&tid=$tid", $cfg["board_subscribe_text"]));
            }
        }
        $db->free_result($subscribers);
    }
    $dsp->AddFieldsetEnd();
}

if ($thread['caption'] != '') {
    // Bookmarks and Auto-Mail
    if ($auth['login']) {
        if ($_GET["set_bm"]) {
            $db->qry_first("DELETE FROM %prefix%board_bookmark WHERE tid = %int% AND userid = %int%", $tid, $auth['userid']);
            if ($_POST["check_bookmark"]) {
                $db->qry_first("INSERT INTO %prefix%board_bookmark SET tid = %int%, userid = %int%, email = %string%, sysemail = %string%", $tid, $auth['userid'], $_POST["check_email"], $_POST["check_sysemail"]);
            }
        }
  
        $bookmark = $db->qry_first("SELECT 1 AS found, email, sysemail FROM %prefix%board_bookmark WHERE tid = %int% AND userid = %int%", $tid, $auth['userid']);
        if ($bookmark["found"]) {
            $_POST["check_bookmark"] = 1;
        }
        if ($bookmark["email"]) {
            $_POST["check_email"] = 1;
        }
        if ($bookmark["sysemail"]) {
            $_POST["check_sysemail"] = 1;
        }
  
        $dsp->SetForm("index.php?mod=board&action=thread&tid=$tid&fid=$fid&set_bm=1");
        $dsp->AddFieldsetStart(t('Monitoring'));
        $additionalHTML = "onclick=\"CheckBoxBoxActivate('email', this.checked)\"";
        $dsp->AddCheckBoxRow("check_bookmark", t('Lesezeichen'), t('Diesen Beitrag in meine Lesezeichen aufnehmen<br><i>(Lesezeichen ist Vorraussetzung, um Benachrichtigung per Mail zu abonnieren)</i>'), "", 1, $_POST["check_bookmark"], '', '', $additionalHTML);
        $dsp->StartHiddenBox('email', $_POST["check_bookmark"]);
        $dsp->AddCheckBoxRow("check_email", t('E-Mail Benachrichtigung'), t('Bei Antworten auf diesen Beitrag eine Internet-Mail an mich senden'), "", 1, $_POST["check_email"]);
        $dsp->AddCheckBoxRow("check_sysemail", t('System-E-Mail'), t('Bei Antworten auf diesen Beitrag eine System-Mail an mich senden'), "", 1, $_POST["check_sysemail"]);
        if ($bookmark["found"]) {
            $dsp->StopHiddenBox();
        }
        $dsp->AddFormSubmitRow("edit");
        if (!$bookmark["found"]) {
            $dsp->StopHiddenBox();
        }
        $dsp->AddFieldsetEnd();
    }
  
    // Generate Boardlist-Dropdown
    $foren_liste = $db->qry("
      SELECT
        fid,
        name
      FROM %prefix%board_forums
      WHERE
        need_type <= %string%
        AND (
          !need_group
          OR need_group = %int%
      )", $list_type, $auth['group_id']);
    $goto = '';
    while ($forum = $db->fetch_array($foren_liste)) {
        $goto .= "<option value=\"{$forum["fid"]}\">{$forum["name"]}</option>";
    }
    $smarty->assign('goto', $goto);
    $smarty->assign('forum_choise', t('Bitte auswählen'));
    $dsp->AddDoubleRow(t('Gehe zu Forum'), $smarty->fetch('modules/board/templates/forum_dropdown.htm'));
}

$dsp->AddBackButton("index.php?mod=board&action=forum&fid=$fid", "board/show_post");
