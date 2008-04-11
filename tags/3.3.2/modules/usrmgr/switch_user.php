<?php
	
switch ($_GET["step"]){
	case 10: // Switch user

		// Get target user type
		$target_user = $db->query_first("SELECT type FROM {$config["tables"]["user"]} WHERE userid = {$_GET["userid"]}");

		if ($auth["type"] > $target_user["type"]) {

			// Store switch back code in current (admin) user data
			$db->query("UPDATE {$config["tables"]["user"]} SET switch_back = '". $SwitchUser->Code ."' WHERE userid = {$auth["userid"]}");

			// Link session ID to new user ID
			$db->query("UPDATE {$config["tables"]["stats_auth"]} SET
							userid='{$_GET["userid"]}',
							login='1'
							WHERE sessid='{$auth["sessid"]}'");

			$func->information($lang['usrmgr']['switch_success'], $func->internal_referer);
		} else $func->error($lang['usrmgr']['switch_wrong_level'], $func->internal_referer);
	break;

	case 11: // Switch back

		// Check switch back code
		$admin_user = $db->query_first("SELECT switch_back FROM {$config["tables"]["user"]} WHERE userid = {$_COOKIE["olduserid"]}");

		if ($_COOKIE["sb_code"] == $admin_user["switch_back"]) {

			// Link session ID to origin user ID
			$db->query("UPDATE {$config["tables"]["stats_auth"]} SET
							userid='{$_COOKIE["olduserid"]}',
							login='1'
							WHERE sessid='{$auth["sessid"]}'");

			// Delete switch back code in admins user data
			$db->query("UPDATE {$config["tables"]["user"]} SET switch_back = '' WHERE userid = {$_COOKIE["olduserid"]}");

			$func->information($lang['usrmgr']['switch_success'], $func->internal_referer);	
		} else $func->error($lang['usrmgr']['switch_wrong_sbc'], $func->internal_referer);
	break;
}
?>
