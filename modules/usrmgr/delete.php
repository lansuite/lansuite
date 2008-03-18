<?php

$userid 	= $_GET["userid"];

function DeleteUser($userid) {
  global $db, $config, $auth, $lang, $func;
  
	$get_data = $db->query_first("SELECT username, type FROM {$config["tables"]["user"]} WHERE userid = '$userid'");

	if ($auth["type"] == 2 and $get_data["type"] >= 2) $func->error(t('Sie haben nicht die erforderlichen Rechte, um einen Admin zu löschen'), "index.php?mod=usrmgr");
	elseif ($get_data["type"] < 0) $func->error(t('Dieser Benutzer wurde bereits gelöscht'), "index.php?mod=usrmgr");
	elseif ($auth["userid"] == $userid) $func->error(t('Sie können sich nicht selbst löschen'), "index.php?mod=usrmgr");
	#elseif ($get_data["username"] == "") $func->error(t('Dieser Benutzer existiert nicht'), "index.php?mod=usrmgr");
	else {						
		$db->query("UPDATE {$config["tables"]["user"]} SET type = '-4', username = CONCAT(username,' (Deleted)'), comment = CONCAT('".t('Dieser User wurde gelöscht!')." email: ',email), email = CONCAT(userid,'_deleted') WHERE userid = '$userid'");
		$db->query("UPDATE {$config["tables"]["seat_seats"]} SET status = '1', userid='0' WHERE userid = '$userid'");
		$db->qry('DELETE FROM %prefix%stats_auth WHERE userid=%int%', $userid);
		return true;
	}
	
	return false;
}

switch ($_GET["step"]) {
	default:
    include_once('modules/usrmgr/search.inc.php');
	break;

	case 2:
		$get_data = $db->query_first("SELECT username FROM {$config["tables"]["user"]} WHERE userid = '$userid'");
		$username = $get_data["username"];

		if ($username != "") {
			$get_seat = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["seat_seats"]} WHERE userid = '$userid'");
			if ($get_seat["n"] > 0) $seattext = t('und den vorhandenen Sitzplatz freigeben');

			$func->question(t('Sind Sie sicher, dass Sie den Benutzer %1 wirklich löschen %2 wollen?', "<b>$username</b>", $seattext), "index.php?mod=usrmgr&action=delete&step=3&userid=$userid", "index.php?mod=usrmgr&action=details&userid=".$userid);
		} else $func->error(t('Dieser Benutzer existiert nicht'),"index.php?mod=usrmgr&action=delete");	
	break;
	
	case 3:
    if (DeleteUser($userid)) $func->confirmation(t('Der Benutzer wurde erfolgreich gelöscht'), "index.php?mod=usrmgr&action=details&userid=".$userid);
	break;
	
	// Multi-Delete
	case 10:
	  $success = true;
  	foreach ($_POST['action'] as $key => $val) {
  	  if (!DeleteUser($key)) $success = false;
    }
		if ($success) $func->confirmation(t('Der Benutzer wurde erfolgreich gelöscht'), 'index.php?mod=usrmgr');
	break;
}
?>