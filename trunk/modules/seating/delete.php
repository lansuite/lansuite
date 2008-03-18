<?php

switch($_GET['step']) {
	default:
    include_once('modules/seating/search.inc.php');
	break;

	case 2:
		$func->question(t('Wollen Sie diesen Sitzblock wirklich löschen?'),
			"index.php?mod=seating&action=delete&step=3&blockid={$_GET['blockid']}",
			'index.php?mod=seating&action=delete');
	break;

	case 3:
		$db->query("DELETE FROM {$config["tables"]["seat_block"]} WHERE blockid='{$_GET['blockid']}'");
		$db->query("DELETE FROM {$config["tables"]["seat_sep"]} WHERE blockid='{$_GET['blockid']}'");
		$db->query("DELETE FROM {$config["tables"]["seat_seats"]} WHERE blockid='{$_GET['blockid']}'");

		$func->confirmation(t('Der Sitzblock wurde erfolgreich gelöscht'), 'index.php?mod=seating&action=delete');
	break;
}

?>