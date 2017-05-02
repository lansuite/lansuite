<?php
// COUNT NEWS
$get_amount = $db->qry_first('SELECT count(*) as number FROM %prefix%news');
$overall_news = $get_amount["number"];

if ($overall_news == 0) {
    $func->information(t('Es sind keine News vorhanden.'));
} else {
    if ($_GET['subaction'] == 'archive') {
        $dsp->NewContent(t('News Archiv'), t('Archivierte Mitteilungen'));

        if ($cfg["news_shorted_archiv"] == "") {
            $cfg["news_shorted_archiv"] = 10;
        }
        $pages = $func->page_split($_GET["news_page"], $cfg["news_shorted_archiv"], $overall_news - ($cfg['news_shorted'] + $cfg['news_completed']), "index.php?mod=news&action=show&subaction=archive", "news_page");

        $get_newsshorted = $db->qry("SELECT UNIX_TIMESTAMP(n.date) AS date, n.caption, n.text, u.username, n.newsid FROM %prefix%news AS n LEFT JOIN %prefix%user AS u ON u.userid = n.poster ORDER BY n.top DESC, n.date DESC %plain%", $pages["sql"]);
        while ($row=$db->fetch_array($get_newsshorted)) {
            $tmpDate = $func->unixstamp2date($row['date'], 'daydate');
            $shortnews[$tmpDate][$row[1]]['caption'] = "<a href=\"index.php?mod=news&amp;action=comment&amp;newsid=" .$row['newsid'] ."\">" .$row['caption'] ."</a>";
            $shortnews[$tmpDate][$row[1]]['text'] = substr(strip_tags($func->AllowHTML($row['text'])), 0, $cfg["news_shorted_length"]) .'...';
            $shortnews[$tmpDate][$row[1]]['username'] = $row['username'];
        }
        $tmpDate = "";
        $tmpSNCode ="<table cellspacing=\"5\" width=\"100%\">";
        foreach ($shortnews as $newsdate => $value) {
            $tmpSNCode .= "<tr><td colspan=\"2\"><strong>$newsdate</strong></td></tr><tr><td colspan=\"2\"><div class=\"hrule\"></div></td></tr>";
            foreach ($shortnews[$newsdate] as $newsitemtime => $newsitemdata) {
                $tmpSNCode .= "<tr><td width=\"45\" align=\"center\" valign=\"top\" rowspan=\"2\">" .$newsitemtime ."</td><td><strong>" .$newsitemdata['caption'] ."</strong> (" .$newsitemdata['username'] .")</td></tr><tr><td>" .$newsitemdata['text'] ."</td></tr>";
            }
            $tmpSNCode .= "<tr><td colspan=\"2\">&nbsp;</td></tr>";
        }
        $tmpSNCode .= "</table>";
        $rows .= $tmpSNCode;

        $smarty->assign('number', $overall_news);
        $templ_news_case_number_per_site = $howmany;

        $smarty->assign('rows', $rows);
        $smarty->assign('pages', $pages["html"] ."<strong><a href=\"index.php?mod=news\">" .t('Zur&uuml;ck') ."</a></strong>");
        $dsp->AddSingleRow($smarty->fetch('modules/news/templates/show_case.htm'));
        $dsp->AddContent();
    } else {
        if ($cfg["news_shorted"]== "0") {
            $dsp->NewContent(t('Neuigkeiten'), t('Hier siehst du aktuelle Neuigkeiten.'));
            if ($cfg["news_count"] == "") {
                $cfg["news_count"] = 5;
            }
            $pages = $func->page_split($_GET["news_page"], $cfg["news_count"], $overall_news, "index.php?mod=news&amp;action=show", "news_page");

            $get_news = $db->qry('SELECT n.*, UNIX_TIMESTAMP(n.date) AS date, u.userid, u.username FROM %prefix%news AS n LEFT JOIN %prefix%user AS u ON u.userid = n.poster ORDER BY n.top DESC, n.date DESC %plain%', $pages["sql"]);

            while ($row=$db->fetch_array($get_news)) {
                $priority = $row["priority"];
                if ($priority == 1) {
                    $type = important;
                } else {
                    $type = normal;
                }

                $smarty->assign('caption', $row["caption"]);
                $smarty->assign('username', $dsp->FetchUserIcon($row['userid'], $row["username"]));
                $smarty->assign('userid', $row["poster"]);

                if ($row['icon'] and $row['icon'] != 'none') {
                    $smarty->assign('icon', '<img src="ext_inc/news_icons/'.$row['icon'].'" vspace="2" align="right" />');
                } else {
                    $smarty->assign('icon', '');
                }

                $newsid = $row["newsid"];
                $howmany++;

                $smarty->assign('date', $func->unixstamp2date($row["date"], "daydatetime"));

                if ($cfg["news_html"] == 1) {
                    $text = $func->text2html($row["text"]);
                } else {
                    $text = $func->AllowHTML($row["text"]);
                }
                if ($row['link_1']) {
                    $text .= '<br><u>'. t('Links zum Thema:') .'</u><br><a href="'. $row['link_1'] .'" target="_blank">'. $row['link_1'] .'</a>';
                }
                if ($row['link_2']) {
                    $text .= '<br><a href="'. $row['link_2'] .'" target="_blank">'. $row['link_2'] .'</a>';
                }
                if ($row['link_3']) {
                    $text .= '<br><a href="'. $row['link_3'] .'" target="_blank">'. $row['link_3'] .'</a>';
                }
                $smarty->assign('text', $text);

        // GET NUMBER OF COMMENTS
                if ($cfg['news_comments_allowed']) {
                    $get_comments = $db->qry_first('SELECT count(*) as number FROM %prefix%comments WHERE relatedto_id=%int% AND relatedto_item=\'news\'', $newsid);

                    if ($get_comments["number"] >= 0) {
                        $smarty->assign('comments', "<a href=\"index.php?mod=news&amp;action=comment&amp;newsid=$newsid\">" .$get_comments["number"]." ". t('Kommentar(e)') ."</a>");
                    }
                }

        // Buttons
                $buttons = "";
                if ($auth["type"] > 1) {
                    $buttons .= $dsp->FetchIcon("index.php?mod=news&amp;action=change&amp;step=2&amp;newsid=$newsid", "edit") . " ";
                    $buttons .= $dsp->FetchIcon("index.php?mod=news&amp;action=delete&amp;step=2&amp;newsid=$newsid", "delete") . " ";
                }
                if ($cfg['news_comments_allowed']) {
                    $buttons .= $dsp->FetchIcon("index.php?mod=news&amp;action=comment&amp;newsid=$newsid", "quote") . " ";
                }
                $smarty->assign('buttons', $buttons);
                $rows .= $smarty->fetch("modules/news/templates/show_row_$type.htm");
            }
        } else {
            if ($cfg["news_complete"] == "") {
                $cfg["news_complete"] = 3;
            }
            $dsp->NewContent(t('Neuigkeiten'), t('Hier siehst du aktuelle Neuigkeiten.'));

            $get_news = $db->qry('SELECT n.*, UNIX_TIMESTAMP(n.date) AS date, u.userid, u.username FROM %prefix%news AS n LEFT JOIN %prefix%user AS u ON u.userid = n.poster ORDER BY n.top DESC, n.date DESC LIMIT %int%', $cfg["news_complete"]);
            while ($row=$db->fetch_array($get_news)) {
                $priority = $row["priority"];
                if ($priority == 1) {
                    $type = important;
                } else {
                    $type = normal;
                }

                $smarty->assign('caption', $row["caption"]);
                $smarty->assign('username', $dsp->FetchUserIcon($row['userid'], $row["username"]));
                $smarty->assign('userid', $row["poster"]);
                if ($row['icon'] and $row['icon'] != 'none') {
                    $smarty->assign('icon', '<img src="ext_inc/news_icons/'.$row['icon'].'" vspace="2" align="right" />');
                } else {
                    $smarty->assign('icon', '');
                }

                $newsid                                                     = $row["newsid"];
                $howmany++;
                $smarty->assign('date', $func->unixstamp2date($row["date"], "daydatetime"));

                if ($cfg["news_html"] == 1) {
                    $text = $func->text2html($row["text"]);
                } else {
                    $text = $func->AllowHTML($row["text"]);
                }
                if ($row['link_1']) {
                    $text .= '<br><u>'. t('Links zum Thema:') .'</u><br><a href="'. $row['link_1'] .'" target="_blank">'. $row['link_1'] .'</a>';
                }
                if ($row['link_2']) {
                    $text .= '<br><a href="'. $row['link_2'] .'" target="_blank">'. $row['link_2'] .'</a>';
                }
                if ($row['link_3']) {
                    $text .= '<br><a href="'. $row['link_3'] .'" target="_blank">'. $row['link_3'] .'</a>';
                }
                $smarty->assign('text', $text);

                if ($cfg['news_comments_allowed']) {
                    $get_comments = $db->qry_first('SELECT count(*) as number FROM %prefix%comments WHERE relatedto_id=%int% AND relatedto_item=\'news\'', $newsid);

                    if ($get_comments["number"] >= 0) {
                        $smarty->assign('comments', "<a href=\"index.php?mod=news&amp;action=comment&amp;newsid=$newsid\">" .$get_comments["number"]." Kommentar(e)</a>");
                    }
                }

        // Buttons
                $buttons = "";
                if ($auth["type"] > 1) {
                    $buttons .= $dsp->FetchIcon("index.php?mod=news&amp;action=change&amp;step=2&amp;newsid=$newsid", "edit") . " ";
                    $buttons .= $dsp->FetchIcon("index.php?mod=news&amp;action=delete&amp;step=2&amp;newsid=$newsid", "delete") . " ";
                }
                if ($cfg['news_comments_allowed']) {
                    $buttons .= $dsp->FetchIcon("index.php?mod=news&amp;action=comment&amp;newsid=$newsid", "quote") . " ";
                }
                $smarty->assign('buttons', $buttons);

                $rows .= $smarty->fetch("modules/news/templates/show_row_$type.htm");
            }

            $get_newsshorted = $db->qry("SELECT UNIX_TIMESTAMP(n.date) AS date, n.caption, n.text, u.username, n.newsid FROM %prefix%news AS n LEFT JOIN %prefix%user AS u ON u.userid = n.poster ORDER BY n.top DESC, n.date DESC LIMIT %plain%", $cfg["news_complete"] ."," .$cfg["news_shorted"]);
            while ($row=$db->fetch_array($get_newsshorted)) {
                $tmpDate = $func->unixstamp2date($row['date'], 'daydate');
                $shortnews[$tmpDate][$row[1]]['caption'] = "<a href=\"index.php?mod=news&amp;action=comment&amp;newsid=" .$row[5] ."\">" .$row[2] ."</a>";
                $shortnews[$tmpDate][$row[1]]['text'] = substr(strip_tags($func->AllowHTML($row[3])), 0, $cfg["news_shorted_length"]) ."...";
                $shortnews[$tmpDate][$row[1]]['username'] = $row[4];
            }
            $tmpDate = "";
            $tmpSNCode ="<table cellspacing=\"5\" width=\"100%\">";
            if ($shortnews) {
                foreach ($shortnews as $newsdate => $value) {
                    $tmpSNCode .= "<tr><td colspan=\"2\"><strong>$newsdate</strong></td></tr><tr><td colspan=\"2\"><div class=\"hrule\"></div></td></tr>";
                    foreach ($shortnews[$newsdate] as $newsitemtime => $newsitemdata) {
                        $tmpSNCode .= "<tr><td width=\"45\" align=\"center\" valign=\"top\" rowspan=\"2\">" .$newsitemtime ."</td><td><strong>" .$newsitemdata['caption'] ."</strong> (" .$newsitemdata['username'] .")</td></tr><tr><td>" .$newsitemdata['text'] ."</td></tr>";
                    }
                    $tmpSNCode .= "<tr><td colspan=\"2\">&nbsp;</td></tr>";
                }
            }
            $tmpSNCode .= "</table>";
            $smarty->assign('title', "<strong>" .t('&Auml;ltere Mitteilungen') ."</strong>");
            if ($row['link_1']) {
                $tmpSNCode .= '<br><u>'. t('Links zum Thema:') .'</u><br><a href="'. $row['link_1'] .'" target="_blank">'. $row['link_1'] .'</a>';
            }
            if ($row['link_2']) {
                $tmpSNCode .= '<br><a href="'. $row['link_2'] .'" target="_blank">'. $row['link_2'] .'</a>';
            }
            if ($row['link_3']) {
                $tmpSNCode .= '<br><a href="'. $row['link_3'] .'" target="_blank">'. $row['link_3'] .'</a>';
            }
            $smarty->assign('text', $tmpSNCode);
            $rows .= $smarty->fetch("modules/news/templates/show_row_shorted.htm");
            if ($cfg['news_shorted_archiv'] != 0) {
                $pages["html"] = "<strong><a href=\"index.php?mod=news&amp;action=show&amp;subaction=archive\">" .t('News Archiv') ."</a></strong>";
            }
        }

    // SET TEMPLATE CASE VARS
        $smarty->assign('number', $overall_news);
        $templ_news_case_number_per_site = $howmany;
        $smarty->assign('pages', $pages["html"]);

        $smarty->assign('rows', $rows);
        $dsp->AddSingleRow($smarty->fetch("modules/news/templates/show_case.htm"));
        $dsp->AddContent();
    }
}
