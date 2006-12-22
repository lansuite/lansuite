<?php
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


if ($thread['caption'] == '') $func->error($lang['board']['no_posts'], '');
else {

	$fid = $thread["fid"];

	// Mark thread read
	$search_read = $db->query_first("SELECT 1 AS found FROM {$config["tables"]["board_read_state"]} WHERE tid = $tid and userid = '{$auth["userid"]}'");
	if ($search_read["found"]) $db->query_first("UPDATE {$config["tables"]["board_read_state"]} SET last_read = ". time() ." WHERE tid = $tid and userid = '{$auth["userid"]}'");
	else $db->query_first("INSERT INTO {$config["tables"]["board_read_state"]} SET last_read = ". time() .", tid = $tid, userid = '{$auth["userid"]}'");

  // Tread Headline
	$hyperlink = '<a href="%s" class="menu">%s</a>';
	$overview_capt = sprintf($hyperlink, "index.php?mod=board", $lang['board']['overview_caption']);
	$forum_capt = sprintf($hyperlink, "index.php?mod=board&action=forum&fid=$fid", $thread['ForumName']);
	$dsp->NewContent($func->db2text($thread["caption"]), "{$lang['board']['board']} - $overview_capt - $forum_capt - ". $func->db2text($thread["caption"]));

	// Generate Thread-Buttons
	$buttons = '';
	if (($auth["login"] == 1 and $thread['need_type'] >= 1) or $thread['need_type'] == 0 or $auth['type'] > 1) $buttons .= " ". $dsp->FetchButton("index.php?mod=board&action=post&fid=$fid", "new_thread") .' '. $dsp->FetchButton("index.php?mod=board&action=post&tid=$tid", "new_post");
	if ($auth["type"] > 1) {
    if ($thread['closed']) $buttons .= ' '. $dsp->FetchButton("index.php?mod=board&action=thread&step=11&tid=$tid", "open");
    else $buttons .= ' '. $dsp->FetchButton("index.php?mod=board&action=thread&step=10&tid=$tid", "close");
    $buttons .= ' '. $dsp->FetchButton("index.php?mod=board&action=edit&mode=delete&tid=$tid", "delete");
  }

	$query = $db->query("SELECT pid, comment, userid, date FROM {$config['tables']['board_posts']} WHERE tid='$tid' order by date");
	$count_entrys = $db->num_rows($query);

  // Page select
	if ($count_entrys > $cfg['board_max_posts']){
		if($_GET['pid'] == "last") $_GET['posts_page'] = ceil(($count_entrys) / $cfg['board_max_posts']) - 1;
		$pages = $func->page_split($_GET['posts_page'], $cfg['board_max_posts'], $count_entrys, "index.php?mod=board&action=thread&tid=$tid", "posts_page");
		$query = $db->query("SELECT pid, comment, userid, date FROM {$config['tables']['board_posts']} WHERE tid='$tid' order by date {$pages['sql']}");
	}
  $dsp->AddSingleRow($buttons.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$pages['html']);

	while ($row = $db->fetch_array($query)){
		$pid = $row["pid"];

		$templ['board']['thread']['case']['info']['post']['pid'] 		= $pid;
		$templ['board']['thread']['case']['info']['post']['text'] 		= $func->db2text2html($row["comment"]);
		$templ['board']['thread']['case']['info']['post']['date'] 		= $lang['board']['add_at'] . ": " . $func->unixstamp2date($row["date"],"daydatetime");
		$templ['board']['thread']['case']['info']['thread']['go_top'] 	= $lang['board']['go_top'];

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
		$templ['board']['thread']['case']['info']['post']['poster']['type'] 		= $userdata["type"];
		$templ['board']['thread']['case']['info']['post']['poster']['rank'] 		= $lang['board']['rank'] . ": " . $userdata["rank"];
		$templ['board']['thread']['case']['info']['post']['poster']['posts'] 		= $lang['board']['posts'] . ": " . $userdata["posts"];
		$templ['board']['thread']['case']['info']['post']['poster']['avatar']		= $userdata["avatar"];
		$templ['board']['thread']['case']['info']['post']['poster']['signature'] = '';
		if ($userdata["signature"]) $templ['board']['thread']['case']['info']['post']['poster']['signature'] 	= '<hr size="1">'.$func->db2text2html($userdata["signature"]);

		$templ['board']['thread']['case']['info']['post']['edit'] = "";
		if ($auth['type'] > 1 or $row["userid"] == $auth["userid"])
			$templ['board']['thread']['case']['info']['post']['edit'] .= $dsp->FetchButton("index.php?mod=board&action=edit&mode=pchange&pid=$pid", "edit");
		if ($auth['type'] > 1)
			$templ['board']['thread']['case']['info']['post']['edit'] .= $dsp->FetchButton("index.php?mod=board&action=edit&mode=pdelete&pid=$pid", "delete");

		$templ['board']['forum']['case']['control']['rows'] .= $dsp->AddModTpl("board", "board_thread_row");
	}

	if ($_SESSION['threadview'] != $tid) $db->query("UPDATE {$config["tables"]["board_threads"]} SET views=views+1 WHERE tid='$tid'");
	$_SESSION['threadview'] = $tid;

  $dsp->AddSingleRow($buttons.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$pages['html']);
  $dsp->AddContent();

	// Generate Boardlist-Dropdown
	$foren_liste = $db->query("SELECT fid, name FROM {$config["tables"]["board_forums"]} WHERE (need_type <= '{$list_type}')");
	while ($forum = $db->fetch_array($foren_liste))
	  $templ['board']['thread']['case']['control']['goto'] .= "<option value=\"index.php?mod=board&action=forum&fid={$forum["fid"]}\">{$forum["name"]}</option>";
	$templ['board']['forum']['case']['info']['forum_choise'] = $lang['board']['forum_choise'];
  $dsp->AddDoubleRow($lang['board']['goto_forum'], $dsp->FetchModTpl('board', 'forum_dropdown'));

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
		$dsp->AddCheckBoxRow("check_bookmark", $lang["board"]["check_bookmark"], $lang["board"]["check_bookmark2"], "", 1, $_POST["check_bookmark"]);
		$dsp->AddCheckBoxRow("check_email", $lang["board"]["check_email"], $lang["board"]["check_email2"], "", 1, $_POST["check_email"]);
		$dsp->AddCheckBoxRow("check_sysemail", $lang["board"]["check_sysemail"], $lang["board"]["check_sysemail2"], "", 1, $_POST["check_sysemail"]);
		$dsp->AddFormSubmitRow("change");
	}

	$dsp->AddBackButton("index.php?mod=board&action=forum&fid=$fid", "board/show_post"); 
	$dsp->AddContent();
}
?>
