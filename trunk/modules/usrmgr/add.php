<?php 

function Update($id) {
global $mf;

  // Clan-Management
  include_once("modules/usrmgr/class_clan.php");
  $clan = new Clan();
  if ($_POST['clan_new']) $_POST['clan'] = $clan->Add($_POST['clan_new'], $_POST["clanurl"], $_POST["newclanpw"]);
  if ($_POST['clan']) $clan->AddMember($_POST['clan'], $id);
  else $clan->RemoveMember($id);

	// Update User-Perissions
	$db->query("DELETE FROM {$config["tables"]["user_permissions"]} WHERE userid = $id");
	if ($_POST["permissions"]) foreach ($_POST["permissions"] as $perm) {
		$db->query("INSERT INTO {$config["tables"]["user_permissions"]} SET module = '$perm', userid = $id");
	}

	// Update Party-Signon
	if (isset($_POST['signon']) && $_POST['signon'] == "1") $party->add_user_to_party($id, $_POST['price_id'], $_POST['paid'], $checkin);
	elseif((!isset($_POST['signon']) || $_POST['signon'] == "0") && $auth["type"] > 1){
	 	$party->delete_user_from_party($id);
	}

	// Update Seating
	if ($_POST['paid']) $seat2->ReserveSeatIfPaidAndOnlyOneMarkedSeat($id);
	else $seat2->MarkSeatIfNotPaidAndSeatReserved($id);
	
  if (!$mf->isChange) {
    $add_query2 = $db->query("INSERT INTO {$config["tables"]["usersettings"]} SET userid = '{$_GET["userid"]}'");
    WriteXMLStatFile();
  }

/*
	// Picture Upload
	if ($auth["type"] >= 2) if (file_exists($_FILES['picture']['tmp_name'])) {
		@unlink("ext_inc/user_pics/pic$id.jpg");
		@copy($_FILES["picture"]["tmp_name"], "ext_inc/user_pics/pic$id.jpg");
	}
*/
	return true;
}


function PersoInput($field, $mode, $error = '') {
  global $dsp, $templ, $lang, $auth;

  switch ($mode) {
    case OUTPUT_PROC:
			$_POST[$field .'_1'] = substr($_POST[$field], 0, 11);
			$_POST[$field .'_2'] = substr($_POST[$field], 13, 7);
			$_POST[$field .'_3'] = substr($_POST[$field], 21, 7);
			$_POST[$field .'_4'] = substr($_POST[$field], 35, 1);

    	if ($_POST[$field .'_1'] == '') $_POST[$field .'_1'] = "aaaaaaaaaaD";
    	if ($_POST[$field .'_2'] == '') $_POST[$field .'_2'] = "bbbbbbb";
    	if ($_POST[$field .'_3'] == '') $_POST[$field .'_3'] = "ccccccc";
    	if ($_POST[$field .'_4'] == '') $_POST[$field .'_4'] = "d";

    	$templ['ls']['row']['textfield']['name']	= $field;
    	$templ['ls']['row']['textfield']['value1']	= $_POST[$field .'_1'];
    	$templ['ls']['row']['textfield']['value2']	= $_POST[$field .'_2'];
    	$templ['ls']['row']['textfield']['value3']	= $_POST[$field .'_3'];
    	$templ['ls']['row']['textfield']['value4']	= $_POST[$field .'_4'];
    	if ($error) $templ['ls']['row']['textfield']['errortext']	= $dsp->errortext_prefix . $error . $dsp->errortext_suffix;
    	else $templ['ls']['row']['textfield']['errortext']	= '';
    	if (Optional("perso")) $templ['ls']['row']['textfield']['optional']	= "_optional";

    	return $dsp->FetchModTpl('usrmgr', 'row_perso');
    break;

    case CHECK_ERROR_PROC:
  		$_POST[$field] = $_POST["perso_1"] . "<<" . $_POST["perso_2"] . "<". $_POST["perso_3"] . "<<<<<<<" . $_POST["perso_4"];
  		if ($_POST[$field] == "aaaaaaaaaaD<<bbbbbbb<ccccccc<<<<<<<d") $_POST[$field] = "";
  		if ($_POST[$field] == "<<<<<<<<<<") $_POST[$field] = "";
#  		if (($auth["type"] >= 2 or $missing_fields) and (($_POST[$field] != '') or (Needed($_POST[$field])))){
#  			$perso_res = $signon->CheckPerso($perso);
#  			switch ($perso_res) {
#  				case 2: return str_replace("<", "&lt;", $lang["usrmgr"]["add_err_perso_format"]); break;
#  				case 3: return $lang["usrmgr"]["add_err_perso_cs"]; break;
#  				case 4: return $lang["usrmgr"]["add_err_perso_expired"]; break;
#  			}
#  		}
			return false; // -> Means no error
    break;
  }
}

function BirthdayInput($field, $mode, $error = '') {
  global $dsp, $templ, $lang, $auth, $func;

  switch ($mode) {
    case OUTPUT_PROC:
			if ($_POST['birthday'] == 0) $_POST['birthday'] = 1;
  		$dsp->AddDateTimeRow("birthday", $lang["usrmgr"]["add_birthday"], $_POST['birthday'], $error["birthday"], "", "", (1970 - date("Y")), -5, 1, Optional("birthday"), " onChange=\"WriteAge();\"");
  		$dsp->AddDoubleRow($lang["usrmgr"]["add_u18check"], $dsp->FetchModTPL("usrmgr", "u18check") . " Jahre");
    	return false;
    break;

    case CHECK_ERROR_PROC:
  		// GetBirthdayTimestamp
  		if (($_POST["birthday_value_year"] == (date("Y") - 34)) && ($_POST["birthday_value_month"] == "1") && ($_POST["birthday_value_day"] == "1")) $_POST['birthday'] = 0;
  		else $_POST['birthday'] = $func->date2unixstamp($_POST["birthday_value_year"], $_POST["birthday_value_month"], $_POST["birthday_value_day"], 0, 0, 0);
#  		if (($auth["type"] >= 2 or $missing_fields) and $this->Needed("birthday") and $birthday == 0) $error["birthday"] = $lang["usrmgr"]["add_err_no_birthday"];
			return false; // -> Means no error
    break;
  }
}


function Addr1Input($field, $mode, $error = '') {
  global $dsp, $templ, $lang, $auth, $func;

  switch ($mode) {
    case OUTPUT_PROC:
			if ($_POST['street|hnr'] == '' and $_POST['street'] and $_POST['hnr']) $_POST['street|hnr'] = $_POST['street'] .' '. $_POST['hnr'];
  		$dsp->AddTextFieldRow('street|hnr', $lang['usrmgr']['add_street'], $_POST['street|hnr'], $error, '', Optional('street'));
    	return false;
    break;

    case CHECK_ERROR_PROC:
  		if ($_POST['street|hnr'] != '' or FieldNeeded('street')){
        $pieces = explode(' ', $_POST['street|hnr']);
        $_POST['hnr'] = (int)array_pop($pieces);
        $_POST['street'] = implode(' ', $pieces);

  			if ($_POST['street'] == '') return $lang['usrmgr']['add_err_invalid_street'];
  			elseif ($_POST['hnr'] == 0) return $lang['usrmgr']['add_err_invalid_nr'];
  		}
			return false; // -> Means no error
    break;
  }
}

function Addr2Input($field, $mode, $error = '') {
  global $dsp, $templ, $lang, $auth, $func;

  switch ($mode) {
    case OUTPUT_PROC:
			if ($_POST['plz|city'] == '' and $_POST['plz'] and $_POST['city']) $_POST['plz|city'] = $_POST['plz'] .' '. $_POST['city'];
  		$dsp->AddTextFieldRow('plz|city', $lang['usrmgr']['add_city'], $_POST['plz|city'], $error, '', Optional('city'));
    	return false;
    break;

    case CHECK_ERROR_PROC:
  		if (($_POST['plz|city'] != '') || (FieldNeeded('city'))){
        $pieces = explode(' ', $_POST['plz|city']);
        $_POST['plz'] = (int)array_shift($pieces);
        $_POST['city'] = implode(' ', $pieces);

  			if ($_POST['plz'] == 0 or $_POST['city'] == '') return $lang['usrmgr']['add_err_invalid_city'];
  			elseif (strlen($_POST['plz']) < 4) return $lang['usrmgr']['add_err_invalid_plz'];
  		}
			return false; // -> Means no error
    break;
  }
}


function Optional($key){
	global $cfg;

	if ($cfg["signon_show_".$key] <= 1) return 1;
	else return 0;
}

function FieldNeeded($key){
	global $cfg;

	if ($cfg["signon_show_".$key] == 2) return 1;
	else return 0;
}

function WriteXMLStatFile() {
	global $cfg, $db, $config,$party;

	include_once ("inc/classes/class_xml.php");
	$xml = new xml;
	$output = '<?xml version="1.0" encoding="UTF-8"?'.'>'."\r\n";

	$part_infos = $xml->write_tag("name", $cfg["feed_partyname"], 2);
	$part_infos .= $xml->write_tag("link", $cfg["sys_partyurl"], 2);
	$part_infos .= $xml->write_tag("language", "de-de", 2);
	$lansuite = $xml->write_master_tag("part_infos", $part_infos, 1);

	$registered = $db->query_first("SELECT COUNT(*) AS anz FROM {$config["tables"]["party_user"]} WHERE party_id = {$party->party_id}");
	$paid = $db->query_first("SELECT COUNT(*) AS anz FROM {$config["tables"]["party_user"]} WHERE (paid = 1) AND party_id = {$party->party_id}");

	$stats = $xml->write_tag("guests", ($registered["anz"] - 1), 2);
	$stats .= $xml->write_tag("paid_guests", $paid["anz"], 2);
	$stats .= $xml->write_tag("max_guests", $_SESSION['party_info']['max_guest'] , 2);
	$stats .= $xml->write_tag("signon_start", $_SESSION['party_info']['s_startdate'], 2);
	$stats .= $xml->write_tag("signon_end", $_SESSION['party_info']['s_enddate'], 2);
	$lansuite .= $xml->write_master_tag("stats", $stats, 1);

	$output .= $xml->write_master_tag("lansuite version=\"1.0\"", $lansuite, 0);

	if (is_writable("ext_inc/party_infos/")) {
		if ($fp = @fopen("ext_inc/party_infos/infos.xml", "w")) {
			if (!@fwrite($fp, $output)) return false;
		@fclose($fp);
		} else return false;
	} else return false;
	return true;
}



if ($auth['type'] >= 2 or ($auth['userid'] == $_GET['userid'] and $cfg['user_self_details_change'])) {
  $party_user = $db->query_first("SELECT * FROM {$config['tables']['party_user']} WHERE user_id = ". (int)$_GET["userid"] ." AND party_id={$party->party_id}");
  if (!isset($_POST['signon'])) $_POST['signon'] = $party_user['party_id'];
  if (!isset($_POST['price_id'])) $_POST['price_id'] = $party_user['price_id'];
  if (!isset($_POST['paid'])) $_POST['paid'] = $party_user['paid'];

  include_once('inc/classes/class_masterform.php');
  $mf = new masterform();

  if (!$quick_signon) {
    if (($auth['type'] >= 2 or $missing_fields)) $mf->AddField($lang['usrmgr']['add_username'], 'username');
    else $mf->AddField($lang['usrmgr']['add_username'], '', IS_TEXT_MESSAGE, $lang["usrmgr"]["add_limitedright_hint"]);

    $mf->AddField($lang['usrmgr']['add_firstname'], 'firstname', '', '', Optional('firstname'));
    $mf->AddField($lang['usrmgr']['add_lastname'], 'name', '', '', Optional('lastname'));
    $mf->AddGroup('Namen');

    if ($auth['type'] >= 2) {
      $selections = array();
      $selections['1'] = $lang['usrmgr']['add_type_user'];
      $selections['2'] = $lang['usrmgr']['add_type_admin'];
      if ($auth['type'] >= 3) $selections['3'] = $lang['usrmgr']['add_type_operator'];
      $mf->AddField($lang['usrmgr']['add_type'], 'type', IS_SELECTION, $selections, '', '', 1);

      $selections = array();
      $res = $db->query("SELECT module.name, module.caption FROM {$config["tables"]["modules"]} AS module
      	LEFT JOIN {$config["tables"]["menu"]} AS menu ON menu.module = module.name
      	WHERE menu.file != ''
      	GROUP BY menu.module");
      while($row = $db->fetch_array($res)) $selections[$row['name']] = $row['caption'];
      $db->free_result($res);
      $mf->AddField($lang['usrmgr']['add_permission'], 'permissions', IS_MULTI_SELECTION, $selections, FIELD_OPTIONAL);
      $mf->AddGroup('Rechte');
    }
  }

  $mf->AddField($lang['usrmgr']['add_email'], 'email');
  if (!$cfg['signon_autopw'] and $_GET['action'] != 'change') $mf->AddField($lang['usrmgr']['add_password'], 'password', IS_NEW_PASSWORD);
  $mf->AddGroup('Account');

  if ($auth['type'] >= 2) {
    $mf->AddField($lang['usrmgr']['add_signon'], 'signon', 'tinyint(1)', '', '', '', 3);

    $party->GetPriceDropdown((int)$_POST["group_id"], (int)$_POST["price_id"]);

    $selections = array();
    $selections['0'] = $lang['usrmgr']['add_paid_no'];
    $selections['1'] = $lang['usrmgr']['add_paid_vvk'];
    $selections['2'] = $lang['usrmgr']['add_paid_ak'];
    $mf->AddField($lang['usrmgr']['add_paid'], 'paid', IS_SELECTION, $selections);

    $party->GetUserGroupDropdown('NULL', 1, (int)$_POST['group_id'], true);
    $mf->AddGroup('Party');
  }


  if (!$quick_signon) {
    // Clan select
    $clans_query = $db->query("SELECT c.clanid, c.name, c.url, COUNT(u.clanid) AS members
    		FROM {$config["tables"]["clan"]} AS c
    		LEFT JOIN {$config["tables"]["user"]} AS u ON c.clanid = u.clanid
    		WHERE u.clanid IS NULL or u.type >= 1
    		GROUP BY c.clanid
    		ORDER BY c.name
    		");
    $selections = array();
    $selections[''] = '---';
    while($row = $db->fetch_array($clans_query)) {
    	if ($_POST['clan'] == '') $_POST['clan'] = $row['clanid'];
    	if ($_POST['clanurl'] == '') $_POST['clanurl'] = $row['url'];
      $selections[$row['clanid']] = $row['name'] .' '. $row['members'];
    }
    $db->free_result($clans_query);
    $mf->AddField($lang['usrmgr']['add_existing_clan'], 'clan', IS_SELECTION, $selections, Optional('clan'));
    $mf->AddField($lang['usrmgr']['chpwd_password2'], 'clanpw', IS_PASSWORD, '', FIELD_OPTIONAL);
    $mf->AddField($lang['usrmgr']['add_create_clan'], 'new_clan_select', 'tinyint(1)', '', FIELD_OPTIONAL, '', 3);
    $mf->AddField($lang['usrmgr']['add_create_clan'], 'clan_new', '', '', FIELD_OPTIONAL);
    $mf->AddField($lang['usrmgr']['add_clanurl'], 'clanurl', '', '', FIELD_OPTIONAL);
    $mf->AddField($lang['usrmgr']['chpwd_password'], 'newclanpw', IS_NEW_PASSWORD, '', FIELD_OPTIONAL);
    $mf->AddGroup('Clan');

    $mf->AddField($lang['usrmgr']['add_wwcl_id'], 'wwclid', '', '', Optional('wwclid'));
    $mf->AddField($lang['usrmgr']['add_ngl_id'], 'nglid', '', '', Optional('nglid'));
    $mf->AddGroup('Leagues');

    $mf->AddField($lang['usrmgr']['add_street'], 'street|hnr', IS_CALLBACK, 'Addr1Input', Optional('street'));
    $mf->AddField($lang['usrmgr']['add_city'], 'plz|city', IS_CALLBACK, 'Addr2Input', Optional('city'));
    $mf->AddGroup('Adresse');

    if (($auth['type'] >= 2 or $missing_fields)) {
      $mf->AddField($lang['usrmgr']['add_perso'], 'perso', IS_CALLBACK, 'PersoInput', Optional('perso'));
      $mf->AddField($lang['usrmgr']['add_birthday'], 'birthday', IS_CALLBACK, 'BirthdayInput', Optional('birthday'));
    }
    $selections = array();
    $selections['0'] = $lang['usrmgr']['add_gender_no'];
    $selections['1'] = $lang['usrmgr']['add_gender_m'];
    $selections['2'] = $lang['usrmgr']['add_gender_f'];
    $mf->AddField($lang['usrmgr']['add_gender'], 'sex', IS_SELECTION, $selections, Optional('gender'));
    $mf->AddField($lang['usrmgr']['add_newsletter'], 'newsletter', '', '', Optional('newsletter'));
    if (($auth['type'] >= 2)) {
      $mf->AddField($lang['usrmgr']['add_picture'], 'picture', IS_FILE_UPLOAD, 'ext_inc/user_pics/', Optional('picture'));
      $mf->AddField($lang['usrmgr']['add_comment'], 'comment', '', HTML_ALLOWED, FIELD_OPTIONAL);
    }
    $mf->AddGroup('Misc.');
  }

  $mf->AdditionalDBUpdateFunction = 'Update';
  if ($mf->SendForm('index.php?mod=usrmgr&action='. $_GET['action'], 'user', 'userid', $_GET['userid'])) {
    if (!$mf->isChange) {
      WriteXMLStatFile();
    }
  }
}
/*


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
    	$dsp->NewContent($lang["usrmgr"]["add_caption"], $lang["usrmgr"]["add_subcaption"]);
    	$dsp->SetForm("index.php?mod=usrmgr&action={$_GET["action"]}&umode={$action}&quick_signon={$_GET['quick_signon']}&step=". ($_GET["step"] + 1) ."&userid={$_GET["userid"]}", "signon", "", "multipart/form-data");
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
*/
?>
