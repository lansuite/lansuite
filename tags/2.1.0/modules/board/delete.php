<?php

switch ($_GET['step']) {
	default:
		$mastersearch = new MasterSearch( $vars, "index.php?mod=board&action=delete", "index.php?mod=board&action=delete&step=2&fid=", "");
		$mastersearch->LoadConfig("board_forums", $lang['board']['ms_board_search'], $lang['board']['ms_board_result']);
	//	$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();
		$templ['index']['info']['content'] .= $mastersearch->GetReturn();
	break;

	case 2:
		$text = $lang['board']['del_forum_quest'];

		$link_target_yes = "index.php?mod=board&action=delete&step=3&fid={$_GET['fid']}";
		$link_target_no = "index.php?mod=board&action=delete";
		$func->question($text, $link_target_yes, $link_target_no);
	break;

	case 3:
		$db->query("DELETE FROM {$config["tables"]["board_forums"]} WHERE fid='{$_GET['fid']}'");
		$db->query("DELETE FROM {$config["tables"]["board_threads"]} WHERE fid='{$_GET['fid']}'");
		$db->query("DELETE FROM {$config["tables"]["board_posts"]} WHERE fid='{$_GET['fid']}'");

		$func->confirmation($lang['board']['forum_del'], "index.php?mod=board&action=delete"); 
	break;
}

?>
