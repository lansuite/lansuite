<?php

// Delete old read states
$db->qry('DELETE FROM %prefix%lastread WHERE DATEDIFF(NOW(), date) > 7 AND tab != "task"');

if ($auth["type"] == 1) {
    $home_page = $cfg["home_login"];
} elseif ($auth["type"] == 2 or $auth["type"] == 3) {
    $home_page = $cfg["home_admin"];
} else {
    $home_page = $cfg["home_logout"];
}

switch ($home_page) {
    // Show overview
    default:
        $dsp->NewContent($cfg['sys_page_title'], t('Übersicht der neusten Aktivitäten auf %1.', $framework->internal_url_query['host']));

        $z = 0;

        $plugin = new plugin('home');
        while (list($caption, $inc) = $plugin->fetch()) {
            if ($caption == 'install') {
                $caption = 'comments';
            }
            if ($cfg['home_item_cnt_'.$caption]
            or ($caption == 'party' and $party->count > 0)
            or ($caption == 'troubleticket' and $auth['type'] >= 2)
            or ($caption == 'rent' and $auth['type'] >= 2)
            or ($caption == 'task' and $auth['type'] >= 2)
                ) {
                    $content = '';
                    include($inc);
                if ($content) {
                    if ($z % 2 == 0) {
                        $MainContent .= '<ul class="Line">';
                        $MainContent .= '<li class="LineLeftHalf">';
                    } else {
                        $MainContent .= '<li class="LineRightHalf">';
                    }
                        $smarty->assign('text2', '');
                        $smarty->assign('content', $content);
                        $MainContent .= $smarty->fetch('modules/home/templates/show_item.htm');
                        $MainContent .= '</li>';
                    if ($z % 2 == 1) {
                        $MainContent .= '</ul>';
                    }
                    $z++;
                }
            }
        }

        if ($z % 2 == 1) {
            $MainContent .= '<li class="LineRightHalf">&nbsp;</li></ul>';
        }

        if ($party->count > 1 && $cfg['display_change_party']) {
            $party->get_party_dropdown_form();
        }
        break;

    // Show News
    case 1:
        include("modules/news/show.php");
        if ($party->count > 1 && $cfg['display_change_party']) {
            $party->get_party_dropdown_form();
        }
        break;
    
    // Show Logout-Text
    case 2:
        $dsp->NewContent(t('Startseite'), t('Willkommen! Zum Einloggen verwende bitte, die Login-Box auf der rechten Seite'));
        $logout_hometext = file_get_contents("ext_inc/home/logout.txt");
        $dsp->AddSingleRow($func->text2html($logout_hometext));
        $dsp->AddHRuleRow();

        $dsp->AddSingleRow(t("Die letzten News:"));
        $get_news_caption = $db->qry("SELECT newsid, caption FROM	%prefix%news ORDER BY date DESC LIMIT 3");
        $i = 1;
        while ($row=$db->fetch_array($get_news_caption)) {
            $dsp->AddDoubleRow("", "<a href=\"index.php?mod=news&action=show&newsid={$row["newsid"]}\">{$row["caption"]}</a>");
            $i++;
        }
        $db->free_result($get_news_caption);

        if ($party->count > 1 && $cfg['display_change_party']) {
            $party->get_party_dropdown_form();
        }
        break;
}
