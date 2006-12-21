<?php

$userid 	= $_GET["userid"];

function DeleteUser($userid) {
  global $db, $config, $auth, $lang, $func;
  
	$get_data = $db->query_first("SELECT username, type FROM {$config["tables"]["user"]} WHERE userid = '$userid'");

	if ($auth["type"] == 2 and $get_data["type"] >= 2) $func->error($lang['usrmgr']['del_tofew_rights'], "index.php?mod=usrmgr");
	elseif ($get_data["type"] < 0) $func->error($lang['usrmgr']['del_allready'], "index.php?mod=usrmgr");
	elseif ($auth["userid"] == $userid) $func->error($lang['usrmgr']['del_no_self'], "index.php?mod=usrmgr");
	#elseif ($get_data["username"] == "") $func->error($lang["usrmgr"]["checkin_nouser"], "index.php?mod=usrmgr");
	else {						
		$db->query("UPDATE {$config["tables"]["user"]} SET type = '-4', username = CONCAT(username,' (Deleted)'), comment = CONCAT('{$lang["usrmgr"]["del_sql_mark"]} email: ',email), email = CONCAT(userid,'_deleted') WHERE userid = '$userid'");
		$db->query("UPDATE {$config["tables"]["seat_seats"]} SET status = '1', userid='0' WHERE userid = '$userid'");
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
			if ($get_seat["n"] > 0) $seattext = $lang['usrmgr']['del_quest_seat_text'];

			$func->question(str_replace("%USER%", "<b>$username</b>", str_replace("%SEAT_TEXT%", $seattext, $lang['usrmgr']['del_quest'])), "index.php?mod=usrmgr&action=delete&step=3&userid=$userid", "index.php?mod=usrmgr&action=details&userid=".$userid);
		} else $func->error($lang["usrmgr"]["checkin_nouser"],"index.php?mod=usrmgr&action=delete");	
	break;
	
	case 3:
    if (DeleteUser($userid)) $func->confirmation($lang["usrmgr"]["del_success"], "index.php?mod=usrmgr&action=details&userid=".$userid);
	break;
	
	// Multi-Delete
	case 10:
	  $success = true;
  	foreach ($_POST['action'] as $key => $val) {
  	  if (!DeleteUser($key)) $success = false;
    }
		if ($success) $func->confirmation($lang["usrmgr"]["del_success"], 'index.php?mod=usrmgr');
	break;
}
?>
