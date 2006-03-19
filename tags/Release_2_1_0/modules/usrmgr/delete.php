<?php

$userid 	= $_GET["userid"];

switch ($_GET["step"]) {
	default:
    include_once('modules/usrmgr/search.inc.php');
/*
		$sql = " AND u.type > 0 AND u.userid != '{$auth["userid"]}'";
		if ($auth["type"] != 3) $sql .= " AND u.type < 2";

		$mastersearch = new MasterSearch( $vars, "index.php?mod=usrmgr&action=delete", "index.php?mod=usrmgr&action=delete&step=2&userid=", $sql);
		$mastersearch->LoadConfig("users", $lang['usrmgr']['ms_search'], $lang['usrmgr']['ms_resulth']);
		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();

		$templ['index']['info']['content'] .= $mastersearch->GetReturn();
*/		
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
		$get_data = $db->query_first("SELECT username, type FROM {$config["tables"]["user"]} WHERE userid = '$userid'");

		if ($auth["type"] == 2 and $get_data["type"] >= 2) $func->error($lang['usrmgr']['del_tofew_rights'], "index.php?mod=usrmgr&action=delete");
		elseif ($get_data["type"] < 0) $func->error($lang['usrmgr']['del_allready'], "index.php?mod=usrmgr&action=delete");
		elseif ($auth["userid"] == $userid) $func->error($lang['usrmgr']['del_no_self'], "index.php?mod=usrmgr&action=delete");
		elseif ($get_data["username"] == "") $func->error($lang["usrmgr"]["checkin_nouser"], "index.php?mod=usrmgr&action=details&userid=$userid");
		else {						
			$db->query("UPDATE {$config["tables"]["user"]} SET type = '-4', username = CONCAT(username,' (Deleted)'), comment = CONCAT('{$lang["usrmgr"]["del_sql_mark"]} email: ',email), email = CONCAT(userid,'_deleted') WHERE userid = '$userid'");
			$db->query("UPDATE {$config["tables"]["seat_seats"]} SET status = '1', userid='0' WHERE userid = '$userid'");

			$func->confirmation($lang["usrmgr"]["del_success"], "index.php?mod=usrmgr&action=details&userid=".$userid);
		}

		break;
}
?>
