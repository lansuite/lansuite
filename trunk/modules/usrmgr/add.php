<?php 
/*************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.0
*	Filename: 			add.php
*	Module: 			usermanager
*	Main editor:		Michael@one-network.org (bugs *G*), raphael@one-network.org (class design)
*	Additional:			Raphael@one-network.org (Seating)
*	Additional:			denny@one-network.org (some more user details)
*	Additional:			knox@orgapage.net (add.php + change.php combined, some more function, multilanguage)
*	Last change:		15.10.2004
*	Description: 		Adds and changes User. Only accessible by admins.
*	Remarks:
*
**************************************************************************/

if (($auth["type"] >= 2) or (($auth["userid"] == $_GET["userid"]) && $cfg['user_self_details_change'])) {
	include("modules/usrmgr/class_adduser.php");
	$AddUser = new AddUser();

	if ($_GET["step"] == "") $_GET["step"] = 1;

	switch($_GET['step']) {
		default:
			$AddUser->GetDBData($_GET["action"]);
		break;

		case 2:
			$AddUser->CheckErrors($_GET["action"]);
		break;
		
		case 3:
			$party->set_seatcontrol($_GET['userid'],$_GET['seatcontrol']);
			$username = $_GET['username'];
			$pw = $_GET['pw'];
		break;
	}


	switch($_GET['step']) {
		default:
			$AddUser->ShowForm($_GET["action"]);
		break;

		case 2:
			$AddUser->WriteToDB($_GET["action"]);

			$user = $db->query_first("SELECT username FROM {$config["tables"]["user"]} WHERE userid = {$_GET["userid"]}");

			$username = urlencode($_POST['username']);
			$pw = urlencode(base64_encode($_POST['password']));
			
			if(isset($_POST['price_id']) && $auth['type'] > 1){
				$seatprice = $party->price_seatcontrol($_POST['price_id']);
			
				if($seatprice > 0 && $_POST['paid'] > 0 && $_POST['signon']){
					$seat_paid = $party->get_seatcontrol($_GET['userid']);
					$text = str_replace("%PRICE%",$seatprice . " " . $cfg['sys_currency'] ,$lang['usrmgr']['paid_seatcontrol_quest']) .HTML_NEWLINE;
					if($seat_paid){
						$text .= $lang['usrmgr']['paid_seatcontrol_paid'];
					}else{
						$text .= $lang['usrmgr']['paid_seatcontrol_not_paid'];
					}
					$func->question($text,"index.php?mod=usrmgr&action={$_GET["action"]}&umode={$action}&step=". ($_GET["step"] + 1) ."&userid={$_GET["userid"]}&seatcontrol=1&username=$username&priceid={$_POST['price_id']}&pw=$pw","index.php?mod=usrmgr&action={$_GET["action"]}&umode={$action}&step=". ($_GET["step"] + 1) ."&userid={$_GET["userid"]}&seatcontrol=0&username=$username&priceid={$_POST['price_id']}&pw=$pw");
					break;
				}
			}
		
		
		case 3:
			
		//	if(isset($_GET['username'])) $username = $_GET['username'];
		//	if(isset($_GET['pw'])) $pw = $_GET['pw'];
		
			if ($_GET["action"] == "change") $func->confirmation(str_replace("%USER%", urldecode($username), $lang["usrmgr"]["add_editsuccess"]), "index.php?mod=usrmgr&action=details&userid={$_GET["userid"]}");
			else {
				(($cfg["signon_autopw"]) || ($cfg["signon_password_view"]))? $pw_text = HTML_NEWLINE . str_replace("%PASSWORD%", base64_decode(urldecode($pw)), $lang["usrmgr"]["add_pwshow"]) : $pw_text = "";
				$func->confirmation(str_replace("%USER%", urldecode($username), $lang["usrmgr"]["add_success"]) . $pw_text, "index.php?mod=usrmgr&action=details&userid={$_GET["userid"]}");
			}
		break;
	}
} else $func->error("ACCESS_DENIED", "");
?>
