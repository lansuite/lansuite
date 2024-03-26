<?php

$fID = $_GET['fid'] ?? 0;
if ($fID) {
    $row = $database->queryWithOnlyFirstRow("SELECT name, need_type, need_group FROM %prefix%board_forums WHERE fid = ?", [$_GET["fid"]]);
    $new_thread = $dsp->FetchIcon("add", "index.php?mod=board&action=thread&fid=" . $_GET['fid']);

    // Board Headline
    $hyperlink = '<a href="%s" class="menu">%s</a>';
    $overview_capt = '<b>'.sprintf($hyperlink, "index.php?mod=board", t('Forum')).'</b>';
    $dsp->NewContent($row['name'], "<br />".t('Du bist hier » ').$overview_capt.' » '.$row['name']);
    $framework->AddToPageTitle($row['name']);
    $dsp->AddSingleRow($new_thread ." ". $dsp->FetchIcon("back", "index.php?mod=board"));
}

$stepParameter = $_GET['step'] ?? 0;
switch ($stepParameter) {
    // Edit headline
    case 10:
        if ($auth['type'] >= \LS_AUTH_TYPE_ADMIN) {
            $dsp->AddFieldsetStart(t('Thread bearbeiten'));
            $mf = new \LanSuite\MasterForm();
            $mf->AddField(t('Überschrift'), 'caption', 'varchar(255)');
            $pid = $mf->SendForm('index.php?mod=board&action=forum&step=10&fid='. $_GET['fid'] .'&tid='. $_GET['tid'], 'board_threads', 'tid', $_GET['tid']);
            $dsp->AddFieldsetEnd();
        }
        break;

    case 20:
        if ($auth['type'] >= \LS_AUTH_TYPE_ADMIN) {
            foreach ($_POST['action'] as $key => $val) {
                $database->query("UPDATE %prefix%board_threads SET fid = ? WHERE tid = ?", [$_GET['to_fid'], $key]);
            }
        }
        break;
  
    // Delete Bookmark
    case 30:
        $GetFid = $database->queryWithOnlyFirstRow('SELECT fid FROM %prefix%board_threads WHERE tid = ?', [$_GET['tid']]);
        $database->query('DELETE FROM %prefix%board_bookmark WHERE fid = 0 AND tid = ? AND userid = ?', [$_GET['tid'], $auth['userid']]);
        $database->query('DELETE FROM %prefix%board_bookmark WHERE fid = ? AND tid = 0 AND userid = ?', [$GetFid['fid'], $auth['userid']]);
        break;

    // Label
    // None
    case 40:
    case 41:
    case 42:
    case 43:
    case 44:
    case 45:
        if ($auth['type'] >= \LS_AUTH_TYPE_ADMIN) {
            foreach ($_POST['action'] as $key => $val) {
                $database->query('UPDATE %prefix%board_threads SET label = ? WHERE tid = ?', [$_GET['step'] - 40, $key]);
            }
        }
        break;

    // Sticky
    // Add
    case 50:
        if ($auth['type'] >= \LS_AUTH_TYPE_ADMIN) {
            foreach ($_POST['action'] as $key => $val) {
                $database->query('UPDATE %prefix%board_threads SET sticky = 1 WHERE tid = ?', [$key]);
            }
        }
        break;
    // Remove
    case 51:
        if ($auth['type'] >= \LS_AUTH_TYPE_ADMIN) {
            foreach ($_POST['action'] as $key => $val) {
                $database->query('UPDATE %prefix%board_threads SET sticky = 0 WHERE tid = ?', [$key]);
            }
        }
        break;
    // Close Threads
    case 52:
        if ($auth['type'] >= \LS_AUTH_TYPE_ADMIN) {
            foreach ($_POST['action'] as $key => $val) {
                $database->query("UPDATE %prefix%board_threads SET closed = 1 WHERE tid = ?", [$key]);
            }
        }
        break;
}

$colors = [
    0 => '',
    1 => 'red',
    2 => 'blue',
    3 => 'green',
    4 => 'yellow',
    5 => 'purple',

];

$postSearchInputOne = $_POST['search_input'][1] ?? '';
$postSearchInputTwo = $_POST['search_input'][2] ?? '';
$getSearchInputOne = $_GET['search_input'][1]  ?? '';
$getSearchInputTwo = $_GET['search_input'][2] ?? '';
if ($postSearchInputOne != '' || $postSearchInputTwo != '' || $getSearchInputOne != '' || $getSearchInputTwo != '') {
    $dsp->AddSingleRow('<b>'.t('Achtung: du hast als Suche einen Autor, bzw. Text angegeben. Die Ergebnis-Felder Antworten, sowie erster und letzter Beitrag beziehen sich daher nur noch auf Posts, in denen diese Eingaben gefunden wurden, nicht mehr auf den ganzen Thread!').'</b>');
}

$ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2();
$ms2->query['from'] = "%prefix%board_threads AS t
    LEFT JOIN %prefix%board_forums AS f ON t.fid = f.fid
    LEFT JOIN %prefix%board_posts AS p ON t.tid = p.tid
    LEFT JOIN %prefix%lastread AS r ON t.tid = r.entryid AND r.tab = 'board' AND r.userid = ". (int)$auth['userid'] ."
    LEFT JOIN %prefix%user AS u ON p.userid = u.userid
    LEFT JOIN %prefix%board_bookmark AS b ON (b.fid = t.fid OR b.tid = t.tid) AND b.userid = ". (int)$auth['userid'] ."
    ";
$ms2->query['where'] = 'f.need_type <= '. (int)($auth['type'] + 1 ." AND (!need_group OR need_group = {$auth['group_id']})");

$fId = $_GET['fid'] ?? 0;
if ($fId) {
    $ms2->query['where'] .= ' AND t.fid = '. (int)$_GET['fid'];
}
if ($_GET['action'] == 'bookmark') {
    $ms2->query['where'] .= ' AND b.bid IS NOT NULL';
}
$ms2->query['default_order_by'] = 't.sticky DESC, LastPost DESC';

$ms2->AddBGColor('label', $colors);

if (!$fId) {
    $ms2->AddTextSearchField(t('Titel'), array('t.caption' => 'like'));
    $ms2->AddTextSearchField(t('Text'), array('p.comment' => 'fulltext'));
    $ms2->AddTextSearchField(t('Autor'), array('u.username' => '1337', 'u.name' => 'like', 'u.firstname' => 'like'));

    $list = [];
    $list[''] = t('Alle');
    $res = $db->qry("SELECT fid, name FROM %prefix%board_forums");
    while ($row = $db->fetch_array($res)) {
        $list[$row['fid']] = $row['name'];
    }
    $ms2->AddTextSearchDropDown(t('Forum'), 'f.fid', $list);
    $db->free_result($res);
}

$ms2->AddSelect('t.closed');
$ms2->AddSelect('t.sticky');
$ms2->AddResultField(t('Status'), 'UNIX_TIMESTAMP(r.date) AS date', 'NewPosts');
if ($fId) {
    $ms2->AddResultField(t('Thread'), 't.caption', 'FormatTitle');
} else {
    $ms2->AddResultField(t('Thread'), 'CONCAT(\'<b>\', f.name, \'</b><br />\', t.caption) AS ThreadName', 'FormatTitle');
}
$ms2->AddResultField(t('Aufrufe'), 't.views');
$ms2->AddResultField(t('Antworten'), '(COUNT(p.pid) - 1) AS posts');
$ms2->AddResultField(t('Letzter Beitrag'), 'UNIX_TIMESTAMP(MAX(p.date)) AS LastPost', 'LastPostDetails');

if ($_GET['action'] == 'bookmark') {
    $ms2->AddResultField(t('E-Mail'), 'b.email', 'TrueFalse');
    $ms2->AddResultField(t('System-Mail'), 'b.sysemail', 'TrueFalse');
}

$ms2->AddIconField('details', 'index.php?mod=board&action=thread&tid=', t('Details'));
if ($_GET['action'] != 'bookmark') {
    if ($auth['type'] >= \LS_AUTH_TYPE_ADMIN) {
        $ms2->AddIconField('edit', 'index.php?mod=board&action=forum&step=10&fid='. $fId .'&tid=', t('Überschrift editieren'));
    }
    if ($auth['type'] >= \LS_AUTH_TYPE_SUPERADMIN) {
        $ms2->AddIconField('delete', 'index.php?mod=board&action=delete&step=11&tid=', t('Löschen'));
    }

    if ($auth['type'] >= \LS_AUTH_TYPE_ADMIN) {
        $res = $db->qry("SELECT fid, name FROM %prefix%board_forums");
        while ($row = $db->fetch_array($res)) {
            $ms2->AddMultiSelectAction(t('Verschieben nach '). $row['name'], 'index.php?mod=board&action=forum&step=20&to_fid='. $row['fid'] .'&fid='. $fId, 1, 'in');
        }
        $db->free_result($res);

        $ms2->AddMultiSelectAction(t('Markierung entfernen'), 'index.php?mod=board&action=forum&fid='. $fId .'&step=40', 0, 'selection_none');
        $ms2->AddMultiSelectAction(t('Markieren: Rot'), 'index.php?mod=board&action=forum&fid='. $fId .'&step=41', 0, 'selection_all');
        $ms2->AddMultiSelectAction(t('Markieren: Blau'), 'index.php?mod=board&action=forum&fid='. $fId .'&step=42', 0, 'selection_all');
        $ms2->AddMultiSelectAction(t('Markieren: Grün'), 'index.php?mod=board&action=forum&fid='. $fId .'&step=43', 0, 'selection_all');
        $ms2->AddMultiSelectAction(t('Markieren: Gelb'), 'index.php?mod=board&action=forum&fid='. $fId .'&step=44', 0, 'selection_all');
        $ms2->AddMultiSelectAction(t('Markieren: Lila'), 'index.php?mod=board&action=forum&fid='. $fId .'&step=45', 0, 'selection_all');

        $ms2->AddMultiSelectAction(t('Als Top Thread setzen'), 'index.php?mod=board&action=forum&fid='. $fId .'&step=50', 0, 'important');
        $ms2->AddMultiSelectAction(t('Top Thread Marker entfernen'), 'index.php?mod=board&action=forum&fid='. $fId .'&step=51', 0, 'del_important');
        $ms2->AddMultiSelectAction(t('Schliessen'), 'index.php?mod=board&action=forum&fid='. $fId .'&step=52', 1);
    }
} else {
    $ms2->AddIconField('delete', 'index.php?mod=board&action=bookmark&step=30&tid=', t('Löschen'));
}

$ms2->PrintSearch('index.php?mod=board&action='. $_GET['action'] .'&fid='. $fId, 't.tid');

if ($fId) {
    $dsp->AddSingleRow($new_thread ." ". $dsp->FetchIcon("back", "index.php?mod=board"));
}

// Bookmarks and Auto-Mail
if ($fId && $auth['login']) {
    $setBmParameter = $_GET["set_bm"] ?? 0;
    if ($setBmParameter) {
        $database->query("DELETE FROM %prefix%board_bookmark WHERE fid = ? AND userid = ?", [$_GET['fid'], $auth['userid']]);
        if ($_POST["check_bookmark"]) {
            $database->query("INSERT INTO %prefix%board_bookmark SET fid = ?, userid = ?, email = ?, sysemail = ?", [$_GET['fid'], $auth['userid'], $_POST["check_email"], $_POST["check_sysemail"]]);
        }
    }

    $bookmark = $database->queryWithOnlyFirstRow("SELECT 1 AS found, email, sysemail FROM %prefix%board_bookmark WHERE fid = ? AND userid = ?", [$_GET['fid'], $auth['userid']]);
    if (is_array($bookmark) && $bookmark["found"]) {
        $_POST["check_bookmark"] = 1;
    }
    if (is_array($bookmark) && $bookmark["email"]) {
        $_POST["check_email"] = 1;
    }
    if (is_array($bookmark) && $bookmark["sysemail"]) {
        $_POST["check_sysemail"] = 1;
    }

    $checkBookmarkParameter = $_POST["check_bookmark"] ?? 0;
    $checkEmailParameter = $_POST["check_email"] ?? 0;
    $checkSysEmailParameter = $_POST["check_sysemail"] ?? 0;

    $dsp->SetForm("index.php?mod=board&action=forum&fid={$fId}&set_bm=1");
    $dsp->AddFieldsetStart(t('Monitoring'));
    $additionalHTML = "onclick=\"CheckBoxBoxActivate('email', this.checked)\"";
    $dsp->AddCheckBoxRow("check_bookmark", t('Lesezeichen'), t('Alle Beiträge in diesem Forum in meine Lesezeichen aufnehmen<br><i>(Lesezeichen ist Vorraussetzung, um Benachrichtigung per Mail zu abonnieren)</i>'), "", 1, $checkBookmarkParameter, '', '', $additionalHTML);
    $dsp->StartHiddenBox('email', $checkBookmarkParameter);
    $dsp->AddCheckBoxRow("check_email", t('E-Mail Benachrichtigung'), t('Bei Antworten auf Beiträge in Threads dieses Forums eine Internet-Mail an mich senden'), "", 1, $checkEmailParameter);
    $dsp->AddCheckBoxRow("check_sysemail", t('System-E-Mail'), t('Bei Antworten auf Beiträge in Threads dieses Forums eine System-Mail an mich senden'), "", 1, $checkSysEmailParameter);
    $dsp->StopHiddenBox();
    $dsp->AddFormSubmitRow("edit");
    $dsp->AddFieldsetEnd();
}

// Generate Boardlist-Dropdown
$goto = '';
$foren_liste = $db->qry("SELECT fid, name FROM %prefix%board_forums WHERE need_type <= %int% AND (!need_group OR need_group = %int%)", ($auth['type'] + 1), $auth['group_id']);
while ($forum = $db->fetch_array($foren_liste)) {
    $goto .= "<option value=\"{$forum["fid"]}\">{$forum["name"]}</option>";
}
$smarty->assign('goto', $goto);
$smarty->assign('forum_choise', t('Bitte auswählen'));
$dsp->AddDoubleRow(t('Gehe zu Forum'), $smarty->fetch('modules/board/templates/forum_dropdown.htm'));
