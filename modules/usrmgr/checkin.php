<?php

$userid = $_GET['userid'];
$timestamp = time();

switch($_GET['step']) {
	default:
		$mastersearch = new MasterSearch($vars, "index.php?mod=usrmgr&action=checkin", "index.php?mod=usrmgr&action=checkin&step=2&userid=", " (p.checkin = '0' OR p.checkout != '0') AND u.type > 0 AND (p.party_id={$party->party_id}) ");
		$mastersearch->LoadConfig("users", $lang['usrmgr']['ms_search'], $lang['usrmgr']['ms_result']);
		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();
		
		$templ['index']['info']['content'] .= $mastersearch->GetReturn();	
	break;

	case "2":
		 $u_data = $db->query_first("SELECT username, party.paid, type FROM {$config["tables"]["user"]} LEFT JOIN {$config["tables"]["party_user"]} AS party ON userid=party.user_id WHERE userid='$userid' AND party_id={$party->party_id}");

		 $q_text = "";
		 if ((!$u_data["paid"]) && ($u_data["type"] < 2)) $q_text = $lang["usrmgr"]["checkin_not_paid"] .HTML_NEWLINE . HTML_NEWLINE;
		 $q_text .= str_replace("%USER%", $u_data["username"], $lang["usrmgr"]["checkin_confirm"]);

		 if ($u_data["username"]) $func->question($q_text, "index.php?mod=usrmgr&action=checkin&step=3&userid=$userid", $func->internal_referer);
		 else $func->error($lang["usrmgr"]["checkin_nouser"], "index.php?mod=usrmgr&action=checkin");
	break;

	case "2a":   // Re-CheckIn, after CheckOut
		$row = $db->query_first("SELECT username  FROM {$config["tables"]["user"]} WHERE userid='$userid'");

		$func->question(str_replace("%USER%", $row["username"], $lang["usrmgr"]["checkin_re_confirm"]), "index.php?mod=usrmgr&action=checkin&step=3&userid=$userid", $func->internal_referer);
	break;

	case "3":
		 $u_data = $db->query_first("SELECT username, type FROM {$config["tables"]["user"]} WHERE userid='$userid'");

		 if ($u_data["username"] != "") {
			// User -> paid = 1
			if ($u_data["type"] < 2) $party->update_user_at_party($userid,1,'',1);
			// Admins -> no paid-change
			else $party->update_user_at_party($userid,'','',1);
			
			$func->confirmation(str_replace("%USER%", $u_data["username"], $lang["usrmgr"]["checkin_success"]), "index.php?mod=usrmgr");
		 } else $func->error($lang["usrmgr"]["checkin_nouser"], "index.php?mod=usrmgr&action=checkin");
	break;
} // switch
?>
