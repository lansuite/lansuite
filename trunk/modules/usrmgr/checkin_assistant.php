<?php

$timestamp 	= time();

include_once("modules/usrmgr/class_adduser.php");
$AddUser = new AddUser();

$user_data = $db->query_first("SELECT username FROM {$config["tables"]["user"]} WHERE userid='{$_GET["userid"]}'");

// Error-Switch
switch($_GET["step"]) {
	case 3:
		$AddUser->GetDBData($_GET["umode"]);
	break;

	case 4:
		$AddUser->CheckErrors($_GET["umode"], $_GET['quick_signon']);
	break;
	
	case 5:
		$party->set_seatcontrol($_GET['userid'],$_GET['seatcontrol']);
		$_POST['username'] = urldecode($_GET['username']);
		$_POST['password'] = base64_decode(urldecode($_GET['pw']));
	break;
}


// Main-Switch
switch($_GET["step"]) {
	// Auswahl: Angemeldet? ja/Nein
	default:
		if($cfg['sys_barcode_on']){
			$dsp->AddBarcodeForm("<strong>" . $lang['barcode']['barcode'] . "</strong>","","index.php?mod=usrmgr&action=entrance&step=3&umode=change&userid=");
		}
		$questionarr[1] = $lang["usrmgr"]["entrance_signedon"];
		$questionarr[2] = $lang["usrmgr"]["entrance_comunity"];
		$questionarr[3] = $lang["usrmgr"]["entrance_notsignedon"];
		$questionarr[4] = $lang["usrmgr"]["entrance_notsignedon_advanced"];
		$linkarr[1]	= "index.php?mod=usrmgr&action=entrance&step=2&umode=change&signon=1";
		$linkarr[2]	= "index.php?mod=usrmgr&action=entrance&step=2&umode=change&signon=0";
		$linkarr[3]	= "index.php?mod=usrmgr&action=entrance&step=3&umode=add&quick_signon=1";
		$linkarr[4]	= "index.php?mod=usrmgr&action=entrance&step=3&umode=add&quick_signon=0";
		$func->multiquestion($questionarr, $linkarr, "");
	break;

	// Wenn Angemeldet: Benutzerauswahl
	case 2:
    include_once('modules/usrmgr/search_checkin_assistant.inc.php');
	break;

	// Benutzerdaten eingeben / ändern
	case 3:
		if (($_POST["paid"] == "") || ($_POST["paid"] == 0)) {
			if ($_GET["umode"] != "add") {
			  $_GET['quick_signon'] = 1;
        $error["paid"] = $lang["usrmgr"]["entrance_notpaid_warning"];
      }
			$_POST["paid"] = 2;
		}
		$_POST["signon"] = 1;

		$dsp->NewContent($lang["usrmgr"]["add_caption"], $lang["usrmgr"]["add_subcaption"]);
		$dsp->SetForm("index.php?mod=usrmgr&action={$_GET["action"]}&umode={$_GET['umode']}&quick_signon={$_GET['quick_signon']}&step=". ($_GET["step"] + 1) ."&userid={$_GET["userid"]}", "signon", "", "multipart/form-data");
		$AddUser->ShowForm($_GET["umode"], $_GET['quick_signon']);
	break;

	// Platzpfand prüfen
	case 4:
		$cfg["signon_autocheckin"] = 1;
		$AddUser->WriteToDB($_GET["umode"], $_GET['quick_signon']);

		$user = $db->query_first("SELECT username FROM {$config["tables"]["user"]} WHERE userid = {$_GET["userid"]}");
		$seatcontrol = $party->get_seatcontrol($_GET['userid']);
		$seatprice = $party->price_seatcontrol($_POST['price_id']);
		$username = urlencode($_POST['username']);
		$pw = urlencode(base64_encode($_POST['password']));

		if ($seatprice > 0 and $_POST['paid'] > 0 and $_POST['signon'] and $seatcontrol == "0"){
			$func->question(str_replace("%PRICE%", $seatprice . " " . $cfg['sys_currency'] ,$lang['usrmgr']['paid_seatcontrol_quest']),"index.php?mod=usrmgr&action={$_GET["action"]}&umode={$_GET['umode']}&step=". ($_GET["step"] + 1) ."&userid={$_GET["userid"]}&seatcontrol=1&username=$username&priceid={$_POST['price_id']}&pw=$pw","index.php?mod=usrmgr&action={$_GET["action"]}&umode={$_GET['umode']}&step=". ($_GET["step"] + 1) ."&userid={$_GET["userid"]}&seatcontrol=0&username=$username&priceid={$_POST['price_id']}&pw=$pw");
			break;
		}
		
		
	// Passwort ausgeben
	case 5:	
		if ($_GET["umode"] == "change") $func->confirmation(str_replace("%USER%", $_POST["username"], $lang["usrmgr"]["add_editsuccess"]), "");
		else {
			(($cfg["signon_autopw"]) || ($cfg["signon_password_view"]))? $pw_text = HTML_NEWLINE . str_replace("%PASSWORD%", $_POST["password"], $lang["usrmgr"]["add_pwshow"]) : $pw_text = "";
			$func->confirmation(str_replace("%USER%", $_POST["username"], $lang["usrmgr"]["add_success"]) . $pw_text, "");
		}

	// Neuen Sitzplatz auswählen?
	case 6:
		$func->question(str_replace("%SEAT%", $seat2->SeatNameLink($_GET["userid"]), $lang["usrmgr"]["entrance_seat_user"]), "index.php?mod=usrmgr&action=entrance&step=7&umode=". $_GET["umode"] ."&userid=". $_GET["userid"], "index.php?mod=usrmgr&action=entrance&step=11&umode=". $_GET["umode"] ."&userid=". $_GET["userid"]);
	break;	

	// Sitzblock auswählen
	case 7:
		if ($_GET['next_userid']) {
			$seat2->AssignSeat($_GET['userid'], $_GET['blockid'], $_GET['row'], $_GET['col']);
			$func->confirmation("Der Sitzplatz wurde erfolgreich reserviert. Sie fahren nun mit dem alten Besitzer dieses Sitzplatzes fort", '');
			$_GET['userid'] = $_GET['next_userid'];
		}

		$mastersearch = new MasterSearch($vars, "index.php?mod=usrmgr&action=entrance&step=7&umode={$_GET["umode"]}&userid={$_GET["userid"]}", "index.php?mod=usrmgr&action=entrance&step=8&umode={$_GET["umode"]}&userid={$_GET["userid"]}&blockid=", '');
		$mastersearch->LoadConfig('seat_blocks', $lang['seat']['ms_search'], $lang['seat']['ms_result']);
		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();
		$templ['index']['info']['content'] .= $mastersearch->GetReturn();
	break;

	// Sitzplatz auswählen
	case 8:
		$dsp->NewContent('Sitzplatz - Informationen', 'Fahren Sie mit der Maus über einen Sitzplatz, um weitere Informationen zu erhalten.');

		$dsp->AddDoubleRow('Sitzplatz', '', 'seating');
		$dsp->AddDoubleRow('Benutzer', '', 'name');
		$dsp->AddDoubleRow('Clan', '', 'clan');
		$dsp->AddDoubleRow('IP', '', 'ip');
		$dsp->AddSingleRow($seat2->DrawPlan($_GET['blockid'], 0, "index.php?mod=usrmgr&action=entrance&step=9&umode={$_GET["umode"]}&userid={$_GET["userid"]}&blockid={$_GET["blockid"]}"));

		$dsp->AddBackButton('index.php?mod=seating', 'seating/show'); 
		$dsp->AddContent();
	break;

	// Belegten Sitzplatz tauschen / löschen?
	case 9:
		$seat = $db->query_first("SELECT s.userid, s.status, u.username, u.firstname, u.name FROM {$config["tables"]["seat_seats"]} AS s
			LEFT JOIN {$config["tables"]["user"]} AS u ON s.userid = u.userid
			WHERE blockid = '{$_GET['blockid']}' AND row = '{$_GET['row']}' AND col = '{$_GET['col']}'");

		if ($seat['status'] == 1) $_GET['step'] = 10;
		elseif ($seat['status'] == 2) {
			$questionarray = array();
			$linkarray = array();

			array_push($questionarray, "Dennoch reservieren. {$seat['username']} hat dadurch anschließend keinen Sitzplatz mehr");
			array_push($linkarray, "index.php?mod=usrmgr&action=entrance&step=10&umode={$_GET["umode"]}&userid={$_GET["userid"]}&blockid={$_GET["blockid"]}&row={$_GET['row']}&col={$_GET['col']}");

			array_push($questionarray, "Dennoch reservieren und {$seat['username']} anschließend einen neuen Sitzplatz aussuchen");
			array_push($linkarray, "index.php?mod=usrmgr&action=entrance&step=7&umode={$_GET["umode"]}&userid={$_GET["userid"]}&blockid={$_GET["blockid"]}&next_userid={$seat['userid']}&row={$_GET['row']}&col={$_GET['col']}");

			array_push($questionarray, 'Aktion abbrechen. Zurück zum Sitzplan');
			array_push($linkarray, "index.php?mod=usrmgr&action=entrance&step=7&umode={$_GET["umode"]}&userid={$_GET["userid"]}&blockid={$_GET["blockid"]}");

			$func->multiquestion($questionarray, $linkarray, "Dieser Sitzplatz ist aktuell belegt durch {$seat['username']} ({$seat['firstname']} {$seat['name']})");
		}
	break;

	case 10:
	case 11:
	break;
}

switch ($_GET['step']) {
	case 10:
		$seat2->AssignSeat($_GET['userid'], $_GET['blockid'], $_GET['row'], $_GET['col']);

	// Erfolgsmeldung zeigen
	case 11:
		$func->confirmation(str_replace("%USER%", $user_data["username"], $lang["usrmgr"]["checkin_success"]), "index.php?mod=usrmgr&action=entrance");
	break;
}

?>
