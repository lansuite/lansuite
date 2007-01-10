<?php
$LSCurFile = __FILE__;

// CHECK IF NEWSID IS VALID
$check = $db->query_first("SELECT caption FROM {$config["tables"]["news"]} WHERE newsid = '{$vars["newsid"]}'");
if($check["caption"] != "") { 

// GET NEWS DATA
$get_news = $db->query_first("SELECT n.*, u.username FROM {$config["tables"]["news"]} n LEFT JOIN {$config["tables"]["user"]} u ON u.userid = n.poster WHERE n.newsid = '{$vars["newsid"]}'");
$templ_news_single_row_priority = $get_news["priority"];
	
if($templ_news_single_row_priority == 1) { $news_type = "important"; } else { $news_type = "normal"; }
	
	$templ['news']['show']['single']['row'][$news_type]['info']['caption']      = $get_news["caption"];	
	$text                                                                       = $get_news["text"];
	$templ['news']['show']['single']['row'][$news_type]['control']['userid']    = $get_news["poster"];
	$templ['news']['show']['single']['row'][$news_type]['info']['username']     = $get_news["username"];
	$date                                                                       = $get_news["date"];
	
	$templ['news']['show']['single']['row'][$news_type]['info']['date']         = $func->unixstamp2date($date,"daydatetime");
  if ($cfg["news_html"] == 1) $text = $func->text2html($text);
	$templ['news']['show']['single']['row'][$news_type]['info']['text']         = $text;
	
	// SELECT ACTION TYPE
	if ($vars["mcact"] == "" OR $vars["mcact"] == "show") {

		$dsp->NewContent(t('Newsmeldung + Kommentare'), t('Hier können Sie diese News kommentieren'));

		// SET ADMIN-ONLY FUNCTION BUTTONS
		$templ['news']['show']['single']['row'][$news_type]['control']['buttons'] = "";
		if ($auth["type"] > 1) {
			$templ['news']['show']['single']['row'][$news_type]['control']['buttons'] .= $dsp->FetchButton("index.php?mod=news&action=change&came_from=1&step=2&newsid={$_GET["newsid"]}", "edit") . " ";
			$templ['news']['show']['single']['row'][$news_type]['control']['buttons'] .= $dsp->FetchButton("index.php?mod=news&action=delete&came_from=2&step=2&newsid={$_GET["newsid"]}", "delete") . " ";
		}
	
		$dsp->AddSingleRow($dsp->FetchModTpl("news", "show_single_row_$news_type"));
		$dsp->AddBackButton("index.php?mod=news&action=show", "");
		$dsp->AddContent();
	}

	include('inc/classes/class_mastercomment.php');
	new Mastercomment('news', $_GET['newsid']);

} else $func->error(t('Diese Newsmeldung existiert nicht'), '');
?>
