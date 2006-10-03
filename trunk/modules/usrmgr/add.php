<?php

include_once("modules/usrmgr/class_usrmgr.php");
$usrmgr = new UsrMgr();

function Update($id) {
global $mf, $db, $config, $auth, $party, $seat2, $usrmgr, $func, $lang, $cfg;

  // Clan-Management
  include_once("modules/usrmgr/class_clan.php");
  $clan = new Clan();
  if ($_POST['new_clan_select']) $_POST['clan'] = $clan->Add($_POST['clan_new'], $_POST["clanurl"], $_POST["newclanpw"]);
  if ($_POST['clan']) $clan->AddMember($_POST['clan'], $id);
  else $clan->RemoveMember($id);

	// Update User-Perissions
	if ($id) {
  	$db->query("DELETE FROM {$config["tables"]["user_permissions"]} WHERE userid = $id");
  	if ($_POST["permissions"]) foreach ($_POST["permissions"] as $perm) {
  		$db->query("INSERT INTO {$config["tables"]["user_permissions"]} SET module = '$perm', userid = $id");
  	}
  }

	// Update Party-Signon
	if (isset($_POST['signon']) and $_POST['signon'] == "1") $party->add_user_to_party($id, $_POST['price_id'], $_POST['paid'], $checkin);
	elseif ((!isset($_POST['signon']) or $_POST['signon'] == "0") and $auth["type"] > 1) $party->delete_user_from_party($id);

	// Update Seating
	if ($_POST['paid']) $seat2->ReserveSeatIfPaidAndOnlyOneMarkedSeat($id);
	else $seat2->MarkSeatIfNotPaidAndSeatReserved($id);
  
  // If new user has been added
  if (!$mf->isChange) {
    if ($id) $add_query2 = $db->query("INSERT INTO {$config["tables"]["usersettings"]} SET userid = '$id'");
    $usrmgr->WriteXMLStatFile();

  	if ($cfg["signon_password_mail"]) {
  		if ($usrmgr->SendSignonMail()) $func->confirmation($lang['signon']['add_pw_mail_success']);
  		else {
  			$func->error($lang['signon']['add_pw_mail_failed'] .'. Error-Text: '. $mail->error);
  			$cfg['signon_password_view'] = 1;
  		}
    }

    // Show passwort, if wanted, or has mail failed
    if ($cfg['signon_password_view']) $func->information(str_replace("%PASSWORD%", "<b>". $_COOKIE['tmp_pass'] ."</b>", $lang["signon"]["add_pw_text"]));
    $_COOKIE['tmp_pass'] = '';
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



function CheckClanPW ($clanpw) {
  global $db, $config, $auth;

  if (!$_POST['new_clan_select'] and $auth['type'] <= 1) {
    $clan = $db->query_first("SELECT password FROM {$config['tables']['clan']} WHERE clanid = '{$_POST['clan']}'");
    if ($clan['password'] and $clan['password'] != md5($clanpw)) return 'Wrong PW!';
  }
  return false;
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
        $_POST['plz'] = array_shift($pieces);
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

function ShowField($key){
	global $cfg;

	if ($cfg["signon_show_".$key] > 0) return 1;
	else return 0;
}


if ($auth['type'] >= 2 or !$_GET['userid'] or ($auth['userid'] == $_GET['userid'] and $cfg['user_self_details_change'])) {
  $party_user = $db->query_first("SELECT * FROM {$config['tables']['party_user']} WHERE user_id = ". (int)$_GET["userid"] ." AND party_id={$party->party_id}");
  include_once('inc/classes/class_masterform.php');
  $mf = new masterform();

  if (count($_POST) == 0) $_POST['signon'] = $party_user['party_id'];
  if (!isset($_POST['price_id'])) $_POST['price_id'] = $party_user['price_id'];
  if (!isset($_POST['paid'])) $_POST['paid'] = $party_user['paid'];

  if (!$DoSignon) {
    if (!$quick_signon) {
      // If Admin, Creating a new user, or Missing fields:
      //   Show Username Field
      if (($auth['type'] >= 2 or !$_GET['userid'] or $missing_fields)) $mf->AddField($lang['usrmgr']['add_username'], 'username');
      else $mf->AddField($lang['usrmgr']['add_username'], '', IS_TEXT_MESSAGE, $lang["usrmgr"]["add_limitedright_hint"]);

      if (ShowField('firstname')) $mf->AddField($lang['usrmgr']['add_firstname'], 'firstname', '', '', Optional('firstname'));
      if (ShowField('lastname')) $mf->AddField($lang['usrmgr']['add_lastname'], 'name', '', '', Optional('lastname'));
      $mf->AddGroup('Namen');

      // If Admin: Usertype and Module-Permissions
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

        if (!$_GET['mf_step']) {
          $res = $db->query("SELECT module FROM {$config["tables"]["user_permissions"]} WHERE userid = {$_GET['userid']}");
          while($row = $db->fetch_array($res)) $_POST["permissions"][] = $row["module"];
          $db->free_result($res);
        }
        
        $mf->AddField($lang['usrmgr']['add_permission'], 'permissions', IS_MULTI_SELECTION, $selections, FIELD_OPTIONAL);
        $mf->AddGroup('Rechte');
      }
    }
    // If not admin and user is created (not changed)
    if ($auth['type'] < 2 and !$_GET['userid']) $mf->AddFix('type', 1);

    $mf->AddField($lang['usrmgr']['add_email'], 'email');
    if ($_GET['action'] != 'change') {
      if ($cfg['signon_autopw']) {
        $_COOKIE['tmp_pass'] = $usrmgr->GeneratePassword();
        $mf->AddFix('password', md5($_COOKIE['tmp_pass']));
      }
      else $mf->AddField($lang['usrmgr']['add_password'], 'password', IS_NEW_PASSWORD);
    }
    $mf->AddGroup($lang['usrmgr']['account']);
  }
  
  // If Admin: Signon and Payed options
  if ($auth['type'] >= 2 or !$_GET['userid'] or $DoSignon) {
    ($auth['type'] >= 2) ? $DependOn = 3 : $DependOn = 1;
    if (!$_GET['userid'] or $DoSignon) {
      if (ShowField('voll')) $DependOn++;
      if (ShowField('agb') and !$cfg['signon_alwaysagb']) $DependOn++;
    }
    $mf->AddField($lang['usrmgr']['add_signon'], 'signon', 'tinyint(1)', '', FIELD_OPTIONAL, '', $DependOn);
    $party->GetPriceDropdown((int)$_POST["group_id"], (int)$_POST["price_id"]);

    if ($auth['type'] >= 2) {
      $selections = array();
      $selections['0'] = $lang['usrmgr']['add_paid_no'];
      $selections['1'] = $lang['usrmgr']['add_paid_vvk'];
      $selections['2'] = $lang['usrmgr']['add_paid_ak'];
      $mf->AddField($lang['usrmgr']['add_paid'], 'paid', IS_SELECTION, $selections);

      $party->GetUserGroupDropdown('NULL', 1, (int)$_POST['group_id'], true);
    }

    // AGB and Vollmacht, if new user
    if (!$_GET['userid'] or $DoSignon) {
    	if (ShowField('voll')) $mf->AddField($lang["signon"]["add_vollmacht"] .'|'. str_replace("%LINK%", "<a href=\"". $cfg["signon_volllink"] ."\" target=\"new\">U18 Vollmacht</a>", str_replace("%NAME%", $_SESSION['party_info']['name'], $lang["signon"]["add_vollmacht_detail"])), 'vollmacht', 'tinyint(1)');

      if (ShowField('agb')) {
      	($cfg['signon_agb_targetblank']) ? $target = 'target="_blank"' : $target = '';
        $mf->AddField($lang["signon"]["add_agb"] .'|'. str_replace("%LINK%", "<a href=\"". $cfg["signon_agblink"] ."\"$target>AGB</a>", str_replace("%NAME%", $_SESSION['party_info']['name'], $lang["signon"]["add_agb_detail"])), 'agb', 'tinyint(1)');
      }
    }

    $mf->AddGroup('Party "'. $_SESSION['party_info']['name'] .'"');
  }

  if (!$DoSignon) {
    if (!$quick_signon) {
      // Clan Options
      if (ShowField('clan')) {
      	if (!isset($_POST['clan'])) {
          $users_clan = $db->query_first("SELECT clanid FROM {$config["tables"]["user"]} WHERE userid = ". (int)$_GET['userid']);
          $_POST['clan'] = $users_clan['clanid'];
        }

        $selections = array();
        $selections[''] = '---';
        $PWClans = array();
        $clans_query = $db->query("SELECT c.clanid, c.name, c.url, c.password, COUNT(u.clanid) AS members
        		FROM {$config["tables"]["clan"]} AS c
        		LEFT JOIN {$config["tables"]["user"]} AS u ON c.clanid = u.clanid
        		WHERE u.clanid IS NULL or u.type >= 1
        		GROUP BY c.clanid
        		ORDER BY c.name
        		");
        while ($row = $db->fetch_array($clans_query)) {
          $selections[$row['clanid']] = $row['name'] .' ('. $row['members'] .')';
          if ($row['password']) $PWClans[] = $row['clanid'];
        }
        $db->free_result($clans_query);

        $mf->AddField($lang['usrmgr']['add_existing_clan'], 'clan', IS_SELECTION, $selections, Optional('clan'), '', 1, $PWClans);
        $mf->AddField($lang['usrmgr']['add_password'], 'clanpw', IS_PASSWORD, '', FIELD_OPTIONAL, 'CheckClanPW');
        $mf->AddField($lang['usrmgr']['add_create_clan'], 'new_clan_select', 'tinyint(1)', '', FIELD_OPTIONAL, '', 3);
        $mf->AddField($lang['usrmgr']['add_create_clan'], 'clan_new', '', '', FIELD_OPTIONAL);
        if (ShowField('clanurl')) $mf->AddField($lang['usrmgr']['add_clanurl'], 'clanurl', '', '', FIELD_OPTIONAL);
        $mf->AddField($lang['usrmgr']['chpwd_password'], 'newclanpw', IS_NEW_PASSWORD, '', FIELD_OPTIONAL);
        $mf->AddGroup($lang['usrmgr']['clan']);
      }

      // Leagues
      if (ShowField('wwcl_id')) $mf->AddField($lang['usrmgr']['add_wwcl_id'], 'wwclid', '', '', Optional('wwclid'));
      if (ShowField('ngl_id')) $mf->AddField($lang['usrmgr']['add_ngl_id'], 'nglid', '', '', Optional('nglid'));
      $mf->AddGroup($lang['usrmgr']['leagues']);

      // Address
      if (ShowField('street')) $mf->AddField($lang['usrmgr']['add_street'], 'street|hnr', IS_CALLBACK, 'Addr1Input', Optional('street'));
      if (ShowField('city')) $mf->AddField($lang['usrmgr']['add_city'], 'plz|city', IS_CALLBACK, 'Addr2Input', Optional('city'));
      $mf->AddGroup($lang['usrmgr']['address']);

      // Contact
      if (ShowField('telefon')) $mf->AddField($lang['usrmgr']['telefon'], 'telefon', '', '', Optional('telefon'));
      if (ShowField('handy')) $mf->AddField($lang['usrmgr']['handy'], 'handy', '', '', Optional('telefon'));
      if (ShowField('icq')) $mf->AddField('ICQ', 'icq', '', '', Optional('icq'));
      if (ShowField('msn')) $mf->AddField('MSN', 'msn', '', '', Optional('msn'));
      if (ShowField('skype')) $mf->AddField('Skype', 'skype', '', '', Optional('skype'));
      $mf->AddGroup($lang['usrmgr']['contact']);

      // Misc (Perso + Birthday + Gender + Newsletter)
      if (($auth['type'] >= 2 or !$_GET['userid'] or $missing_fields)) {
        if (ShowField('perso')) $mf->AddField($lang['usrmgr']['add_perso'], 'perso', IS_CALLBACK, 'PersoInput', Optional('perso'));
        if (ShowField('birthday')) $mf->AddField($lang['usrmgr']['add_birthday'], 'birthday', IS_CALLBACK, 'BirthdayInput', Optional('birthday'));
      }
      if (ShowField('gender')) {
        $selections = array();
        $selections['0'] = $lang['usrmgr']['add_gender_no'];
        $selections['1'] = $lang['usrmgr']['add_gender_m'];
        $selections['2'] = $lang['usrmgr']['add_gender_f'];
        $mf->AddField($lang['usrmgr']['add_gender'], 'sex', IS_SELECTION, $selections, Optional('gender'));
      }
      if (ShowField('newsletter')) $mf->AddField($lang['usrmgr']['add_newsletter'], 'newsletter', '', '', Optional('newsletter'));

      // If Admin: Picture and Comment
      if (($auth['type'] >= 2)) {
        $mf->AddField($lang['usrmgr']['add_picture'], 'picture', IS_FILE_UPLOAD, 'ext_inc/user_pics/', Optional('picture'));
        $mf->AddField($lang['usrmgr']['add_comment'], 'comment', '', HTML_ALLOWED, FIELD_OPTIONAL);
      }
      $mf->AddGroup($lang['usrmgr']['misc']);
    }
  }
  

  $mf->AdditionalDBUpdateFunction = 'Update';
  $mf->SendForm('index.php?mod='. $_GET['mod'] .'&action='. $_GET['action'] .'&step='. $_GET['step'] .'&signon='. $_GET['signon'], 'user', 'userid', $_GET['userid']);
}

?>