<?php
/*************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.0
*	Filename: 		newpwd.php
*	Module: 		usermanager
*	Main editor: 		Michael@one-network.org (previous version), raphael@one-network.org (class design)
*	Last change: 		07.02.2003 16:34
*	Description: 		Generate new password. Only accessible by admins.
*	Remarks: 		
*
**************************************************************************/

$user_data = $db->query_first("SELECT name, firstname, username, type FROM {$config["tables"]["user"]} WHERE userid = '{$_GET['userid']}'");

switch($_GET['step']) {	
	default:
		$mastersearch = new MasterSearch($vars, "index.php?mod=usrmgr&action=newpwd", "index.php?mod=usrmgr&action=newpwd&step=2&userid=", " AND (u.type > 0)");
		$mastersearch->LoadConfig("users", $lang['usrmgr']['ms_search'], $lang['usrmgr']['ms_result']);
		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();

		$templ['index']['info']['content'] .= $mastersearch->GetReturn();
	break;

	case 2:
		$func->question(str_replace("%FIRSTNAME%", $user_data["firstname"], str_replace("%LASTNAME%", $user_data["name"], str_replace("%USERNAME%", $user_data["username"], $lang["usrmgr"]["newpw_quest"]))), "index.php?mod=usrmgr&action=newpwd&step=3&userid=". $_GET['userid'], $func->internal_referer);
	break;

	case 3:
		$password = rand(1000, 9999);
		$md5_password = md5($password);

		if ($_SESSION["auth"]["type"] < $userdata["type"]) $func->information($lang["usrmgr"]["newpw_few_rights"], "");
		else {
			$db->query("UPDATE {$config["tables"]["user"]} SET password = '$md5_password' WHERE userid = '{$_GET['userid']}'");

			$func->confirmation(str_replace("%PASSWORD%", $password, str_replace("%FIRSTNAME%", $user_data["firstname"], str_replace("%LASTNAME%", $user_data["name"], str_replace("%USERNAME%", $user_data["username"], $lang["usrmgr"]["newpw_success"])))), "index.php?mod=usrmgr&action=details&userid=". $_GET['userid']);
		}
	break;
}
?>
