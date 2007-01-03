<?php

include("modules/board/class_board.php");
$bfunc = new board_func;

$tid 		= $_GET['tid'];
$pid 		= $_GET['pid'];
$mode		= $_GET['mode'];
$step 		= $_GET['step'];

$text 		= $func->text2db($_POST['text']);
$caption 	= $func->text2db($_POST['caption']);

switch ($mode) {
	case change:
	case pchange:
		switch($step) {
			default:
				$_SESSION['posted'] = FALSE; 
			break;

			case 2:
				if ($fid == "" AND $_GET['action'] == "thread") {
					$func->error($lang['board']['thread_no_forumid'], "");
					$step = 1;
				}
				if ($tid == "" AND $_GET['action'] == "post") {
					$func->error($lang['board']['thread_no_threadid'], "");
					$step = 1;
				}
				if ($text == "") 		{
					$board_error['text'] = $lang['board']['thread_no_text'];
					$step = 1;
				}
				if ($_GET['action'] == "thread" AND $caption == "") 	{
					$board_error['caption'] = $lang['board']['thread_no_caption'];
					$step = 1;
				}
				if (strlen($text) > 5000) {
					$board_error['text'] = $lang['board']['thread_max_text'];
					$step = 1;
				}
				$posted = $_SESSION['posted'];
			break;	
		}
	break;
}


switch($mode) {
	case change:
		switch($step) {
			default:
				$row = $db->query_first("SELECT fid, caption, text, userid FROM {$config["tables"]["board_threads"]} WHERE tid='$tid'");

				// Name auslesen
				$board_username = preg_match("@<!--[.*]-->@si",$text,$treffer);
				$text = preg_replace("@<!--[.*]-->@si","",$text);

				if ($row["userid"] != $auth["userid"] and !($auth['type'] > 1)) $func->error("ACCESS DENIED","");
				else {
					$fid = $row["fid"];
					$text 	 =($text == "")    ? $row["text"]    : $text;
					$caption =($caption == "") ? $row["caption"] : $caption;

					$dsp->NewContent($lang['board']['thread_change_caption'],$lang['board']['thread_change_subcaption']);
					$dsp->SetForm("index.php?mod=board&action=edit&mode=change&step=2&tid=$tid");
					$dsp->AddTextFieldRow("caption", $lang['board']['thread_desc'], $caption, $board_error['caption']);
					$dsp->AddTextAreaPlusRow("text", $lang['board']['thread_text'], $text, $board_error['text']);
					if ($auth['type'] > 1){
						$array_forums = array();
						$forums = $db->query("SELECT fid, name FROM {$config["tables"]["board_forums"]}");
						while ($data = $db->fetch_array($forums)){
							($data['fid'] == $fid) ? $selected = "selected" : $selected = "";
							array_push($array_forums, "<option $selected value='{$data['fid']}'>" . $data['name'] . "</option>");
						}
						$dsp->AddDropDownFieldRow("fid", $lang['board']['thread_forum'], $array_forums, "");
					}					
					$dsp->AddFormSubmitRow("edit");
					$dsp->AddBackButton("index.php?mod=board&action=thread&tid=$tid");
					$dsp->AddContent();

				}
			break;

			case 2:
				$db->query("UPDATE {$config['tables']['board_threads']} SET text='$text', caption='$caption', fid='{$_POST['fid']}' WHERE tid='$tid' AND (userid = {$auth["userid"]} OR {$auth['type']} > 1)");
				$db->query("UPDATE {$config['tables']['board_posts']} SET fid='{$_POST['fid']}' WHERE tid='$tid AND (userid = {$auth["userid"]} OR {$auth['type']} > 1)'");

				$func->confirmation($lang['board']['thread_change_ok'],"index.php?mod=board&action=thread&tid=$tid");
			break;
		}
	break;


	case pchange:
		switch($step) {
			default:
				$row = $db->query_first("SELECT comment, userid FROM {$config["tables"]["board_posts"]} WHERE pid='$pid'");

				if ($row["userid"] != $auth["userid"] and !($auth['type'] > 1)) $func->error("ACCESS DENIED", "");
				else {
					$tid = $bfunc->get_tid($pid);
					$text =($text == "") ?  $row["comment"] : $text;

					$dsp->NewContent(str_replace("%CAPTION%", $caption, $lang['board']['post_change_caption']), $lang['board']['post_change_subcaption']);
					$dsp->SetForm("index.php?mod=board&action=edit&mode=pchange&step=2&pid=$pid");
					$dsp->AddTextAreaPlusRow("text", $lang['board']['thread_text'], $text,$board_error['text']);
					$dsp->AddFormSubmitRow("edit");	
					$dsp->AddBackButton("index.php?mod=board&action=thread&tid=$tid");
					$dsp->AddContent();			
				}

			break;

			case 2:
				$tid = $bfunc->get_tid($pid);

				$db->query("UPDATE {$config['tables']['board_posts']} SET comment='$text' WHERE pid='$pid' AND (userid = {$auth["userid"]} OR {$auth['type']} > 1)");
				$func->confirmation($lang['board']['post_change_ok'],"index.php?mod=board&action=thread&tid=$tid");
				break;		
		}
	break;

	case delete:
		if ($auth["type"] > 1) switch($step) {
			default:
				$func->question($lang['board']['del_post_quest'], "index.php?mod=board&action=edit&mode=delete&step=2&tid=$tid", "index.php?mod=board&action=thread&tid=$tid");
			break;

			case 2:
				$fid = $bfunc->get_fid($tid);

				$db->query("DELETE FROM {$config['tables']['board_threads']} WHERE tid='$tid'");
				$db->query("DELETE FROM {$config['tables']['board_posts']} WHERE tid='$tid'");

				$func->confirmation($lang['board']['thread_del_ok'], "index.php?mod=board&action=forum&fid=$fid");
			break;
		} else $func->error("ACCESS DENIED", "");
	break;


	case pdelete:
		$tid = $bfunc->get_tid($pid);
		if ($auth["type"] > 1) switch($step) {
			default:
				$func->question($lang['board']['del_post_quest'], "index.php?mod=board&action=edit&mode=pdelete&step=2&pid=$pid&gotopid={$_GET['gotopid']}", "index.php?mod=board&action=thread&tid=$tid&gotopid={$_GET['gotopid']}");
			break;

			case 2:
				$db->query("DELETE FROM {$config['tables']['board_posts']} WHERE pid='$pid'");
				$func->confirmation($lang['board']['post_del_ok'], "index.php?mod=board&action=thread&tid=$tid&gotopid={$_GET['gotopid']}");
			break;
		} else $func->information($lang['board']['post_del_orga'], "index.php?mod=board&action=thread&tid=$tid&gotopid={$_GET['gotopid']}");
	break;
}

?>
