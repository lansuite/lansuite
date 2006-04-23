<?php

	$user = $db->query_first("SELECT * FROM {$config["tables"]["party_user"]} WHERE user_id = '{$auth['userid']}' AND party_id = '{$party->party_id}'");

	$currenttime = time();
	if ($user["user_id"]) {
		$func->information($lang['signon']['allready'], "index.php?mod=news");

	} elseif($_SESSION['party_info']['s_startdate'] >= $currenttime && $_GET['signon'] != 0) {
		$func->information(HTML_NEWLINE . "{$lang['signon']['signon_start']}:" . HTML_NEWLINE . HTML_NEWLINE . "<strong>". $func->unixstamp2date($_SESSION['party_info']['s_startdate'], "daydatetime"). "</strong>", "");
		if($_SESSION["auth"]["type"] == 0){
			$dsp->NewContent("");
			$dsp->AddDoubleRow("", "<a href=\"index.php?mod=signon&action=add&step=2&signon=0\">". $lang["signon"]["add_not_registered_nosignup"] ."</a>");
			$dsp->AddContent();
		}
	} elseif($_SESSION['party_info']['s_enddate'] <= $currenttime && $_GET['signon'] != 0) {
		$func->information( HTML_NEWLINE . "{$lang['signon']['signon_closed']}:" . HTML_NEWLINE . HTML_NEWLINE . "<strong>". $func->unixstamp2date($_SESSION['party_info']['s_enddate'],"daydatetime"). "</strong>", "");
		if($_SESSION["auth"]["type"] == 0){
			$dsp->NewContent("");
			$dsp->AddDoubleRow("", "<a href=\"index.php?mod=signon&action=add&step=2&signon=0\">". $lang["signon"]["add_not_registered_nosignup"] ."</a>");
			$dsp->AddContent();
		}
	} else {
    $step 		= $_GET['step'];
    
    $_POST["username"] = trim($_POST["username"]);
    
    include "modules/signon/class_signon.php";
    $signon = new signon();
    
    // Get Fieldtypes (Optional / Duty / Invissible)
    $rows = $db->query("SELECT * FROM {$config["tables"]["config"]} WHERE cfg_group = 'Anmeldungsfelder' ORDER BY cfg_value DESC");
    $signup_cfg = array();
    while($row = $db->fetch_array($rows)) {
    	$signup_cfg[substr($row["cfg_key"], 12, strlen($row["cfg_key"]))] = $row["cfg_value"];
    }
    $db->free_result($rows);
    
    
    
    switch ($step) {
    	case 10:
    		// Existing Account
    
    		// Check Error
    		if ($_POST['email'] == "") {
    			$email_error = $lang["signon"]["add_err_no_mail"];
    			$step = 1;
    		} else {
    			$res = $db->query("SELECT email FROM {$config["tables"]["user"]} WHERE email = '{$_POST['email']}'");
    			if ($db->num_rows($res) == 0) {
    				$email_error = $lang["signon"]["add_err_invalid_user"];
    				$step = 1;
    			}
    			$db->free_result($res);
    		}
    
    		if ($_POST['password'] == "") {
    			$password_error = $lang["signon"]["add_err_no_password"];
    			$step = 1;
    		} else {
    			$res = $db->query_first("SELECT password FROM {$config["tables"]["user"]} WHERE email = '{$_POST['email']}'");
    			if ($res["password"] != md5($_POST['password'])) {
    				$password_error = $lang["signon"]["add_err_wrong_password"];
    				$step = 1;
    			}
    		}
    
    
    		// Try PW at Lansurfer, if in online mode and account is not in local DB
    		if ($_POST["try_lansurfer"] and $cfg["sys_internet"] and ($email_error == $lang["signon"]["add_err_invalid_user"] or $password_error == $lang["signon"]["add_err_wrong_password"])) {
    			$lansurfer_data = $signon->GetLansurfer($_POST['email'], $_POST['password']);
    
    			// If data fetched
    			if ($lansurfer_data["f_nick"] != "") {
    				$_POST["username"] = $lansurfer_data["f_nick"];
    				$_POST["firstname"] = $lansurfer_data["f_name1"];
    				$_POST["lastname"] = $lansurfer_data["f_name2"];
    				$_POST["clan_new"] = $lansurfer_data["f_clan"];
    				$_POST["wwcl_id"] = $lansurfer_data["f_wwclid"];
    				$_POST["wwcl_clanid"] = $lansurfer_data["f_wwclclanid"];
    				$plz = $lansurfer_data["f_zipcode"];
    				$city = $lansurfer_data["f_town"];
    				$_POST["clanurl"] = $lansurfer_data["f_homepage"];
    				$_POST["password2"] = $_POST['password'];
    
    				// More available fields:
    #					$lansurfer_data["party"];
    #					$lansurfer_data["uid"];
    #					$lansurfer_data["submited"];
    #					$lansurfer_data["f_lanpw1"];
    #					$lansurfer_data["f_coord"];
    #					$lansurfer_data["f_birthyear"];
    
    				$email_error = "";
    				$password_error = "";
    				$step = 3;
    			}
    		}
    
    		if ($signup_cfg["agb"] == 2 and $_POST['agb'] == "") {
    			$agb_error = $lang["signon"]["add_err_no_agb"];
    			$step = 1;
    		}
    	break;
    }
    
    
    // Error Switch
    switch($step) {
    	case 3:
    		// GetBirthdayTimestamp
    		if (($_POST["birthday_value_year"] == 1970) && ($_POST["birthday_value_month"] == "1") && ($_POST["birthday_value_day"] == "1")) $signon->birthday = 0;
    		else $signon->birthday = $func->date2unixstamp($vars["birthday_value_year"], $vars["birthday_value_month"], $vars["birthday_value_day"], 0, 0, 0);
    
    		// Email exist?
    		$get_email = $db->query_first("SELECT LOWER(email) AS email
    				FROM {$config["tables"]["user"]}
    				WHERE LOWER(email) = '{$_POST["email"]}'
    				");
    
    		// User exist?
    		$get_username = $db->query_first("SELECT LOWER(username) AS username
    				FROM {$config["tables"]["user"]}
    				WHERE LOWER(username) = '{$_POST["username"]}'
    				");
    
    		if ($get_username["username"] != "") {
    			$username_error = $lang["signon"]["add_err_user_exist"];
    			$step = 2;
    		}
    		if (preg_match("/([.^\"\'`�]+)/",$_POST["username"])) {
    			$username_error = $lang["signon"]["add_err_user_chars"];
    			$step = 2;
    		}
    		
	   		if ($get_email["email"] != "") {
    			$email_error = $lang["signon"]["add_err_mail_exist"];
    			$step = 2;
    		}
    		if ($_POST["username"] == "") {
    			$username_error = $lang["signon"]["add_err_no_user"];
    			$step = 2;
    		}
    		if (($signup_cfg["lastname"] == 2) && ($_POST["lastname"] == "")) {
    			$lastname_error = $lang["signon"]["add_err_no_last"];
    			$step = 2;
    		}
    		if($_POST['lastname'] != "" && preg_match("/([.^\"\'`�]+)/",$_POST["lastname"])){
    			$lastname_error = $lang["signon"]["add_err_user_chars"];
    			$step = 2;
    		}
    		if (($signup_cfg["firstname"] == 2) && ($_POST["firstname"] == "")) {
    			$firstname_error = $lang["signon"]["add_err_no_first"];
    			$step = 2;
    		}
    		if($_POST['firstname'] != "" && preg_match("/([.^\"\'`�]+)/",$_POST["firstname"])){
    			$firstname_error = $lang["signon"]["add_err_user_chars"];
    			$step = 2;
    		}
    		if (!preg_match("/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/", $_POST["email"])) {
    			$email_error = $lang["signon"]["add_err_invalid_mail"];
    			$step = 2;
    		}
    
    		// PW Check
    		if (!$cfg["signon_autopw"]){
    			if ($_POST["password"] == "") {
    				$password_error = $lang["signon"]["add_err_nopass"];
    				$step = 2;
    			}
    			if ($_POST["password"] != $_POST["password2"]) {
    				$password2_error = $lang["signon"]["add_err_wrong2ndpass"];
    				$step = 2;
    			}
    		}
    
    		// Address Check
    		if (($_POST['addr1'] != "") || ($signup_cfg["street"] == 2)){
    			$res = $signon->SplitStreet($_POST['addr1']);
    			$nr = $res["nr"];
    			$street = $res["street"];
    
    			if (($street == "") || ($nr == "")) {
    				$street_error = $lang["signon"]["add_err_invalid_street"];
    				$step = 2;
    			} elseif ((int)$nr == 0){
    				$street_error = $lang["signon"]["add_err_invalid_nr"];
    				$step = 2;
    			}
    
    		}
    		if (($_POST['addr2'] != "") || ($signup_cfg["city"] == 2)) {
    			$res = $signon->SplitCity($_POST['addr2']);
    			$plz = $res["plz"];
    			$city = $res["city"];
    
    			if (($plz == "") || ($city == "")) {
    				$city_error = $lang["signon"]["add_err_invalid_city"];
    				$step = 2;
    			} elseif ((strlen($plz) < 4) || ((int)$plz == 0)){
    				$city_error = $lang["signon"]["add_err_invalid_plz"];
    				$step = 2;
    			}
    		}
    		if (($signup_cfg["agb"] == 2) && ($_POST['agb'] == "") && (($_GET["signon"]) || ($cfg["signon_alwaysagb"]))) {
    			$agb_error = $lang["signon"]["add_err_no_agb"];
    			$step = 2;
    		}
    		if (($signup_cfg["voll"] == 2) && ($_POST['voll'] == "")) {
    			$voll_error = $lang["signon"]["add_err_no_voll"];
    			$step = 2;
    		}
    
    		// CheckPerso
    		$perso = $_POST["perso_1"] . "<<" . $_POST["perso_2"] . "<". $_POST["perso_3"] . "<<<<<<<" . $_POST["perso_4"];
    		if ($perso == "aaaaaaaaaaD<<bbbbbbb<ccccccc<<<<<<<d") $perso = "";
    		if ($perso == "<<<<<<<<<<") $perso = "";
    		if (($perso != "") || ($signup_cfg["perso"] == 2)){
    			$perso_res = $signon->CheckPerso($perso);
    			switch ($perso_res){
    				case 2: $perso_error = str_replace("<", "&lt;", $lang["signon"]["add_err_perso_format"]); break;
    				case 3: $perso_error = $lang["signon"]["add_err_perso_cs"]; break;
    				case 4: $perso_error = $lang["signon"]["add_err_perso_expired"]; break;
    			}
    			if ($perso_res > 1) $step = 2;
    		}

    		// URL given, but no clan selected / entered
    		if ($_POST['clanurl'] == 'http://') $_POST['clanurl'] = '';
    		if(($_POST['clanurl'] != '' and $_POST['clan'] == '' and $_POST['clan_new'] == '')){
    			$error["clanurl"] = $lang["usrmgr"]["add_err_clanurl_no_clan"];
    		}
  
        // Get Clanurl, if clan selected  		
    		if ($_POST['clanurl'] == '' and $_POST['clan'] != '') {
    			$clandata = $db->query_first("SELECT url
    				FROM {$config['tables']['clan']}
    				WHERE clanid = '{$_POST['clan']}'
    				");
    			$_POST['clanurl'] = $clandata['url'];
    		}
  
        // Check clanpass, when join
  			$clanpass = $db->query_first("SELECT password
  				FROM {$config["tables"]["clan"]}
  				WHERE clanid = '{$_POST['clan']}'
  				");
  			if ($clanpass['password'] != md5('') and $_POST['clan'] and md5($_POST['clanpw']) != $clanpass['password']) {
  				$error['clan_pass'] = $lang["signon"]["add_err_no_clanpass"];
  			}
  			
  			// Check clanpass, when create
  			if ($_POST['newclanpw'] != $_POST['newclanpw2']) $error['newclanpw'] = $lang['usrmgr']['clanpw_diffpw'];			
    
/*    
    		// Get Clandata
    		// Check Clandata chars
    		if($_POST['new_clan'] != "" && preg_match("/([.^\"\'`�]+)/",$_POST["new_clan"])){
    			$clan_error = $lang["signon"]["add_err_user_chars"];
    			$step = 2;
    		}
    		if ($_POST["clan"] == "") {
    			$_POST["clan"] = $_POST["clan_new"];
    		} else {
    			$clandata = $db->query_first("SELECT clanurl,clanpass
    				FROM {$config["tables"]["user"]}
    				WHERE (clan = '{$_POST["clan"]}') AND (clanurl != '') AND (clanurl != 'http://')
    				GROUP BY clan
    				");
    			$_POST["clanurl"] = $clandata["clanurl"];
    			
    			$clanpass = $db->query_first("SELECT clanpass
    				FROM {$config["tables"]["user"]}
    				WHERE (clan = '{$_POST["clan"]}')
    				GROUP BY clan
    				");
    			if(md5($_POST["clanpass"]) != $clanpass["clanpass"]){
    				$clan_err_pass = $lang["signon"]["add_err_no_clanpass"];
    				$step = 2;
    			}
    		}
    		if ($_POST["clanurl"] == "http://") $_POST["clanurl"] = "";
    
    		if($_POST["clanurl"] != "" && $_POST['clan'] == ""){
    			$clanurl_error = $lang["usrmgr"]["add_err_clanurl_no_clan"];
    			$step = 2;
    		}
    		if (($signup_cfg["clan"] == 2) && ($_POST["clan"] == "")) {
    			$clan_error = $lang["signon"]["add_err_no_clan"];
    			$step = 2;
    		}
    		if (($signup_cfg["clanurl"] == 2) && ($_POST["clan"] == "") && ($_POST["clanurl"] == "")) {
    			$clanurl_error = $lang["signon"]["add_err_no_clanurl"];
    			$step = 2;
    		}
*/
    		if (($signup_cfg["wwcl_id"] == 2) && ($_POST["wwcl_id"] == "")) {
    			$wwclid_error = $lang["signon"]["add_err_no_wwclid"];
    			$step = 2;
    		}
    		if (($signup_cfg["ngl_id"] == 2) && ($_POST["ngl_id"] == "")) {
    			$nglid_error = $lang["signon"]["add_err_no_nglid"];
    			$step = 2;
    		}
    		if (($signup_cfg["birthday"] == 2) && ($signon->birthday == 0)) {
    			$birthday_error = $lang["signon"]["add_err_no_birthday"];
    			$step = 2;
    		}
    		if (($signup_cfg["gender"] == 2) && ($_POST["gender"] == 0)) {
    			$gender_error = $lang["signon"]["add_err_no_gender"];
    			$step = 2;
    		}
    		
    		if ($error) foreach ($error as $e_key => $e_val) if ($error[$e_key] != "") {
    			$step = 2;
    		}    		

    		// Check for Usergroups
    		$_POST['group_id'] = 0;
    		$groups = $db->query("SELECT * FROM {$config['tables']['party_usergroups']} WHERE selection != 0 ORDER BY pos DESC");
    		if($db->num_rows($groups) > 0){
    			while ($group = $db->fetch_array($groups)){
    				switch ($group['selection']){
    					// check adult
    					case 1: 
    						if($signon->birthday != 0){
    							unset($array);
    							if(preg_match("/^[0-9]+-[0-9]+$/i",trim($group['select_opts']))){
    								$array = split("-",trim($group['select_opts']));
    								if($signon->birthday > strtotime("-{$array[1]} years") && $signon->birthday < strtotime("-{$array[0]} years")){
    									$_POST['group_id'] = $group['group_id'];	
    								}
    							}elseif (preg_match("/^-[0-9]+$/i",trim($group['select_opts']))){
    								$array = substr(trim($group['select_opts']),1);
    								if($signon->birthday > strtotime("-{$array} years") ){
    									$_POST['group_id'] = $group['group_id'];	
    								}
    							}elseif (preg_match("/^[0-9]+\+$/i",trim($group['select_opts']))){
    								$array = substr(trim($group['select_opts']),0,strlen($group['select_opts'])-1);
    								if($signon->birthday < strtotime("-{$array} years")){
    									$_POST['group_id'] = $group['group_id'];	
    								}
    							}
    						}
    					break;
    					// check sex women
    					case 2:
    						if($_POST['gender'] == 2){
    							$_POST['group_id'] = $group['group_id'];	
    						}
    					break;
    					// check sex men
    					case 3:
    						if($_POST['gender'] == 1){
    							$_POST['group_id'] = $group['group_id'];	
    						}					
    					break;
    					// check city	
    					case 4:
    						if(strtolower($city) == strtolower($group['select_opts'])){
    							$_POST['group_id'] = $group['group_id'];
    						}
    					break;
    						
    				}
    			
    			}
    		}
    		
    	break;
    }
    
    
    
    function WriteForm($optional){
    	global $dsp, $lang, $signon, $db, $signup_cfg, $config, $username_error, $firstname_error, $lastname_error, $email_error, $agb_error, $voll_error, $street_error, $city_error, $perso_error, $clan_error, $clanurl_error, $wwclid_error, $nglid_error, $birthday_error, $gender_error, $cfg, $templ, $password_error, $password2_error, $clan_err_pass, $error;
    
    	($optional)? $needed = 1 : $needed = 2;
    
    	if ($optional == 0) $dsp->AddTextFieldRow("username", $lang["signon"]["add_username"], $_POST["username"], $username_error);
    	if ($signup_cfg["firstname"] == $needed) $dsp->AddTextFieldRow("firstname", $lang["signon"]["add_firstname"], $_POST["firstname"], $firstname_error, "", $optional);
    	if ($signup_cfg["lastname"] == $needed) $dsp->AddTextFieldRow("lastname", $lang["signon"]["add_lastname"], $_POST["lastname"], $lastname_error, "", $optional);
    	if ($optional == 0) $dsp->AddTextFieldRow("email", $lang["signon"]["add_email"], $_POST["email"], $email_error);
    
    	if ((!$cfg["signon_autopw"]) && (!$optional)){
    		$dsp->AddPasswordRow("password", $lang["signon"]["add_password"], $_POST["password"], $password_error, "", "", "onKeyUp=\"checkInput(this);\"");
    		$dsp->AddPasswordRow("password2", $lang["signon"]["add_password2"], $_POST["password2"], $password2_error);
    		$dsp->AddDoubleRow($lang["signon"]["add_password_security"], str_replace("{default_design}", $_SESSION["auth"]["design"], $dsp->FetchModTPL("signon", "row_pw_security")));
    	}
    
    	($cfg["signon_agb_targetblank"]) ? $target = "target=\"_blank\"" : $target = "";
    	if (($signup_cfg["agb"] == $needed) && (($_GET["signon"]) || ($cfg["signon_alwaysagb"]))) $dsp->AddCheckBoxRow("agb", $lang["signon"]["add_agb"], str_replace("%LINK%", "<a href=\"". $cfg["signon_agblink"] ."\"$target>AGB</a>", str_replace("%NAME%", $_SESSION['party_info']['name'], $lang["signon"]["add_agb_detail"])), $agb_error, $optional, $_POST["agb"]);
    
    	if ($signup_cfg["voll"] == $needed) $dsp->AddCheckBoxRow("voll", $lang["signon"]["add_vollmacht"], str_replace("%LINK%", "<a href=\"". $cfg["signon_volllink"] ."\" target=\"new\">U18 Vollmacht</a>", str_replace("%NAME%", $_SESSION['party_info']['name'], $lang["signon"]["add_vollmacht_detail"])), $voll_error, $optional, $_POST["voll"]);


    	if ($signup_cfg["clan"] == $needed) {
    		// Clan select
    		$clans_query = $db->query("SELECT c.clanid, c.name, c.url, COUNT(u.clanid) AS members
    				FROM {$config["tables"]["clan"]} AS c
    				LEFT JOIN {$config["tables"]["user"]} AS u ON c.clanid = u.clanid
    				WHERE u.clanid IS NULL or u.type >= 1
    				GROUP BY c.clanid
    				ORDER BY c.name
    				");
    		$t_array = array();
    		($_POST["clan"] == '') ? $selected = "selected" : $selected = "";
    		array_push ($t_array, "<option $selected value=\"\">---</option>");
    		while($row = $db->fetch_array($clans_query)) {
    			if ($_POST['clan'] == $row['clanid'] and $_POST['clan'] != ""){
    				$selected = "selected";
    				if ($_POST["clanurl"] == "") $_POST["clanurl"] = $row["url"];
    			} else $selected = "";
    			array_push ($t_array, "<option $selected value=\"{$row['clanid']}\">{$row['name']} ({$row['members']})</option>");
    		}
    		$dsp->AddDropDownFieldRow("clan", $lang["signon"]["add_existing_clan"], $t_array, $error["clan"], $optional);
        $dsp->AddPasswordRow('clanpw', $lang["signon"]["add_create_clanpass"], '', $error['clan_pass'], '', OPTIONAL);
  			$dsp->AddCheckBoxRow('new_clan_select" onChange="change_check_box_state(\'new_clan_fields\', this.checked)', $lang["signon"]["add_create_clan"], '', '', OPTIONAL, $_POST['new_clan_select']);
  			$dsp->StartHiddenBox('new_clan_fields', $_POST['new_clan_select']);
    		$dsp->AddTextFieldRow("clan_new", $lang["signon"]["add_create_clan"], $_POST["clan_new"], $error["clan_new"], "", $optional);  
    		if ($signup_cfg["clanurl"] == $needed) $dsp->AddTextFieldRow("clanurl", $lang["signon"]["add_clanurl"], $_POST["clanurl"], $error["clanurl"], "", $optional);
        $dsp->AddPasswordRow('newclanpw', $lang["signon"]["add_create_clanpass"], '', $error['newclanpw'], '', OPTIONAL, ' onKeyUp="checkInput(this);"');
        $dsp->AddPasswordRow('newclanpw2', $lang["signon"]["add_create_clanpass"], '', '', '', OPTIONAL);
    		$dsp->AddDoubleRow($lang["usrmgr"]["chpwd_password_security"], $dsp->FetchModTPL('signon', 'row_pw_security'));
  			$dsp->StopHiddenBox();
    		$dsp->AddHRuleRow();
      }

/*    
    	// Clan select
    	if ($signup_cfg["clan"] == $needed) {
    		$clans_query = $db->query("SELECT clan, COUNT(*) AS members
    				FROM {$config["tables"]["user"]}
    				WHERE (type >= 1) AND (clan != '') AND (clan != '---')
    				GROUP BY clan
    				ORDER BY clan
    				");
    		$t_array = array();
    		($_POST["clan"] == "") ? $selected = "selected" : $selected = "";
    		array_push ($t_array, "<option $selected value=\"\">---</option>");
    		while($row = $db->fetch_array($clans_query)) {
    			($_POST["clan"] == $row["clan"]) ? $selected = "selected" : $selected = "";
    			array_push ($t_array, "<option $selected value=\"{$row["clan"]}\">{$row["clan"]} ({$row["members"]})</option>");
    		}
    		$dsp->AddDropDownFieldRow("clan", $lang["signon"]["add_existing_clan"], $t_array, "", $optional);
    
    		$dsp->AddTextFieldRow("clan_new", $lang["signon"]["add_create_clan"], $_POST["clan_new"], $clan_error, "", $optional);
    		$dsp->AddTextFieldRow("clanpass",$lang["signon"]["add_create_clanpass"], $_POST["clanpass"], $clan_err_pass,"",$optional);
    	}
    	if ($signup_cfg["clanurl"] == $needed) $dsp->AddTextFieldRow("clanurl", $lang["signon"]["add_clanurl"], $_POST["clanurl"], $clanurl_error, "", $optional);
*/    	
    	if ($signup_cfg["wwcl_id"] == $needed) $dsp->AddTextFieldRow("wwcl_id", $lang["signon"]["add_wwcl_id"], $_POST["wwcl_id"], $wwclid_error, "", $optional);
    	if ($signup_cfg["ngl_id"] == $needed) $dsp->AddTextFieldRow("ngl_id", $lang["signon"]["add_ngl_id"], $_POST["ngl_id"], $nglid_error, "", $optional);
    	if ($signup_cfg["street"] == $needed) $dsp->AddTextFieldRow("addr1", $lang["signon"]["add_street"], $_POST['addr1'], $street_error, "", $optional);
    	if ($signup_cfg["city"] == $needed) $dsp->AddTextFieldRow("addr2", $lang["signon"]["add_city"], $_POST['addr2'], $city_error, "", $optional);
    
    	if ($signup_cfg["perso"] == $needed){
    		if ($_POST["perso_1"] == "") $_POST["perso_1"] = "aaaaaaaaaaD";
    		if ($_POST["perso_2"] == "") $_POST["perso_2"] = "bbbbbbb";
    		if ($_POST["perso_3"] == "") $_POST["perso_3"] = "ccccccc";
    		if ($_POST["perso_4"] == "") $_POST["perso_4"] = "d";
    
    		$templ['ls']['row']['textfield']['key']	= $lang["signon"]["add_perso"];
    		$templ['ls']['row']['textfield']['name']	= "perso";
    		$templ['ls']['row']['textfield']['value1']	= $_POST["perso_1"];
    		$templ['ls']['row']['textfield']['value2']	= $_POST["perso_2"];
    		$templ['ls']['row']['textfield']['value3']	= $_POST["perso_3"];
    		$templ['ls']['row']['textfield']['value4']	= $_POST["perso_4"];
    		$templ['ls']['row']['textfield']['errortext']	= $dsp->errortext_prefix . $perso_error . $dsp->errortext_suffix;
    		if ($optional) $templ['ls']['row']['textfield']['optional'] = "_optional";
    
    		$dsp->AddModTpl("signon", "row_perso");
    	}
    
    	if ($signon->birthday == 0) $signon->birthday = 1;
    
    	if ($signup_cfg["birthday"] == $needed) $dsp->AddDateTimeRow("birthday", $lang["signon"]["add_birthday"], $signon->birthday, $birthday_error, "", "", (1970 - date("Y")), -5, 1);
    
    	if ($signup_cfg["gender"] == $needed) {
    		// Gender select
    		$gender_array = array($lang["signon"]["add_gender_no"],
    			$lang["signon"]["add_gender_m"],
    			$lang["signon"]["add_gender_f"]
    			);
    
    		$t_array = array();
    		if ($_POST["gender"] == "") $_POST["gender"] = 0;
    		while (list ($key, $val) = each ($gender_array)) {
    			($_POST["gender"] == $key) ? $selected = "selected" : $selected = "";
    			array_push ($t_array, "<option $selected value=\"$key\">$val</option>");
    		}
    		$dsp->AddDropDownFieldRow("gender", $lang["signon"]["add_gender"], $t_array, $gender_error, $optional);
    	}
    
    	if ($signup_cfg["newsletter"] == $needed) {
    		if ($_POST["newsletter"] == "") $_POST["newsletter"] = 1;
    		$dsp->AddCheckBoxRow("newsletter", $lang["signon"]["add_newsletter"], $lang["signon"]["add_newsletter_detail"], "", $optional, $_POST["newsletter"]);
    	}
    }
    
    function WriteSignon(){
    	global $db,$config,$cfg,$party,$dsp,$auth;
    	if($_SESSION['auth']['group_id'] > 0){
    		$party->get_price_dropdown($_SESSION['auth']['group_id'],$_POST['price_id']);	
    	}else{
    		$party->get_price_dropdown("NULL",$_POST['price_id']);
    	}
    	
    	
    }
    
    
    switch($step) {
    	default:
    		if ($auth["type"] > 0){
    			$dsp->NewContent(str_replace("%NAME%", $_SESSION['party_info']['name'], $lang["signon"]["add_caption2"]), str_replace("%NAME%", $_SESSION['party_info']['name'], $lang["signon"]["add_subcaption2_old"]));
    		} else {
    			$dsp->NewContent(str_replace("%NAME%", $_SESSION['party_info']['name'], $lang["signon"]["add_caption2"]), str_replace("%NAME%", $_SESSION['party_info']['name'], $lang["signon"]["add_subcaption2"]));
    		}
    		
    		$dsp->SetForm("index.php?mod=signon&action=add&step=10");
    		$dsp->AddTextFieldRow("email", $lang["signon"]["add_email"], $_POST['email'], $email_error);
    		$dsp->AddPasswordRow("password", $lang["signon"]["add_password"], $_POST['password'], $password_error);
    		$party->get_price_dropdown("NULL",$_POST['price_id']);
    		($cfg["signon_agb_targetblank"]) ? $target = "target=\"_blank\"" : $target = "";
    		if ($signup_cfg["agb"] == 2) $dsp->AddCheckBoxRow("agb", $lang["signon"]["add_agb"], str_replace("%LINK%", "<a href=\"". $cfg["signon_agblink"] ."\"$target>AGB</a>", str_replace("%NAME%", $_SESSION['party_info']['name'], $lang["signon"]["add_agb_detail"])), $agb_error, $optional, $_POST["agb"]);
    		if (function_exists("socket_create")) $dsp->AddCheckBoxRow("try_lansurfer", $lang["signon"]["add_try_lansurfer"], $lang["signon"]["add_try_lansurfer2"], "", "", $_POST["try_lansurfer"]);
    		if($auth['login'] == 0) $dsp->AddSingleRow("<input type='hidden' name='login_x' value='1'>");
    		
    		$dsp->AddFormSubmitRow("join","signon/add_mini");
    		
    
    		if ($auth["type"] == 0){
#    			if ($cfg['singon_multiparty'] == "0"){
    				$dsp->AddSingleRow("<b>". $lang["signon"]["add_not_registered"] ."</b>");
    				$dsp->AddDoubleRow("", "<input type='hidden' name='login_x' value='1'><a href=\"index.php?mod=signon&action=add&step=2&signon=1\">". $lang["signon"]["add_not_registered_signup"] ."</a>");
    				$dsp->AddDoubleRow("", "<a href=\"index.php?mod=signon&action=add&step=2&signon=0\">". $lang["signon"]["add_not_registered_nosignup"] ."</a>");
/*    			} else {
    				$dsp->AddSingleRow("<b>". $lang["signon"]["add_not_registered"] ."</b>");				
    				$dsp->AddDoubleRow("", "<a href=\"index.php?mod=signon&action=add&step=2&signon=1\">". $lang["signon"]["add_not_registered_signup"] ."</a>");
    				$dsp->AddDoubleRow("", "<a href=\"index.php?mod=signon&action=add&step=2&signon=0\">". $lang["signon"]["add_not_registered_nosignup"] ."</a>");
    			}*/
    		}
    		$dsp->AddContent();
    	break;
    
    	case 2:
    		$_SESSION['add_blocker_signon'] = FALSE;
    
    		$dsp->NewContent(str_replace("%NAME%", $_SESSION['party_info']['name'], $lang["signon"]["add_caption"]), str_replace("%NAME%", $_SESSION['party_info']['name'], $lang["signon"]["add_subcaption"]));
    		$dsp->SetForm("index.php?mod=signon&action=add&step=3&signon={$_GET["signon"]}", "signon");
    
    		if($auth['userid'] > 0){
    			WriteSignon();
    			$dsp->AddSingleRow("<input type='hidden' name='login_x' value='1'>");
    			$helplet = "signon/add_mini";
    		}else{
    			WriteForm(0);
    			if (in_array(1, $signup_cfg)) $dsp->AddSingleRow("<b>". $lang["signon"]["optional"] ."</b>");
    			$helplet = "signon/add";
    			WriteForm(1);
    		}
    		
    		$dsp->AddFormSubmitRow("join",$helplet);
    
    		
    		$dsp->AddBackButton("index.php?mod=signon&action=add"); 
    		$dsp->AddContent();
    	break;
    
    	case 3:
    		if($_SESSION['add_blocker_signon'] == TRUE) $func->error("NO_REFRESH", "");
    		else {
    			$_SESSION['add_blocker_signon'] = TRUE;
    
    			if ($cfg["signon_autopw"]) $_POST["password"] = $signon->GeneratePassword();
    			$md5_password = md5($_POST["password"]);

    			$add_query = $db->query("INSERT INTO {$GLOBALS["config"]["tables"]["user"]} SET
    										username	= '{$_POST["username"]}',
    										password	= '$md5_password',
    										type		= '1',
    										name 		= '{$_POST["lastname"]}',
    										firstname	= '{$_POST["firstname"]}',
    										email		= '{$_POST["email"]}',
    										wwclid		= '{$_POST["wwcl_id"]}',
    										wwclclanid	= '{$_POST["wwcl_clanid"]}',
    										nglid		= '{$_POST["ngl_id"]}',
    										sex			= '{$_POST["gender"]}',
    										street		= '$street',
    										hnr			= '$nr',
    										plz			= '$plz',
    										city		= '$city',
    										birthday	= '$signon->birthday',
    										newsletter	= '{$_POST["newsletter"]}',
    										group_id	= '{$_POST["group_id"]}',
    										perso		= '$perso',
    										locked = ". (int)$cfg['signon_locked']
    										);
    			$userid = $db->insert_id();
    			$add_query2 = $db->query("INSERT INTO {$GLOBALS["config"]["tables"]["usersettings"]} SET userid = $userid");

          // Clan-Management
          include_once("modules/usrmgr/class_clan.php");
          $clan = new Clan();
          if ($_POST['clan_new']) $_POST['clan'] = $clan->Add($_POST['clan_new'], $_POST["clanurl"], $_POST["newclanpw"]);
          if ($_POST['clan']) $clan->AddMember($_POST['clan'], $userid);
          else $clan->RemoveMember($_GET["userid"]);

    			$confirm_text = $lang["signon"]["add_success"];
    			
    			if ($cfg["signon_password_mail"]) {
    				if ($signon->SendSignonMail()) $confirm_text .= HTML_NEWLINE . HTML_NEWLINE . $lang["signon"]["add_pw_mail_success"];
    				else {
    					$confirm_text .= HTML_NEWLINE . HTML_NEWLINE . "Fehler beim EMail versenden. Error-Text: ". $mail->error;		
    					$cfg["signon_password_view"] = 1;
    				}
    			}
    
    			if ($cfg["signon_password_view"]) $confirm_text .= HTML_NEWLINE . HTML_NEWLINE . str_replace("%PASSWORD%", "<b>". $_POST["password"] ."</b>", $lang["signon"]["add_pw_text"]);
    
    			if($_GET['signon'] == 1){
    				$confirm_text .= HTML_NEWLINE . "<strong>" . str_replace("%NAME%", $_SESSION['party_info']['name'], $lang["signon"]["add_party"]) . "</strong>";
    			}
    			
    			$signon->WriteXMLStatFile();
    			if($_GET['signon']){
    				$func->confirmation($confirm_text, 0);
  	    			$dsp->NewContent(str_replace("%NAME%", $_SESSION['party_info']['name'], $lang["signon"]["add_caption"]));
  		  			$dsp->SetForm("index.php?mod=signon&action=add&step=5&userid=$userid");
    				$party->get_price_dropdown($_POST["group_id"]);
    				$dsp->AddFormSubmitRow("next");
    				$dsp->AddBackButton("index.php?mod=news");
    				$dsp->AddContent();
    				
 	   				// $func->question($confirm_text, "index.php?mod=signon&action=add&step=4&userid=$userid&group_id={$_POST["group_id"]}","index.php?mod=news");
    			}else{
    				$func->confirmation($confirm_text, "index.php?mod=news");
    			}
    		}
    	break;
 /*   
    	case 4:
    			$dsp->NewContent(str_replace("%NAME%", $_SESSION['party_info']['name'], $lang["signon"]["add_caption"]));
    			$dsp->SetForm("index.php?mod=signon&action=add&step=5&userid={$_GET["userid"]}");
    			$party->get_price_dropdown($_GET['group_id']);
    			$dsp->AddFormSubmitRow("add");
    			$dsp->AddContent();
    
    	break;
*/    	
    	case 5:
    			$party->add_user_to_party($_GET['userid'],$_POST['price_id']);
    			$func->confirmation(str_replace("%NAME%", $_SESSION['party_info']['name'], $lang["signon"]["add_success"]),"index.php?mod=home");
    	break;
    	case 10:
    		if($auth['login'] == 1){
    			$user = $db->query("SELECT * FROM {$config['tables']['party_user']} WHERE party_id={$party->party_id} AND user_id= {$auth['userid']}");
    			
    			if($db->num_rows($user) == 0){
    					$party->add_user_to_party($auth['userid'],$_POST['price_id']);
    					$func->confirmation(str_replace("%NAME%", $_SESSION['party_info']['name'], $lang["signon"]["add_success"]), "index.php?mod=signon&action=add");
    					$signon->WriteXMLStatFile();
    			}else{
    				$func->error($lang['signon']['is_signon'],"index.php?mod=signon");
    			}
    
    			
    		}else $func->error("NO_LOGIN","index.php?mod=signon");
    	break;
    }
	} // End: If signon activated
?>