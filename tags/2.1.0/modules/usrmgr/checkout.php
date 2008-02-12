<?php

$userid = $_GET['userid'];
$timestamp = time();

$user_data = $db->query_first("SELECT username FROM {$config["tables"]["user"]} WHERE userid='$userid'");

switch($_GET['step']) {
	default:
		$mastersearch = new MasterSearch($vars, "index.php?mod=usrmgr&action=checkout", "index.php?mod=usrmgr&action=checkout&step=2&userid=", " AND p.checkout = '0' AND p.checkin != '0' AND u.type < 2 AND p.party_id={$party->party_id}");
		$mastersearch->LoadConfig("users", $lang['usrmgr']['ms_search'], $lang['usrmgr']['ms_result']);
		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();

		$templ['index']['info']['content'] .= $mastersearch->GetReturn();
	break;

	case 2:
		 if ($user_data["username"]) $func->question(str_replace("%USER%", $user_data["username"], $lang["usrmgr"]["checkout_quest"]), "index.php?mod=usrmgr&action=checkout&step=3&userid=$userid", "index.php?mod=usrmgr&action=checkout");
		 else $func->error($lang["usrmgr"]["checkout_nouser"], "index.php?mod=usrmgr&action=checkout");
	break;

	case 3:
		$db->query("UPDATE {$config["tables"]["party_user"]} SET checkout='$timestamp' WHERE user_id='$userid' AND party_id={$party->party_id}");
	 	$func->confirmation(str_replace("%USER%", $user_data["username"], $lang["usrmgr"]["checkout_success"]), "index.php?mod=usrmgr");
	 break;
} // switch
?>
