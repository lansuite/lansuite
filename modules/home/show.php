<?php
if (!$cfg['home_item_count']) $cfg['home_item_count'] = 8;

if ($auth["type"] == 1 or $auth["type"] == 2 or $auth["type"] == 3) $home_page = $cfg["home_login"];
else $home_page = $cfg["home_logout"];

switch ($home_page) {
	// Show overview
	default:
		$dsp->NewContent(t('Startseite'), t('Willkommen! Hier sehen Sie eine kleine Übersicht der wichtigsten Aktivitäten.'));

    $ModOverviews = array();
    if (in_array('news', $ActiveModules)) $ModOverviews[] = 'news';
    if (in_array('board', $ActiveModules)) $ModOverviews[] = 'board';
    if (in_array('mail', $ActiveModules)) $ModOverviews[] = 'mail';
    if (in_array('server', $ActiveModules)) $ModOverviews[] = 'server';
    if (in_array('poll', $ActiveModules)) $ModOverviews[] = 'poll';
    if (in_array('bugtracker', $ActiveModules)) $ModOverviews[] = 'bugtracker';
    if (in_array('tournament2', $ActiveModules)) $ModOverviews[] = 'tournament';
    if (in_array('partylist', $ActiveModules)) $ModOverviews[] = 'partylist';
		if (in_array('stats', $ActiveModules)
      and ($party->count > 0 or $auth['type'] >= 2)
      and (in_array('troubleticket', $ActiveModules) or in_array('rent', $ActiveModules)))
      $ModOverviews[] = 'stats';

    include_once('modules/home/templates/home.php');

		if ($party->count > 1) $party->get_party_dropdown_form();
	break;

	// Show News
	case 1:
		if ($party->count > 1) $party->get_party_dropdown_form();
		include ("modules/news/show.php");
	break;
	
	// Show Logout-Text
	case 2:
		$dsp->NewContent(t('Startseite'), t('Willkommen! Zum Einloggen verwenden Sie bitte, die Login-Box auf der rechten Seite'));

		$logout_hometext = file_get_contents("ext_inc/home/logout.txt");
		$templ['home']['logout']['show']['info']['text']  = $func->text2html($logout_hometext);

		$dsp->AddSingleRow($func->text2html($logout_hometext));

		$dsp->AddHRuleRow($func->text2html($logout_hometext));
		$dsp->AddSingleRow("Die letzten News:");

		$get_news_caption = $db->query("SELECT newsid, caption FROM	{$config["tables"]["news"]} ORDER BY date DESC LIMIT 3");
		$i = 1;
		while($row=$db->fetch_array($get_news_caption)) {
			$dsp->AddDoubleRow("", "<a href=\"index.php?mod=news&action=show&newsid={$row["newsid"]}\">{$row["caption"]}</a>");
			$i++;
		}
		$db->free_result($get_news_caption);

		if ($party->count > 1) $party->get_party_dropdown_form();
  break;
}
?>