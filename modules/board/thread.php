<?php
include("modules/board/class_board.php");
$bfunc = new board_func;

$tid = $_GET["tid"];
$list_type = $auth['type'] + 1;
$thread = $db->query_first("SELECT fid, caption, userid, date  FROM {$config["tables"]["board_threads"]} WHERE tid='$tid'");

$query_form = $db->query("SELECT fid, need_type FROM {$config["tables"]["board_forums"]} WHERE (need_type <= '{$list_type}') and (fid='{$thread["fid"]}') GROUP BY fid");
if ($db->num_rows($query_form) == 0) $func->error($lang['board']['posts'], "");
else {

	// Mark thread read
	$search_read = $db->query_first("SELECT 1 AS found FROM {$config["tables"]["board_read_state"]} WHERE tid = $tid and userid = '{$auth["userid"]}'");
	if ($search_read["found"]) $db->query_first("UPDATE {$config["tables"]["board_read_state"]} SET last_read = ". time() ." WHERE tid = $tid and userid = '{$auth["userid"]}'");
	else $db->query_first("INSERT INTO {$config["tables"]["board_read_state"]} SET last_read = ". time() .", tid = $tid, userid = '{$auth["userid"]}'");

	$templ['board']['thread']['case']['info']['thread']['caption'] 	= $func->db2text($thread["caption"]);
	$fid = $thread["fid"];

	$query = $db->query("SELECT pid, comment, userid, date FROM {$config['tables']['board_posts']} WHERE tid='$tid' order by date");
	$count_entrys =  $db->num_rows($query);

	if ($count_entrys > $cfg['board_max_posts']){
		if($_GET['pid'] == "last") $_GET['posts_page'] = floor($count_entrys / $cfg['board_max_posts']);
		$pages = $func->page_split($_GET['posts_page'],$cfg['board_max_posts'],$count_entrys,"index.php?mod=board&action=thread&fid=$fid&tid=$tid","posts_page");
		$query = $db->query("SELECT pid, comment, userid, date FROM {$config['tables']['board_posts']} WHERE tid='$tid' order by date {$pages['sql']}");
		$templ['board']['overview']['case']['info']['page_split'] = $pages['html'];
	}

	$forum = $db->query_first("SELECT name, need_type FROM {$config["tables"]["board_forums"]} WHERE fid='$fid'");
	$hyperlink = '<a href="%s" class="menu">%s</a>';
	$need_type = $forum['need_type'];

	//Bugfix by Poschi: Hier wurde auf "index.php?mod=board&action=forum&fid=$fid" verlinkt, aber das ist deffiniv nicht die Forenübersicht, sondern die Thread übersicht eines Forums ==> ausgebessert
	$overview_capt = sprintf($hyperlink, "index.php?mod=board", $lang['board']['overview_caption']);
	$forum_capt = sprintf($hyperlink, "index.php?mod=board&action=forum&fid=$fid", $forum["name"]);
	$dsp->NewContent($func->db2text($thread["caption"]), "{$lang['board']['board']} - $overview_capt - $forum_capt - ". $func->db2text($thread["caption"]));

	while ($row = $db->fetch_array($query)){
		$pid = $row["pid"];

		$templ['board']['thread']['case']['info']['post']['pid'] 		= $pid;
		$templ['board']['thread']['case']['info']['post']['text'] 		= $func->db2text2html($row["comment"]);
		$templ['board']['thread']['case']['info']['post']['poster']['userid'] 	= $row["userid"];
		$templ['board']['thread']['case']['info']['post']['date'] 		= $lang['board']['add_at'] . ": " . $func->unixstamp2date($row["date"],"daydatetime");
		$templ['board']['thread']['case']['info']['thread']['go_top'] 	= $lang['board']['go_top'];

		if ($row['userid'] == 0){
			preg_match("@<!--(.*)-->@",$row['comment'],$tmp);
			$userdata['username'] = $lang['board']['guest_prefix'] . $tmp[1];
			$userdata["avatar"] = "";
			$userdata["rank"] = "";
			$userdata["posts"] = "";
			$userdata["signature"] = "";
		} else $userdata = $bfunc->getuserinfo($row["userid"]);

		$templ['board']['thread']['case']['info']['post']['poster']['username'] 	= $userdata["username"];
		$templ['board']['thread']['case']['info']['post']['poster']['type'] 		= $userdata["type"];
		$templ['board']['thread']['case']['info']['post']['poster']['rank'] 		= $lang['board']['rank'] . ": " . $userdata["rank"];
		$templ['board']['thread']['case']['info']['post']['poster']['posts'] 		= $lang['board']['posts'] . ": " . $userdata["posts"];
		$templ['board']['thread']['case']['info']['post']['poster']['avatar']		= $userdata["avatar"];
		$templ['board']['thread']['case']['info']['post']['poster']['signature'] 	= $func->db2text2html($userdata["signature"]);

		$templ['board']['thread']['case']['info']['post']['edit'] = "";
		if ($auth['type'] > 1 or $row["userid"] == $auth["userid"])
			$templ['board']['thread']['case']['info']['post']['edit'] .= $dsp->FetchButton("index.php?mod=board&action=edit&mode=pchange&pid=$pid", "edit");
		if ($auth['type'] > 1)
			$templ['board']['thread']['case']['info']['post']['edit'] .= $dsp->FetchButton("index.php?mod=board&action=edit&mode=pdelete&pid=$pid", "delete");

		$templ['board']['forum']['case']['control']['rows'] .= $dsp->FetchModTpl("board", "board_thread_row");
	}

	// Generate Boardlist-Dropdown
	$foren_liste = $db->query("SELECT fid, name FROM {$config["tables"]["board_forums"]} WHERE (need_type <= '{$list_type}')");
	while ($forum = $db->fetch_array($foren_liste))
		$templ['board']['thread']['case']['control']['goto'] .= "<option value=\"{$forum["fid"]}\">{$forum["name"]}</option>";

	if ($_SESSION['threadview'] != $tid) $db->query("UPDATE {$config["tables"]["board_threads"]} SET views=views+1 WHERE tid='$tid'");
	$_SESSION['threadview'] = $tid;

	// Generate Thread-Buttons
	$templ['board']['forum']['case']['control']['new'] = "";
	if (($auth["login"] == 1 && $need_type >= 1) || $need_type == 0 || $auth['type'] > 1) $templ['board']['forum']['case']['control']['new'] .= " ". $dsp->FetchButton("index.php?mod=board&action=post&fid=$fid", "new_thread") ." ". $dsp->FetchButton("index.php?mod=board&action=post&tid=$tid", "new_post");
	#." ". $dsp->FetchButton("index.php?mod=board&action=bookmark&tid=$tid", "bookmark");
	if ($auth["type"] > 1) $templ['board']['forum']['case']['control']['new'] .= " ". $dsp->FetchButton("index.php?mod=board&action=edit&mode=delete&tid=$tid", "delete");

	$templ['board']['forum']['case']['info']['forum_choise'] = $lang['board']['forum_choise'];
	$templ['board']['forum']['case']['info']['forum_goto'] = $lang['board']['goto_forum'];

	$dsp->AddSingleRow($dsp->FetchModTpl("board", "board_thread_case"));

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
