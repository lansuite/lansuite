<?php

$userid = $_GET['userid'];
$timestamp = time();

$user_data = $db->query_first("SELECT username FROM {$config["tables"]["user"]} WHERE userid='$userid'");

switch($_GET['step']) {
	default:
    $additional_where = "p.checkin > 0 AND p.checkout = 0 AND p.party_id = {$party->party_id}";
    $current_url = 'index.php?mod=usrmgr&action=checkout';
    $target_url = 'index.php?mod=usrmgr&action=checkout&step=2&userid=';
    include_once('modules/usrmgr/search_basic_userselect.inc.php');
	break;

	case 2:
	 if ($user_data["username"]) { 
  		$questionarray = array();
  		$linkarray = array();
  
  		array_push($questionarray, $lang['usrmgr']['checkout_quest_a1']);
  		array_push($linkarray, "index.php?mod=usrmgr&action=checkout&step=3&userid=$userid");

  		array_push($questionarray, $lang['usrmgr']['checkout_quest_a2']);
  		array_push($linkarray, "index.php?mod=usrmgr&action=checkout&step=4&userid=$userid");

  		array_push($questionarray, $lang['usrmgr']['checkout_quest_cancel']);
  		array_push($linkarray, $func->internal_referer);
  	
  		$func->multiquestion($questionarray, $linkarray, str_replace("%USER%", $user_data["username"], $lang["usrmgr"]["checkout_quest"]));
	 } else $func->error($lang["usrmgr"]["checkout_nouser"], "index.php?mod=usrmgr&action=checkout");
	break;

  case 10:
		$row = $db->query_first("SELECT username  FROM {$config["tables"]["user"]} WHERE userid='$userid'");

		$func->question(str_replace("%USER%", $row["username"], $lang["usrmgr"]["checkin_re_confirm"]), "index.php?mod=usrmgr&action=checkout&step=4&userid=$userid", $func->internal_referer);  
  break;

  // Checkout
	case 3:
		$db->query("UPDATE {$config["tables"]["party_user"]} SET checkout='$timestamp' WHERE user_id='$userid' AND party_id={$party->party_id}");
	 	$func->confirmation(str_replace("%USER%", $user_data["username"], $lang["usrmgr"]["checkout_success"]), "index.php?mod=usrmgr");

  break;

  // Reset Checkin + Checkout
  case 4:
		$db->query("UPDATE {$config["tables"]["party_user"]} SET checkin = '0', checkout = '0' WHERE user_id='$userid' AND party_id={$party->party_id}");
	 	$func->confirmation(str_replace("%USER%", $user_data["username"], $lang["usrmgr"]["checkout_success"]), "index.php?mod=usrmgr");
  break;
} // switch
?>
