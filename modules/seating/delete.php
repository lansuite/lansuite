<?php

switch($_GET['step']) {
	default:
		$mastersearch = new MasterSearch( $vars, 'index.php?mod=seating&action=delete', 'index.php?mod=seating&action=delete&step=2&blockid=', '');
		$mastersearch->LoadConfig('seat_blocks', $lang['seat']['ms_search'], $lang['seat']['ms_result']);   // <- Wo stehen diese Übersetzungen ???
		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();
		$templ['index']['info']['content'] .= $mastersearch->GetReturn();
	break;

	case 2:
		$func->question($lang['seating']['q_del_block'],
			"index.php?mod=seating&action=delete&step=3&blockid={$_GET['blockid']}",
			'index.php?mod=seating&action=delete');
	break;

	case 3:
		$db->query("DELETE FROM {$config["tables"]["seat_block"]} WHERE blockid='{$_GET['blockid']}'");
		$db->query("DELETE FROM {$config["tables"]["seat_sep"]} WHERE blockid='{$_GET['blockid']}'");
		$db->query("DELETE FROM {$config["tables"]["seat_seats"]} WHERE blockid='{$_GET['blockid']}'");

		$func->confirmation($lang['seating']['c_del_block'], 'index.php?mod=seating&action=delete');
	break;
}

?>