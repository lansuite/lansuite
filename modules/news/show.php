<?php

// COUNT NEWS
$get_amount = $db->query_first("SELECT count(*) as number FROM {$config["tables"]["news"]}");
$overall_news = $get_amount["number"];

if($overall_news == 0) $func->no_items("Newsmeldungen", "", "rlist");
else {
	$dsp->NewContent($lang["news"]["show_caption"], $lang["news"]["show_subcaption"]);

	// SET PAGE SPLIT
	if ($cfg["news_count"] == "") $cfg["news_count"] = 5;
	$pages = $func->page_split($vars["news_page"], $cfg["news_count"], $overall_news, "index.php?mod=news&action=show", "news_page");

	//GET NEWS DATA AND ORDER NEWS
	$get_news = $db->query("SELECT n.*, u.username FROM	{$config["tables"]["news"]} n LEFT JOIN {$config["tables"]["user"]} u ON u.userid = n.poster ORDER BY n.top DESC, n.date DESC {$pages["sql"]}");

	while($row=$db->fetch_array($get_news)) {
		$priority = $row["priority"];

		// SELECT NEWS PRIORITY
		if($priority == 1) { $type = important; } else { $type = normal; }
		$templ['news']['show']['row'][$type]['info']['caption']     = $row["caption"];
		$text                                                       = $row["text"];
		$templ['news']['show']['row'][$type]['info']['username']    = $row["username"];
		$templ['news']['show']['row'][$type]['control']['userid']   = $row["poster"];
		if ($row['icon']) $templ['news']['show']['row']['normal']['info']['icon'] =	'<img src="ext_inc/news_icons/'.$row['icon'].'" vspace="2" align="right" />';
		else $templ['news']['show']['row']['normal']['info']['icon'] = '';

		$newsid                                                     = $row["newsid"];
		$date                                                       = $row["date"];
		$howmany++;

		$templ['news']['show']['row'][$type]['info']['date']        = $func->unixstamp2date($date,"daydatetime");

		if (!$cfg["news_html"]) $text = $func->text2html($text);
		$templ['news']['show']['row'][$type]['info']['text']        = $text;

		// GET NUMBER OF COMMENTS
		$get_comments = $db->query_first("SELECT count(*) as number FROM {$config["tables"]["comments"]} WHERE relatedto_id=$newsid AND relatedto_item='news'");
		
		if ($get_comments["number"] >= 0) { $templ['news']['show']['row'][$type]['info']['comments'] = $get_comments["number"]." Kommentar(e)"; }

		// Buttons
		$templ['news']['show']['row'][$type]['control']['buttons'] = "";
		if ($auth["type"] > 1) {
			$templ['news']['show']['row'][$type]['control']['buttons'] .= $dsp->FetchButton("index.php?mod=news&action=change&step=2&newsid=$newsid", "edit") . " ";
			$templ['news']['show']['row'][$type]['control']['buttons'] .= $dsp->FetchButton("index.php?mod=news&action=delete&step=2&newsid=$newsid", "delete") . " ";
		}
		$templ['news']['show']['row'][$type]['control']['buttons'] .= $dsp->FetchButton("index.php?mod=news&action=comment&newsid=$newsid", "comments") . " ";

		$templ['news']['show']['case']['control']['rows'] .= $dsp->FetchModTpl("news", "show_row_$type");
	} // CLOSE WHILE

	// SET TEMPLATE CASE VARS
	$templ['news']['case']['number'] = $overall_news;
	$templ_news_case_number_per_site = $howmany;
	$templ['news']['show']['case']['control']['pages'] = $pages["html"];

	$dsp->AddSingleRow($dsp->FetchModTpl("news", "show_case"));
	$dsp->AddContent();
}
?>
