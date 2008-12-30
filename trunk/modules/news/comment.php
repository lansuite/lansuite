<?php
// CHECK IF NEWSID IS VALID
$check = $db->qry_first('SELECT caption FROM %prefix%news WHERE newsid = %int%', $_GET['newsid']);
if ($check["caption"] != "") {

  $func->SetRead('news', $_GET['newsid']);
  
  // GET NEWS DATA
  $get_news = $db->qry_first('SELECT n.*, u.userid, u.username FROM %prefix%news n LEFT JOIN %prefix%user u ON u.userid = n.poster WHERE n.newsid = %int%', $_GET['newsid']);
  $templ_news_single_row_priority = $get_news["priority"];
  	
  if ($templ_news_single_row_priority == 1) { $news_type = "important"; } else { $news_type = "normal"; }
	
	$templ['news']['show']['single']['row'][$news_type]['info']['caption']      = $get_news["caption"];	
	$templ['news']['show']['single']['row'][$news_type]['control']['userid']    = $get_news["poster"];
	$templ['news']['show']['single']['row'][$news_type]['info']['username']     = $get_news["username"] .' '. $dsp->FetchUserIcon($get_news['userid']);
	$date                                                                       = $get_news["date"];
	
	$templ['news']['show']['single']['row'][$news_type]['info']['date']         = $func->unixstamp2date($date,"daydatetime");

  $text = '';
	if ($auth["type"] > 1) {
    $text .= $dsp->FetchIcon("index.php?mod=news&action=delete&came_from=2&step=2&newsid={$_GET["newsid"]}", "delete", '', '', 'right');
    $text .= $dsp->FetchIcon("index.php?mod=news&action=change&came_from=1&step=2&newsid={$_GET["newsid"]}", "edit", '', '', 'right');
  }
  if ($cfg["news_html"] == 1) $get_news['text'] = $func->text2html($get_news['text']);
  else $get_news['text'] = $func->AllowHTML($get_news['text']);
  $text .= $get_news['text'];
	$templ['news']['show']['single']['row'][$news_type]['info']['text'] .= $text;

	// SELECT ACTION TYPE
	if ($_GET["mcact"] == "" OR $_GET["mcact"] == "show") {

		$dsp->NewContent(t('Newsmeldung + Kommentare'), t('Hier können Sie diese News kommentieren'));
		$dsp->AddSingleRow($dsp->FetchModTpl("news", "show_single_row_$news_type"));
		$dsp->AddSingleRow($dsp->FetchSpanButton(t('Newsübersicht'), "index.php?mod=news&action=show"));
	}

	include('inc/classes/class_mastercomment.php');
	new Mastercomment('news', $_GET['newsid'], array('news' => 'newsid'));

} else $func->error(t('Diese Newsmeldung existiert nicht'), '');
?>