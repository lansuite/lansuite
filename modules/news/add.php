<?php

$dsp->NewContent($lang["news"]["add_caption"], $lang["news"]["add_subcaption"]);

include_once('inc/classes/class_masterform.php');
$mf = new masterform();

// Name
$mf->AddField($lang['news']['add_headline'], 'caption');
$mf->AddField($lang['news']['add_icon'], 'icon', FIELD_OPTIONAL, '', 'ext_inc/news_icons');
$mf->AddField($lang['news']['add_text'], 'text', '', '', HTML_ALLOWED);
$selections = array();
$selections['0'] = $lang['news']['add_normal'];
$selections['1'] = $lang['news']['add_important'];
$mf->AddField($lang['news']['add_priority'], 'priority', FIELD_OPTIONAL, '', $selections);

if (!$_GET['newsid']) {
  $mf->AddFix('date', time());
  $mf->AddFix('poster', $auth['userid']);
}

if ($mf->SendForm('index.php?mod=news&action='. $_GET['action'], 'news', 'newsid', $_GET['newsid'])){

  // Log
  if ($_GET['newsid']) $func->log_event(str_replace('%NAME%', $_POST['caption'], $lang['news']['change_log']), 1, 'News');
  else $func->log_event(str_replace('%NAME%', $_POST['caption'], $lang['news']['add_log']), 1, 'News');

	// News-Feed schreiben
	$output = '<?xml version="1.0" encoding="UTF-8"?'.'>'."\r\n";

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
?>