<?php

// Check if news id is valid
$check = $db->qry_first('SELECT caption FROM %prefix%news WHERE newsid = %int%', $_GET['newsid']);
if ($check["caption"] != "") {
    $framework->AddToPageTitle($check["caption"]);
    $func->SetRead('news', $_GET['newsid']);
  
    // Get news data
    $get_news = $db->qry_first('SELECT n.*, UNIX_TIMESTAMP(n.date) AS date, u.userid, u.username FROM %prefix%news n LEFT JOIN %prefix%user u ON u.userid = n.poster WHERE n.newsid = %int%', $_GET['newsid']);
    $templ_news_single_row_priority = $get_news["priority"];
    
    if ($templ_news_single_row_priority == 1) {
        $news_type = "important";
    } else {
        $news_type = "normal";
    }
    
    $smarty->assign('caption', $get_news["caption"]);
    $smarty->assign('userid', $get_news["poster"]);
    $smarty->assign('username', $dsp->FetchUserIcon($get_news['userid'], $get_news["username"]));
    $smarty->assign('date', $func->unixstamp2date($get_news["date"], "daydatetime"));

    $text = '';
    if ($auth["type"] > 1) {
        $text .= $dsp->FetchIcon("delete", "index.php?mod=news&action=delete&came_from=2&step=2&newsid={$_GET["newsid"]}", '', '', 'right');
        $text .= $dsp->FetchIcon("edit", "index.php?mod=news&action=change&came_from=1&step=2&newsid={$_GET["newsid"]}", '', '', 'right');
    }
    if ($cfg["news_html"] == 1) {
        $get_news['text'] = $func->text2html($get_news['text']);
    } else {
        $get_news['text'] = $func->AllowHTML($get_news['text']);
    }
    $text .= $get_news['text'];
    if ($get_news['link_1']) {
        $text .= '<br><u>'. t('Links zum Thema:') .'</u><br><a href="'. $get_news['link_1'] .'" target="_blank">'. $get_news['link_1'] .'</a>';
    }
    if ($get_news['link_2']) {
        $text .= '<br><a href="'. $get_news['link_2'] .'" target="_blank">'. $get_news['link_2'] .'</a>';
    }
    if ($get_news['link_3']) {
        $text .= '<br><a href="'. $get_news['link_3'] .'" target="_blank">'. $get_news['link_3'] .'</a>';
    }
    $smarty->assign('text', $text);

    if ($_GET["mcact"] == "" or $_GET["mcact"] == "show") {
        $dsp->NewContent(t('Newsmeldung + Kommentare'), t('Hier kannst du diese News kommentieren'));
        $dsp->AddSingleRow($smarty->fetch("modules/news/templates/show_single_row_$news_type.htm"));
        $dsp->AddSingleRow($dsp->FetchSpanButton(t('Newsübersicht'), "index.php?mod=news&action=show"));
    }
    
    if ($cfg['news_comments_allowed'] == false) {
        $dsp->AddSingleRow(t('Kommentare wurden deaktiviert.'));
    } else {
        new \LanSuite\MasterComment('news', $_GET['newsid'], array('news' => 'newsid'));
    }
} else {
    $func->error(t('Diese Newsmeldung existiert nicht'));
}
