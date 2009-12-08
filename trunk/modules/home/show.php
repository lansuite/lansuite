<?php
$func->DeleteOldReadStates();

if ($auth["type"] == 1 or $auth["type"] == 2 or $auth["type"] == 3) $home_page = $cfg["home_login"];
else $home_page = $cfg["home_logout"];

switch ($home_page) {
	// Show overview
	default:
		$dsp->NewContent(t('Startseite'), t('Willkommen! Hier sehen Sie eine kleine Übersicht der wichtigsten Aktivitäten.'));

    $ModOverviews = array();
    if ($cfg['home_item_cnt_news'] and in_array('news', $ActiveModules)) $ModOverviews[] = 'news';
    if ($cfg['home_item_cnt_board'] and in_array('board', $ActiveModules)) $ModOverviews[] = 'board';
    if ($cfg['home_item_cnt_server'] and in_array('server', $ActiveModules)) $ModOverviews[] = 'server';
    if ($cfg['home_item_cnt_bugtracker'] and in_array('bugtracker', $ActiveModules)) $ModOverviews[] = 'bugtracker';
    if ($cfg['home_item_cnt_wiki'] and in_array('wiki', $ActiveModules)) $ModOverviews[] = 'wiki';
    if ($cfg['home_item_cnt_tournament2'] and in_array('tournament2', $ActiveModules)) $ModOverviews[] = 'tournament';
    if ($cfg['home_item_cnt_partylist'] and in_array('partylist', $ActiveModules)) $ModOverviews[] = 'partylist';
    if ($cfg['home_item_cnt_comments']) $ModOverviews[] = 'mastercomment';
    if ($cfg['home_item_cnt_mail'] and in_array('mail', $ActiveModules) and $auth['login']) $ModOverviews[] = 'mail';
    if ($cfg['home_item_cnt_poll'] and in_array('poll', $ActiveModules)) $ModOverviews[] = 'poll';
		if (in_array('stats', $ActiveModules)
      and ($party->count > 0 or $auth['type'] >= 2)
      and (in_array('troubleticket', $ActiveModules)))
      $ModOverviews[] = 'stats';

    $z = 0;
    foreach($ModOverviews as $ModOverview) {
      if ($z % 2 == 0) {
        $MainContent .= '<ul class="Line">';
        $MainContent .= '<li class="LineLeftHalf">';
      } else $MainContent .= '<li class="LineRightHalf">';
      $smarty->assign('text2', '');
      include('modules/home/'. $ModOverview .'.inc.php');
      $smarty->assign('content', $content);
      $MainContent .= $smarty->fetch('modules/home/templates/show_item.htm');
      $MainContent .= '</li>';
      if ($z % 2 == 1) $MainContent .= '</ul>';
      $z++;
    }
    if ($z % 2 == 1) $MainContent .= '<li class="LineRightHalf">&nbsp;</li></ul>';

		if ($party->count > 1 && $cfg['display_change_party']) $party->get_party_dropdown_form();
	break;

	// Show News
	case 1:
		include ("modules/news/show.php");
		if ($party->count > 1 && $cfg['display_change_party']) $party->get_party_dropdown_form();
	break;
	
	// Show Logout-Text
	case 2:
		$dsp->NewContent(t('Startseite'), t('Willkommen! Zum Einloggen verwenden Sie bitte, die Login-Box auf der rechten Seite'));
		$logout_hometext = file_get_contents("ext_inc/home/logout.txt");
		$dsp->AddSingleRow($func->text2html($logout_hometext));
		$dsp->AddHRuleRow();

		$dsp->AddSingleRow(t("Die letzten News:"));
		$get_news_caption = $db->qry("SELECT newsid, caption FROM	%prefix%news ORDER BY date DESC LIMIT 3");
		$i = 1;
		while($row=$db->fetch_array($get_news_caption)) {
			$dsp->AddDoubleRow("", "<a href=\"index.php?mod=news&action=show&newsid={$row["newsid"]}\">{$row["caption"]}</a>");
			$i++;
		}
		$db->free_result($get_news_caption);

		if ($party->count > 1 && $cfg['display_change_party']) $party->get_party_dropdown_form();
  break;
}
?>
