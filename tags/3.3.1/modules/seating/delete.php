<?php

switch($_GET['step']) {
	default:
    include_once('modules/seating/search.inc.php');
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