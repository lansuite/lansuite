<?php

include("modules/board/class_board.php");
$bfunc = new board_func;

$step 		= $_GET['step']; 
$tid 		= $_GET['tid'];
$fid 		= $_GET['fid'];

if ($fid == "") $fid = $bfunc->get_fid($tid);

// Check general errors
$row = $db->query_first("SELECT f.need_type FROM {$config["tables"]["board_forums"]} AS f WHERE f.fid = ". (int)$fid);
if ($_GET['tid']) $CRow = $db->query_first("SELECT t.closed FROM {$config["tables"]["board_threads"]} AS t WHERE t.tid = ". (int)$_GET['tid']);

if (!($row['need_type'] == 0 or $auth['type'] > 1 or (($row['need_type'] == 2 or $row['need_type'] == 1) and $auth['login'] == 1))) $func->error("ACCESS_DENIED");
elseif ($CRow['closed']) $func->information($lang['board']['locked'], "index.php?mod=board&action=thread&fid=$fid&tid=". $_GET['tid']);
else {

  $text 		= $_POST['text'];
  $caption 	= $_POST['caption'];
$sec->unlock("board_post");
  switch ($step) {
  	default:
  		#$sec->unlock("board_post");
  	break;

  	case 2:
  		if ($fid == "" and $tid == "") {
  			$func->error($lang['board']['thread_no_forumid'], "");
  			$step = 1;
  		}
  		if ($_POST['boardname'] == "" and $auth["login"] == 0) {
  			$board_error['board_name'] = $lang['board']['no_boardname'];
  			$step = 1;
  		}
  		if (strlen($_POST['boardname']) > 10 && $auth["login"] == 0) {
  			$board_error['board_name'] = $lang['board']['long_boardname'];
  			$step = 1;
  		}
  		if ($text == "") {
  			$board_error['text'] = $lang['board']['thread_no_text'];
  			$step = 1;
  		}
  		if ($_GET["tid"] == "" and $caption == "") {
  			$board_error['caption'] = $lang['board']['thread_no_caption'];
  			$step = 1;
  		}
  		if (strlen($text) > 5000) {
  			$board_error['text'] = $lang['board']['thread_max_text'];
  			$step = 1;
  		}
  		$posted = $_SESSION['posted'];

  		if ($_POST["preview_x"] or $_POST["preview_y"]) $step = 1;
  	break;

  } // switch



  switch ($step) {
  	default:
  		if ($_GET["tid"] == "")	{
  			$dsp->NewContent($lang['board']['thread_add_caption'],$lang['board']['thread_add_subcaption']);
  			$dsp->SetForm("index.php?mod=board&action=post&step=2&fid=$fid");
  		} else {
  			$dsp->NewContent(str_replace("%CAPTION%", $caption, $lang['board']['post_add_caption']), $lang['board']['post_add_subcaption']);
  			$dsp->SetForm("index.php?mod=board&action=post&step=2&fid=$fid&tid=$tid");
      }

  		if ($auth["login"] == 0){
  			$dsp->AddSingleRow("<font color=\"red\">" . $lang['board']['not_login']	. "</font>");
  			$dsp->AddTextFieldRow("boardname", $lang['board']['board_name'], $_POST['boardname'], $board_error['board_name']);
  		}

  		// Display Preview
  		if (($_POST["preview_x"] or $_POST["preview_y"]) and (!$board_error['text'])) {
  			$dsp->AddSingleRow("<b>{$lang['button']['preview']}</b>");
  			$dsp->AddDoubleRow($auth["username"] ."<br />". $func->unixstamp2date(time(), "daydatetime"), $func->db2text2html($_POST['text']));
  		}

  		if ($_GET["tid"] == "")	{
  			$dsp->AddTextFieldRow("caption", $lang['board']['thread_desc'], $caption, $board_error['caption']);
  		}

  		$dsp->AddTextAreaPlusRow("text", $lang['board']['thread_text'], $__POST['text'], $board_error['text'], 100, 20);
  		$dsp->AddFormSubmitRow("preview", "", "preview", false);
  		$dsp->AddFormSubmitRow("add");
  		$dsp->AddContent();

  		if ($_GET["tid"] != "")	{
  			// Show most recent posts
  			$dsp->AddSingleRow("<b>{$lang['board']['recent_posts']}</b>");
  			$posts = $db->query("SELECT post.comment, post.date, user.username FROM {$config['tables']['board_posts']} AS post
  				LEFT JOIN {$config['tables']['user']} AS user ON post.userid = user.userid
  				WHERE post.tid='$tid'
  				ORDER BY post.date DESC
  				LIMIT 5
  				");
  			while ($post = $db->fetch_array($posts)){
  				$dsp->AddDoubleRow($post["username"] ."<br />". $func->unixstamp2date($post["date"], "daydatetime"),
            $func->db2text2html($post["comment"]).'<br>'. $dsp->FetchIcon("javascript:InsertCode(document.dsp_form1.text, '[quote]". addslashes(str_replace('"', '', $post["comment"])) ."[/quote]')", "quote"));
  			}
  			$dsp->AddContent();
  		}
  	break;

  	case 2:
  		if (!$sec->locked("board_post")) {
  			$date = time();
  			if ($auth['login'] == 0) $text .= "<!-- " .$_POST['boardname'] . " -->";

        // When new thread
  			if ($_GET["tid"] == "")	{
  				$db->query("INSERT INTO {$config['tables']['board_threads']} SET
  						fid='$fid',
  						caption = '$caption'
  						");
  				$tid = $db->insert_id();

  				$backlink   = "index.php?mod=board&action=forum&fid=$fid";
  			} else $backlink = "index.php?mod=board&action=thread&tid=$tid";

  			// Insert post to table
  			$db->query("INSERT INTO {$config['tables']['board_posts']} SET
  					tid='$tid',
  					userid = '{$auth['userid']}',
  					comment = '$text',
  					date = '$date',
  					ip='{$_SERVER['REMOTE_ADDR']}'
  					");

  			// Update user-posts
  			$db->query("UPDATE {$config["tables"]["user"]} SET posts = posts + 1, changedate = changedate WHERE userid='{$auth['userid']}'");

  			// Send email-notifications to thread-subscribers
  			$path = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], "index.php"));

  			// Internet-Mail
  			$subscribers = $db->query("SELECT u.firstname, u.name, u.email FROM {$config["tables"]["board_bookmark"]} AS b
  				LEFT JOIN {$config["tables"]["user"]} AS u ON b.userid = u.userid
  				WHERE b.email = 1 and b.tid = '$tid'
  				");
  			while ($subscriber = $db->fetch_array($subscribers)){
  				$mail->create_inet_mail($subscriber["firstname"]." ".$subscriber["name"], $subscriber["email"], $cfg["board_subscribe_subject"], str_replace("%URL%", "http://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}{$path}index.php?mod=board&action=thread&tid=$tid", $cfg["board_subscribe_text"]), $cfg["sys_party_mail"]);
  			}
  			$db->free_result($subscribers);

  			// Send email-notifications to thread-subscribers
  			// Sys-Mail
  			$subscribers = $db->query("SELECT userid FROM {$config["tables"]["board_bookmark"]} AS b WHERE b.sysemail = 1 and b.tid = '$tid'");
  			while ($subscriber = $db->fetch_array($subscribers)) {
  				$mail->create_sys_mail($subscriber["userid"], $cfg["board_subscribe_subject"], str_replace("%URL%", "http://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}{$path}index.php?mod=board&action=thread&tid=$tid", $cfg["board_subscribe_text"]));
  			}
  			$db->free_result($subscribers);

  			// Print confirmation message
  			$func->confirmation($lang['board']['post_add_ok'], $backlink);
  			#$sec->lock("board_post");
  		}
  	break;
  }
}
?>
