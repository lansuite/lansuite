<?php

switch ($_GET['step']) {
	default:
		$mastersearch = new MasterSearch($vars, "index.php?mod=board&action=change", "index.php?mod=board&action=add&var=change&fid=", "");
		$mastersearch->LoadConfig("board_forums", $lang['board']['ms_board_search'], $lang['board']['ms_board_result']);
		//	$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();
		$templ['index']['info']['content'] .= $mastersearch->GetReturn();
	break;
}

?> 
