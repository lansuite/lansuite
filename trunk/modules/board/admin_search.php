<?php

if ($_POST["checkbox"]) {
	foreach($_POST["checkbox"] AS $item) {
		switch ($_POST["action_select"]) {
			case "del":
				$db->query("DELETE FROM {$config["tables"]["board_posts"]} WHERE pid = $item");
				$func->confirmation($lang['board']['adms_del_success'], "index.php?mod=board&action=admin_search");
			break;

			case "ban":
				echo $item. "b" . HTML_NEWLINE;
			break;
		}
	}

} else {
	$mastersearch = new MasterSearch( $vars, "index.php?mod=board&action=admin_search", "index.php?mod=board&action=admin_search", "" );
	$mastersearch->LoadConfig("board", $lang['board']['ms_post_search'], $lang['board']['ms_post_result']);
	$mastersearch->PrintForm();
	$mastersearch->Search();
	$mastersearch->PrintResult();

	$templ['index']['info']['content'] .= $mastersearch->GetReturn();
}
?>
