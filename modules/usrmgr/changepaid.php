<?php

$userid 	= $_GET["userid"];

switch($_GET["step"]) {
	// Search User
	default:
    include_once('modules/usrmgr/search.inc.php');
	break;

	// Confirm Action
	case 2:
		if ($_POST['checkbox']) {
			$text = "";
			$userids = "";
			foreach ($_POST['checkbox'] AS $userid) {
				$user_data = $db->query_first("SELECT user.username, party.paid FROM {$config["tables"]["user"]} AS user LEFT JOIN {$config["tables"]["party_user"]} AS party ON user.userid = party.user_id WHERE userid = '$userid' AND party_id={$party->party_id}");
				if ($user_data["paid"]) {
					$a1 = "<font color=\"green\">{$lang['usrmgr']['chpaid_paid']}</font>";
					$a2 = "";
				} else {
					$a1 = "<font color=\"red\">{$lang['usrmgr']['chpaid_not_paid']}</font>";
					$a2 = "";
				}
				str_replace("%USER%", "<b>{$user_data["username"]}</b>", str_replace("%A1%", $a1, str_replace("%A2%", $a2, $lang['usrmgr']['chpaid_chpaid_quest'])));
				$userids .= "$userid,";
			}
			$userids = substr($userids, 0, strlen($userids) - 1);
			$func->question($text, "index.php?mod=usrmgr&action=changepaid&step=3&userids=$userids", "index.php?mod=usrmgr&action=changepaid");

		} elseif ($_GET["userid"]) {
			$user_data = $db->query_first("SELECT u.username, p.paid FROM {$config["tables"]["user"]} AS u
				LEFT JOIN {$config["tables"]["party_user"]} AS p ON u.userid=p.user_id
				WHERE u.userid = '{$_GET['userid']}' AND p.party_id={$party->party_id}
				");

			if ($user_data["username"]){
				if ($user_data["paid"] == "NULL") $func->error($lang["usrmgr"]["chpaid_notatparty"],"index.php?mod=usrmgr&action=details&userid=$userid");
				elseif ($user_data["paid"])
					$func->question(str_replace("%USER%", $user_data["username"], $lang["usrmgr"]["chpaid_notpaidquest"]), "index.php?mod=usrmgr&action=changepaid&step=3&userid=$userid&paid=0", "index.php?mod=usrmgr&action=details&userid=$userid");
				else
					$func->question(str_replace("%USER%", $user_data["username"], $lang["usrmgr"]["chpaid_paidquest"]), "index.php?mod=usrmgr&action=changepaid&step=3&userid=$userid&paid=1", "index.php?mod=usrmgr&action=details&userid=$userid");
			}else {
				$func->error($lang["usrmgr"]["chpaid_notatparty"],"index.php?mod=usrmgr&action=details&userid=$userid");
			}


		} else $func->error($lang["usrmgr"]["chpaid_error_user"], "index.php?mod=usrmgr&action=changepaid");	
	break;

	case 3:
		if($_GET['userid']){
			$user_data = $db->query_first("SELECT price_id FROM {$config["tables"]["party_user"]} WHERE user_id = '$userid' AND party_id={$party->party_id}");
			$seatprice = $party->price_seatcontrol($user_data['price_id']);
				if($seatprice > 0 && $_GET['paid'] == 1){
						$seat_paid = $party->get_seatcontrol($_GET['userid']);
						$text = str_replace("%PRICE%",$seatprice . " " . $cfg['sys_currency'] ,$lang['usrmgr']['paid_seatcontrol_quest']) . HTML_NEWLINE;
						if($seat_paid){
							$text .= $lang['usrmgr']['paid_seatcontrol_paid'];
						}else{
							$text .= $lang['usrmgr']['paid_seatcontrol_not_paid'];
						}
					$func->question($text,"index.php?mod=usrmgr&action=changepaid&step=4&userid=$userid&paid=1&seatcontrol=1","index.php?mod=usrmgr&action=changepaid&step=4&userid=$userid&paid=1&seatcontrol=0");
					break;
				}elseif($seatprice > 0 && $_GET['paid'] == 0){
						$seat_paid = $party->get_seatcontrol($_GET['userid']);
						$text = str_replace("%PRICE%",$seatprice . " " . $cfg['sys_currency'] ,$lang['usrmgr']['paid_seatcontrol_quest']) .HTML_NEWLINE;
						if($seat_paid){
							$text .= $lang['usrmgr']['paid_seatcontrol_paid'];
						}else{
							$text .= $lang['usrmgr']['paid_seatcontrol_not_paid'];
						}
					$func->question($text,"index.php?mod=usrmgr&action=changepaid&step=4&userid=$userid&paid=0&seatcontrol=1","index.php?mod=usrmgr&action=changepaid&step=4&userid=$userid&paid=0&seatcontrol=0");
					break;
				}
		}
	
	// Update DB
	case 4:
		if ($_GET["userids"]) {
			$userids = split(",", $_GET["userids"]);
			foreach ($userids as $userid) {
				$user_data = $db->query_first("SELECT p.paid FROM {$config["tables"]["party_user"]} AS p WHERE user_id = '$userid' AND party_id={$party->party_id}");
				if ($user_data["paid"]) $paid = 0;
				else $paid = 1;
				$db->query("UPDATE {$config["tables"]["party_user"]} SET paid = '$paid' WHERE user_id = $userid AND party_id={$party->party_id} LIMIT 1");
				if ($paid == 1) $seat2->ReserveSeatIfPaidAndOnlyOneMarkedSeat($userid);
				else $seat2->MarkSeatIfNotPaidAndSeatReserved($userid);
			}
		} else {
      $db->query("UPDATE {$config["tables"]["party_user"]} SET paid = '{$_GET["paid"]}' WHERE user_id = $userid AND party_id={$party->party_id} LIMIT 1");
      if ($_GET["paid"] == 1) $seat2->ReserveSeatIfPaidAndOnlyOneMarkedSeat($userid);
      else $seat2->MarkSeatIfNotPaidAndSeatReserved($userid);
    }

		if (isset($_GET['seatcontrol'])){
			$party->set_seatcontrol($userid,$_GET['seatcontrol']);
		}
		$func->confirmation($lang["usrmgr"]["chpaid_success"], "index.php?mod=usrmgr&action=details&userid=$userid");

		if ($cfg['signon_ask_paid_email']) {
			$q_array[] = $lang["usrmgr"]["chpaid_mail_no"];
			$l_array[] = "index.php?mod=usrmgr&action=changepaid";
			$q_array[] = $lang["usrmgr"]["chpaid_mail_sys"];
			$l_array[] = "index.php?mod=usrmgr&action=changepaid&step=5&sysmail=1&userid=$userid&userids={$_GET["userids"]}";
			$q_array[] = $lang["usrmgr"]["chpaid_mail_inet"];
			$l_array[] = "index.php?mod=usrmgr&action=changepaid&step=5&inetmail=1&userid=$userid&userids={$_GET["userids"]}";
			$q_array[] = $lang["usrmgr"]["chpaid_mail_both"];
			$l_array[] = "index.php?mod=usrmgr&action=changepaid&step=5&sysmail=1&inetmail=1&userid=$userid&userids={$_GET["userids"]}";
			$func->multiquestion($q_array, $l_array, $lang["usrmgr"]["chpaid_mail_quest"]);
		} else $_GET["step"] = 5;
	break;

	// Send Mail
	case 5:
		$templ['signon']['username'] = $user_data["username"];
		$templ['signon']['partyname'] = $_SESSION['party_info']['name'];

		if ($_GET["userids"]) $userids = split(",", $_GET["userids"]);
		else $userids[] = $_GET["userid"];

		$confirmation = "";
		$error = "";

		foreach ($userids as $userid) {
			$user_data = $db->query_first("SELECT username, party.paid, email from {$config["tables"]["user"]} LEFT JOIN {$config["tables"]["party_user"]} AS party ON userid=party.user_id WHERE userid = $userid AND party.party_id={$party->party_id}");

			if ($user_data["paid"]) $msgtext = $dsp->FetchModTpl("usrmgr", "mail_paid");
			else $msgtext = $dsp->FetchModTpl("usrmgr", "mail_not_paid");

			$signonmail = New Mail();
			if ($_GET['sysmail']) {
				if ($signonmail->create_sys_mail($userid, $lang["usrmgr"]["chpaid_mail_subj"], $msgtext)) 
					$confirmation .= "{$user_data["username"]} (System-Mail)" . HTML_NEWLINE;
				else $error .= "{$user_data["username"]} (System-Mail)" . HTML_NEWLINE;
			}

			if ($_GET['inetmail']) {
				if ($signonmail->create_inet_mail($user_data["username"], $user_data["email"], $lang["usrmgr"]["chpaid_mail_subj"], $msgtext, $auth["email"])) $confirmation .= "{$user_data["username"]} (Internet-Mail)" . HTML_NEWLINE;
				else $error .= "{$user_data["username"]} (Internet-Mail)" . HTML_NEWLINE;
			}
		}

		if ($confirmation) $func->confirmation($lang['usrmgr']['chpaid_mail_success'] . HTML_NEWLINE . "$confirmation", "index.php?mod=usrmgr&action=details&userid=$userid");
		if ($error) $func->confirmation($lang['usrmgr']['chpaid_mail_failed'] . HTML_NEWLINE . "$error", "index.php?mod=usrmgr&action=details&userid=$userid");
	break;
} // End: Switch


switch($_GET["step"]) {
	case 5:
		// Delete Seat
		
		if (!$_GET["paid"] && !is_array($userids)) {
			$get_seat = $db->query_first("SELECT s.seatid, s.blockid FROM {$config["tables"]["seat_seats"]} AS s LEFT JOIN {$config["tables"]["seat_block"]} AS b ON s.blockid=b.blockid WHERE s.userid ='$userid' AND b.party_id={$party->party_id}");
			if ($get_seat['blockid']) {
				$seat = new seat;
				$dia_quest[2] = str_replace("%SEAT%", $seat->display_seat_link("usrmgr", $userid), $lang["usrmgr"]["chpaid_seat_quest"]);
				$dia_link[] = "index.php?mod=seating&action=free_seat&step=4&seatid=". $get_seat['seatid'] ."&userid=$userid";
				$dia_sel[] = "yes";
				$func->dialog($dia_quest, $dia_link, $dia_sel);
			}
		}
	break;
} //  switch action
?>
