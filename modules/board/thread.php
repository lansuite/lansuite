<?php
$LSCurFile = __FILE__;

include("modules/board/class_board.php");
$bfunc = new board_func;

// Exec Admin-Functions
if ($auth['type'] >= 2) switch ($_GET['step']) {
  case 10:
    $bfunc->CloseThread($_GET['tid']);
  break;
  case 11:
    $bfunc->OpenThread($_GET['tid']);
  break;
}

$tid = (int)$_GET["tid"];
$list_type = $auth['type'] + 1;
$thread = $db->query_first("SELECT t.fid, t.caption, t.closed, f.name AS ForumName, f.need_type FROM {$config["tables"]["board_threads"]} AS t
  LEFT JOIN {$config["tables"]["board_forums"]} AS f ON t.fid = f.fid
  WHERE t.tid=$tid AND (f.need_type <= '{$list_type}')");

if ($thread['caption'] == '' and $tid) $func->error($lang['board']['no_posts'], '');
elseif ($thread['caption'] != '') {

	$fid = $thread["fid"];

	// Mark thread read
	$search_read = $db->query_first("SELECT 1 AS found FROM {$config["tables"]["board_read_state"]} WHERE tid = $tid and userid = '{$auth["userid"]}'");
	if ($search_read["found"]) $db->query_first("UPDATE {$config["tables"]["board_read_state"]} SET last_read = ". time() ." WHERE tid = $tid and userid = '{$auth["userid"]}'");
	else $db->query_first("INSERT INTO {$config["tables"]["board_read_state"]} SET last_read = ". time() .", tid = $tid, userid = '{$auth["userid"]}'");

  // Tread Headline
	$hyperlink = '<a href="%s" class="menu">%s</a>';
	$overview_capt = sprintf($hyperlink, "index.php?mod=board", $lang['board']['board']);
	$forum_capt = sprintf($hyperlink, "index.php?mod=board&action=forum&fid=$fid", $thread['ForumName']);
	$dsp->NewContent($func->db2text($thread["caption"]), "$overview_capt - $forum_capt - ". $func->db2text($thread["caption"]));

	// Generate Thread-Buttons
	$buttons = '';
#" ". $dsp->FetchIcon("index.php?mod=board&action=post&fid=$fid", "add") .
#	if (($auth["login"] == 1 and $thread['need_type'] >= 1) or $thread['need_type'] == 0 or $auth['type'] > 1) $buttons .= ' '. $dsp->FetchIcon("index.php?mod=board&action=post&tid=$tid", "add");
	if ($auth["type"] > 1) {
    if ($thread['closed']) $buttons .= ' '. $dsp->FetchIcon("index.php?mod=board&action=thread&step=11&tid=$tid", "unlocked");
    else $buttons .= ' '. $dsp->FetchIcon("index.php?mod=board&action=thread&step=10&tid=$tid", "locked");
    $buttons .= ' '. $dsp->FetchIcon("index.php?mod=board&action=edit&mode=delete&tid=$tid", "delete");
  }

	$query = $db->query("SELECT pid, comment, userid, date, ip FROM {$config['tables']['board_posts']} WHERE tid='$tid' order by date");
	$count_entrys = $db->num_rows($query);
	
	if ($_GET['gotopid']) {
    $z = 0;
  	$query2 = $db->query("SELECT pid FROM {$config['tables']['board_posts']} WHERE tid='$tid'");
  	while ($row2 = $db->fetch_array($query2)) {
      if ($row2['pid'] == $_GET['gotopid']) break;
      $z++;
  	}
  	$db->free_result($query2);
  	$_GET['posts_page'] = (string)floor($z / $cfg['board_max_posts']);
  }

  // Page select
	if ($count_entrys > $cfg['board_max_posts']){
		$pages = $func->page_split($_GET['posts_page'], $cfg['board_max_posts'], $count_entrys, "index.php?mod=board&action=thread&tid=$tid", "posts_page");
		$query = $db->query("SELECT pid, comment, userid, date FROM {$config['tables']['board_posts']} WHERE tid='$tid' order by date {$pages['sql']}");
	}
  $dsp->AddSingleRow($buttons.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$pages['html']);

  $z = 0;
	while ($row = $db->fetch_array($query)){
		$pid = $row["pid"];

		$templ['board']['thread']['case']['info']['post']['pid'] 		= $pid;
		$templ['board']['thread']['case']['info']['post']['text'] 		= $func->db2text2html($row["comment"]);
		$templ['board']['thread']['case']['info']['post']['date'] 		= $func->unixstamp2date($row["date"], "datetime");

		if ($row['userid'] == 0){
			preg_match("@<!--(.*)-->@",$row['comment'],$tmp);
			$userdata['username'] = $lang['board']['guest_prefix'] . "_" . trim($tmp[1]);
			$userdata['type'] = $lang['board']['guest_prefix'];
			$userdata["avatar"] = "";
			$userdata["rank"] =  $lang['board']['guest_prefix'];
			$userdata["posts"] = "";
			$userdata["signature"] = "";
		} else $userdata = $bfunc->getuserinfo($row["userid"]);

		$templ['board']['thread']['case']['info']['post']['poster']['username'] 	= $userdata["username"] .' '. $dsp->FetchUserIcon($row['userid']);;
		$templ['board']['thread']['case']['info']['post']['poster']['type'] = $userdata["type"];
		if ($auth['type'] >= 2) $templ['board']['thread']['case']['info']['post']['poster']['type'] .= '<br />IP: <a href="http://www.dnsstuff.com/tools/whois.ch?ip='. $row['ip'] .'" target="_blank">'. $row['ip'] .'</a>';
		if (!$cfg['board_ranking'])$templ['board']['thread']['case']['info']['post']['poster']['rank'] = ''; 
    else $templ['board']['thread']['case']['info']['post']['poster']['rank'] 		= $lang['board']['rank'] . ": " . $userdata["rank"];
		$templ['board']['thread']['case']['info']['post']['poster']['posts'] 		= $lang['board']['posts'] . ": " . $userdata["posts"];
		$templ['board']['thread']['case']['info']['post']['poster']['avatar']		= $userdata["avatar"];
		$templ['board']['thread']['case']['info']['post']['poster']['signature'] = '';
		if ($userdata["signature"]) $templ['board']['thread']['case']['info']['post']['poster']['signature'] 	= '<hr size="1" width="100%" color="cccccc">'.$func->db2text2html($userdata["signature"]);

		$templ['board']['thread']['case']['info']['post']['edit'] = '';
		if ($auth['type'] > 1)
			$templ['board']['thread']['case']['info']['post']['edit'] .= $dsp->FetchIcon("index.php?mod=board&action=edit&mode=pdelete&pid=$pid&gotopid=$pid", "delete", '', '', 'right');
		if ($auth['type'] > 1 or $row["userid"] == $auth["userid"])
			$templ['board']['thread']['case']['info']['post']['edit'] .= $dsp->FetchIcon("index.php?mod=board&action=thread&fid=$fid&tid=$tid&pid=$pid&gotopid=$pid", "edit", '', '', 'right');
		$templ['board']['thread']['case']['info']['post']['edit'] .= $dsp->FetchIcon("javascript:InsertCode(document.dsp_form1.comment, '[quote]". addslashes(str_replace('"', '', $row["comment"])) ."[/quote]')", "quote", '', '', 'right');;

    if ($z % 2 == 0) $templ['board']['highlighted'] = '';
    else $templ['board']['highlighted'] = '_highlighted';
    
		$templ['board']['forum']['case']['control']['rows'] .= $dsp->AddModTpl("board", "board_thread_row");
		$z++;
	}

	if ($_SESSION['threadview'] != $tid) $db->query("UPDATE {$config["tables"]["board_threads"]} SET views=views+1 WHERE tid='$tid'");
	$_SESSION['threadview'] = $tid;

  $dsp->AddSingleRow($buttons.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$pages['html']);
	$dsp->AddContent();
}

if ($_GET['pid'] != '') $current_post = $db->query_first("SELECT userid FROM {$config['tables']['board_posts']} WHERE pid = {$_GET['pid']}");

if ($thread['closed']) $func->information(t('Dieser Thread wurde geschlossen. Es können keine Antworten mehr geschrieben werden'), NO_LINK);
elseif ($thread['need_type'] >= 1 and !$auth['login']) $func->information(t('Um auf diese beiträge zu antworten loggen Sie sich bitte zuerst ein'), NO_LINK);
elseif ($_GET['pid'] != '' and $auth['type'] <= 1 and $current_post['userid'] != $auth['userid']) $func->error('Sie dürfen nur Ihre eigenen Beiträge editieren!', NO_LINK);
else {
  $dsp->AddFieldsetStart(t('Antworten - Der Beitrag kann anschließend noch editiert werden'));

  include_once('inc/classes/class_masterform.php');
  $mf = new masterform();
  
  if ($thread['caption'] == '') $mf->AddField(t('Überschrift'), 'caption', 'varchar(255)');
  $mf->AddField(t('Text'), 'comment', '', LSCODE_BIG);
  $mf->AddFix('tid', $_GET['tid']);
  if ($_GET['pid'] == '') $mf->AddFix('date', time());
  $mf->AddFix('userid', $auth['userid']);
  $mf->AddFix('id', $_SERVER['REMOTE_ADDR']);
  
  if ($pid = $mf->SendForm('index.php?mod=board&action=thread&fid='. $_GET['fid'] .'&tid='. $_GET['tid'].'&gotopid='.$pid, 'board_posts', 'pid', $_GET['pid'])) {
    $tid = (int)$_GET['tid'];
  
    // Update thread-table, if new thread
  	if (!$_GET['tid'] and $_POST['caption'] != '')	{
  		$db->query("INSERT INTO {$config['tables']['board_threads']} SET
  				fid = '{$_GET['fid']}',
  				caption = '{$_POST['caption']}'
  				");
  		$tid = $db->insert_id();
  
      // Assign just created post to this new thread
  		$db->query("UPDATE {$config['tables']['board_posts']} SET tid = $tid WHERE pid = $pid");
    }
  
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
  
  	// Sys-Mail
  	$subscribers = $db->query("SELECT userid FROM {$config["tables"]["board_bookmark"]} AS b WHERE b.sysemail = 1 and b.tid = '$tid'");
  	while ($subscriber = $db->fetch_array($subscribers)) {
  		$mail->create_sys_mail($subscriber["userid"], $cfg["board_subscribe_subject"], str_replace("%URL%", "http://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}{$path}index.php?mod=board&action=thread&tid=$tid", $cfg["board_subscribe_text"]));
  	}
  	$db->free_result($subscribers);
  }
  $dsp->AddFieldsetEnd();
}


if ($thread['caption'] != '') { 
  // Bookmarks and Auto-Mail
  if ($auth['login']) {
  	if ($_GET["set_bm"]) {
  		$db->query_first("DELETE FROM {$config["tables"]["board_bookmark"]} WHERE tid = '$tid' AND userid = '{$auth['userid']}'");
  		if ($_POST["check_bookmark"]) $db->query_first("INSERT INTO {$config["tables"]["board_bookmark"]} SET tid = '$tid', userid = '{$auth['userid']}', email = '{$_POST["check_email"]}', sysemail = '{$_POST["check_sysemail"]}'");
  	}
  
  	$bookmark = $db->query_first("SELECT 1 AS found, email, sysemail FROM {$config["tables"]["board_bookmark"]} WHERE tid = '$tid' AND userid = '{$auth['userid']}'");
  	if ($bookmark["found"]) $_POST["check_bookmark"] = 1;
  	if ($bookmark["email"]) $_POST["check_email"] = 1;
  	if ($bookmark["sysemail"]) $_POST["check_sysemail"] = 1;
  
  	$dsp->SetForm("index.php?mod=board&action=thread&tid=$tid&fid=$fid&set_bm=1");
  	$dsp->AddFieldsetStart(t('Monitoring - Das Aufnehmen in die eigenen Lesezeichen ist Vorraussetzung, um per Mail zu aboniert'));
    $additionalHTML = "onclick=\"CheckBoxBoxActivate('email', this.checked)\"";
  	$dsp->AddCheckBoxRow("check_bookmark", $lang["board"]["check_bookmark"], $lang["board"]["check_bookmark2"], "", 1, $_POST["check_bookmark"], '', '', $additionalHTML);
  	$dsp->StartHiddenBox('email', $_POST["check_bookmark"]);
  	$dsp->AddCheckBoxRow("check_email", $lang["board"]["check_email"], $lang["board"]["check_email2"], "", 1, $_POST["check_email"]);
  	$dsp->AddCheckBoxRow("check_sysemail", $lang["board"]["check_sysemail"], $lang["board"]["check_sysemail2"], "", 1, $_POST["check_sysemail"]);
  	$dsp->StopHiddenBox();
  	$dsp->AddFormSubmitRow("edit");
  	$dsp->AddFieldsetEnd();
  }
  
  // Generate Boardlist-Dropdown
  $foren_liste = $db->query("SELECT fid, name FROM {$config["tables"]["board_forums"]} WHERE (need_type <= '{$list_type}')");
  while ($forum = $db->fetch_array($foren_liste))
    $templ['board']['thread']['case']['control']['goto'] .= "<option value=\"index.php?mod=board&action=forum&fid={$forum["fid"]}\">{$forum["name"]}</option>";
  $templ['board']['forum']['case']['info']['forum_choise'] = $lang['board']['forum_choise'];
  $dsp->AddDoubleRow($lang['board']['goto_forum'], $dsp->FetchModTpl('board', 'forum_dropdown'));
}

$dsp->AddBackButton("index.php?mod=board&action=forum&fid=$fid", "board/show_post"); 
$dsp->AddContent();

?>
