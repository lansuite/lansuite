<?php

$userid = $_GET['userid'];
$timestamp = time();

switch($_GET['step']) {
	default:
    include_once('modules/usrmgr/search.inc.php');
	break;

	case "2":
	  // Is this user registered to the current party?
		$user_at_party = $db->query_first("SELECT 1 AS found FROM {$config['tables']['party_user']} WHERE user_id = {$_GET["userid"]} AND party_id={$party->party_id}");
		if ($user_at_party['found']) {
      $u_data = $db->query_first("SELECT u.*, pu.*
        FROM {$config['tables']['user']} AS u
        LEFT JOIN {$config['tables']['party_user']} AS pu ON u.userid = pu.user_id
        WHERE u.userid={$_GET["userid"]} AND pu.party_id={$party->party_id}");
  
        $q_text = "";
        if ((!$u_data["paid"]) && ($u_data["type"] < 2)) $q_text = $lang["usrmgr"]["checkin_not_paid"] .HTML_NEWLINE . HTML_NEWLINE;
        $q_text .= str_replace("%USER%", $u_data["username"], $lang["usrmgr"]["checkin_confirm"]);
        
        $func->question($q_text, "index.php?mod=usrmgr&action=checkin&step=3&userid=$userid", $func->internal_referer);
    } else $func->error($lang["usrmgr"]["checkin_not_signed_on"], "index.php?mod=usrmgr&action=checkin");
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
