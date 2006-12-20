<?php

if ($auth["type"] == 1 or $auth["type"] == 2 or $auth["type"] == 3) $home_page = $cfg["home_login"];
else $home_page = $cfg["home_logout"];

switch ($home_page) {
	// Show overview
	default:
		$dsp->NewContent($lang["home"]["in_caption"], $lang["home"]["in_subcaption"]);

		$z = 1;

    if (in_array('news', $ActiveModules)) {
      include('modules/home/news.inc.php');
      $templ['home']['show']['case']['control']['item_'.$z] .= $dsp->FetchModTpl("home", "show_item");
      $z++;
		}

		if (in_array('board', $ActiveModules)) {
      include('modules/home/board.inc.php');
      $templ['home']['show']['case']['control']['item_'.$z] .= $dsp->FetchModTpl("home", "show_item");
      $z++;
		}

		if (in_array('server', $ActiveModules)) {
      include('modules/home/server.inc.php');
      $templ['home']['show']['case']['control']['item_'.$z] .= $dsp->FetchModTpl("home", "show_item");
      $z++;
		}

		if (in_array('poll', $ActiveModules)) {
      include('modules/home/poll.inc.php');
      $templ['home']['show']['case']['control']['item_'.$z] .= $dsp->FetchModTpl("home", "show_item");
      $z++;
		}

    if (in_array('tournament2', $ActiveModules)) {
      include('modules/home/tournament.inc.php');
      $templ['home']['show']['case']['control']['item_'.$z] .= $dsp->FetchModTpl("home", "show_item");
      $z++;
		}

		if (in_array('stats', $ActiveModules)) {
      include('modules/home/stats.inc.php');
      $templ['home']['show']['case']['control']['item_'.$z] .= $dsp->FetchModTpl("home", "show_item");
      $z++;
		}

		$dsp->AddSingleRow($dsp->FetchModTpl("home", "show_case"));
		if ($party->count > 1) $party->get_party_dropdown_form();
		$dsp->AddContent();
	break;

	// Show News
	case 1:
		if ($party->count > 1) $party->get_party_dropdown_form();
		include ("modules/news/show.php");
	break;
	
	// Show Logout-Text
	case 2:
		$dsp->NewContent($lang["home"]["off_caption"], $lang["home"]["off_subcaption"]);

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
		$dsp->AddContent();
  break;
}
?>
