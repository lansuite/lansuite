<?php
/*****************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	----------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		1.0
*	Filename: 		add.php
*	Module: 		News
*	Main editor: 		Michael@one-network.org
*	Last change: 		24.05.2004 15:24
*	Description: 		Adds news. Only accessible by admins.
*	Remarks: 		No Bugs reported, should be ready for release
*			
******************************************************************************/

if ($vars['action'] == "add") {
	if ($vars['step'] == "") $vars['step'] = 2;
}

switch($vars['step']) {
	case 3:
		//  ERRORS
		$i = strlen($vars["news_text"]);
		if($i > 100000) {
			$news_text_error = $lang["news"]["add_err_longtext"];
			$vars['step'] = 2;
		}
		if($vars["news_caption"] == "") { 
			$news_caption_error = $lang["news"]["add_err_noheadline"];
			$vars['step'] = 2;
		}
		if($vars["news_text"] == "") {
			$news_text_error = $lang["news"]["add_err_notext"];
			$vars['step'] = 2;
		}
	break;
}	

switch ($vars['step']) {
	default:
		$mastersearch = new MasterSearch($vars, "index.php?mod=news&action=change", "index.php?mod=news&action=change&step=2&newsid=", "");
		$mastersearch->LoadConfig("news", $lang["news"]["change_ms_caption"], $lang["news"]["change_ms_subcaption"]);
		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();
		$templ['index']['info']['content'] .= $mastersearch->GetReturn();
	break; 

	case 2:
		$dsp->NewContent($lang["news"]["add_caption"], $lang["news"]["add_subcaption"]);

		if ($vars['action'] == "change") {
			$get_data = $db->query_first("SELECT newsid, caption, text, priority FROM {$config["tables"]["news"]} WHERE newsid = '{$vars['newsid']}'");

			if($vars['newsid'] != "") {
				if($vars['news_caption'] == "") $vars['news_caption'] = $get_data['caption'];
				if($vars['news_text'] == "") $vars['news_text']    = $get_data['text'];
				if($vars['news_priority'] == "") $vars['news_priority'] = $get_data['priority'];
			} else {
				$func->error($lang["news"]["change_err_notexist"], "index.php?mod=news&action=change");
				break;
			}
		}
		unset($_SESSION['add_blocker_news']);

		$dsp->SetForm("index.php?mod=news&action={$vars['action']}&step=3&newsid={$vars['newsid']}");
		$dsp->AddTextFieldRow("news_caption", $lang["news"]["add_headline"], $vars["news_caption"], $news_caption_error);
		$dsp->AddTextAreaPlusRow("news_text", $lang["news"]["add_text"], $vars["news_text"], $news_text_error, "", "", "", 100000);

		$priority_array = array("0" => $lang["news"]["add_normal"],
			"1" => $lang["news"]["add_important"]
			);
		$t_array = array();
		reset ($priority_array);
		while (list ($key, $val) = each ($priority_array)) {
			($vars['news_priority'] == $key) ? $selected = "selected" : $selected = "";
			array_push ($t_array, "<option $selected value=\"$key\">$val</option>");
		}
		$dsp->AddDropDownFieldRow("news_priority", $lang["news"]["add_priority"], $t_array, "", 1);

		$dsp->AddFormSubmitRow("add");
		$dsp->AddBackButton("index.php?mod=news", "news/form"); 
		$dsp->AddContent();
	break;
	
	case 3:
		if($_SESSION["add_blocker_news"]) $func->error("NO_REFRESH", "");
		else {
			$_SESSION['add_blocker_news'] = 1;

			if ($vars['action'] == "add") {
				$current_date = time();
				$add_it = $db->query("INSERT INTO {$config["tables"]["news"]} SET
							caption = '{$vars["news_caption"]}',
							text = '{$vars["news_text"]}',
							poster = '{$_SESSION["auth"]["userid"]}',
							priority = '{$vars["news_priority"]}',
							date = '$current_date'
							");

				if($add_it) { 
					$func->confirmation($lang["news"]["add_success"], "index.php?mod=news");
					$func->log_event(str_replace("%NAME%", $vars["news_caption"], $lang["news"]["add_log"]), 1, "News");
				}
			} else {
				$change_it = $db->query("UPDATE {$config["tables"]["news"]} SET
							caption = '{$vars["news_caption"]}',
							text = '{$vars["news_text"]}',
							priority = '{$vars["news_priority"]}'
							WHERE newsid = '{$vars["newsid"]}'
							");

				if($change_it) {
					$func->confirmation($lang["news"]["change_success"], "index.php?mod=news");
					$func->log_event(str_replace("%NAME%", $vars["news_caption"], $lang["news"]["change_log"]), 1, "News");
				}
			}

			// News-Feed schreiben
			$output = '<?xml version="1.0" encoding="ISO-8859-1"?>'."\r\n";

			$channel = $xml->write_tag("title", $_SESSION['party_info']['name'], 2);
			$channel .= $xml->write_tag("link", $cfg["sys_partyurl"], 2);
			$channel .= $xml->write_tag("description", $cfg["news_description"], 2);
			$channel .= $xml->write_tag("language", "de-de", 2);
			$channel .= $xml->write_tag("copyright", $cfg["news_copyright"], 2);

			$image = $xml->write_tag("url", $cfg["news_logourl"], 3);
			$image .= $xml->write_tag("title", $_SESSION['party_info']['name'] ." - Logo", 3);
			$image .= $xml->write_tag("link", $cfg["sys_partyurl"], 3);
			$channel .= $xml->write_master_tag("image", $image, 2);

			$get_news = $db->query("SELECT n.*, u.username, u.email FROM	{$config["tables"]["news"]} n
					LEFT JOIN {$config["tables"]["user"]} u ON u.userid = n.poster
					ORDER BY n.date DESC");
			while($news = $db->fetch_array($get_news)) {
				$item = $xml->write_tag("title", $news["caption"], 3);
				$item .= $xml->write_tag("description", substr(strip_tags($news["text"]), 0, 150), 3);
				$item .= $xml->write_tag("author", "{$news['email']} ({$news['username']})", 3);
				$item .= $xml->write_tag("pubDate", date("D, j M Y H:i:s O", $news['date']), 3);

				$path = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], "index.php"));
				$item .= $xml->write_tag("link", "http://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}{$path}index.php?mod=news&amp;action=comment&amp;newsid=". $news['newsid'], 3);
				$channel .= $xml->write_master_tag("item", $item, 2);
			}
			$db->free_result($get_news);

			$rss = $xml->write_master_tag("channel", $channel, 1);
			$output .= $xml->write_master_tag("rss version=\"0.91\"", $rss, 0);

			if (is_writable("ext_inc/newsfeed/")) {
				if ($fp = @fopen("ext_inc/newsfeed/news.xml", "w")) {
					if (@fwrite($fp, $output)) {
						$func->log_event($lang["news"]["feed_log"], 1, $lang["news"]["feed_log"]);
					} else $func->log_event($lang["news"]["feed_no_write_file"], 2, $lang["news"]["feed_log"]);
				@fclose($fp);
				} else $func->log_event($lang["news"]["feed_no_open"], 2, $lang["news"]["feed_log"]);
			} else $func->log_event($lang["news"]["feed_no_write"], 2, $lang["news"]["feed_log"]);
			// Ende News-Feed schreiben
		}
	break;
}
?>
