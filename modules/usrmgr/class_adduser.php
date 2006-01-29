<?php 

include_once("modules/signon/class_signon.php");
$signon = new signon();

class AddUser {

	function Optional($key){
		global $cfg;

		if ($cfg["signon_show_".$key] <= 1) return 1;
		else return 0;
	}

	function Needed($key){
		global $cfg;

		if ($cfg["signon_show_".$key] == 2) return 1;
		else return 0;
	}


	// Reads the users data from the db, to display current settings while changing
	function GetDBData($action) {
		global $db, $config, $birthday, $party, $auth;
		
		if ($action == "change"){
			$user_data = $db->query_first("SELECT u.*, s.party_id, p.*
        FROM lansuite_user AS u
        LEFT JOIN lansuite_party_user AS p ON u.userid = p.user_id
        LEFT JOIN lansuite_partys AS s ON p.party_id = s.party_id
        WHERE u.userid={$_GET["userid"]} AND (p.party_id={$party->party_id} OR ISNULL(p.party_id))");
			
			$_POST["username"] = $user_data["username"];
			$_POST["firstname"] = $user_data["firstname"];
			$_POST["lastname"] = $user_data["name"];
			$_POST["type"] = $user_data["type"];
			$_POST["group_id"] = $user_data["group_id"];
			$_POST["paid"] = $user_data["paid"];
			$_POST["price_id"] = $user_data["price_id"];
			$_POST["seatcontrol"] = $user_data["seatcontrol"];
			if($user_data['signondate'] != ""){
				$_POST["signon"] = 1;
			}else{
				$_POST["signon"] = 0;
			}
			$_POST["email"] = $user_data["email"];
			$_POST["clan"] = $user_data["clan"];
			$_POST["clanurl"] = $user_data["clanurl"];
			$_POST["wwcl_id"] = $user_data["wwclid"];
			$_POST["ngl_id"] = $user_data["nglid"];
			if (($user_data["street"]) && ($user_data["hnr"])) $_POST["addr1"] = $user_data["street"] ." ". $user_data["hnr"];
			if (($user_data["plz"]) && ($user_data["city"])) $_POST["addr2"] = $user_data["plz"] ." ". $user_data["city"];
			$_POST["perso_1"] = substr($user_data["perso"], 0, 11);
			$_POST["perso_2"] = substr($user_data["perso"], 13, 7);
			$_POST["perso_3"] = substr($user_data["perso"], 21, 7);
			$_POST["perso_4"] = substr($user_data["perso"], 35, 1);
			$birthday = $user_data["birthday"];
			$_POST["gender"] = $user_data["sex"];
			$_POST["newsletter"] = $user_data["newsletter"];
			$_POST["comment"] = $user_data["comment"];
		}
	}


	// Checks the Post-Data for errors
	function CheckErrors($action, $quick_signon = 0) {
		global $db, $config, $lang, $cfg, $error, $signon, $func, $birthday, $street, $nr, $plz, $city, $auth, $missing_fields, $perso;

		$_POST["username"] = trim($_POST["username"]);
		$_POST["firstname"] = trim($_POST["firstname"]);
		$_POST["lastname"] = trim($_POST["lastname"]);

		$error = Array();

 		// Username check
    if (!$quick_signon and ($auth["type"] >= 2 or $missing_fields)) {
  		if ($_POST["username"] == '') $error["username"] = $lang["usrmgr"]["add_err_no_user"];
  		
  		$get_username = $db->query_first("SELECT LOWER(username) AS username
  				FROM {$config["tables"]["user"]}
  				WHERE LOWER(username) = '{$_POST["username"]}' AND userid != '{$_GET["userid"]}'
  				");
  		if ($get_username["username"] != "") $error["username"] = $lang["usrmgr"]["add_err_user_exist"];

  		if (preg_match("/([.^\"\'`´]+)/", $_POST["username"])) $error["username"] = $lang["usrmgr"]["add_err_user_chars"];
  	}
  	
		// Email exist?
		$get_email = $db->query_first("SELECT LOWER(email) AS email
				FROM {$config["tables"]["user"]}
				WHERE LOWER(email) = '{$_POST["email"]}' AND userid != '{$_GET["userid"]}'
				");
		if ($get_email["email"] != "") $error["email"] = $lang["usrmgr"]["add_err_mail_exist"];

		if (!preg_match("/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/", $_POST["email"])) $error["email"] = $lang["usrmgr"]["add_err_invalid_mail"];

    if (!$quick_signon) {
  		if (($this->Needed("lastname")) && ($_POST["lastname"] == "")) $error["lastname"] = $lang["usrmgr"]["add_err_no_last"];
  
  		if (($this->Needed("firstname")) && ($_POST["firstname"] == "")) $error["firstname"] = $lang["usrmgr"]["add_err_no_first"];
    }
    
		// PW Check
		if ((!$cfg["signon_autopw"]) && ($action != "change")){
			if ($_POST["password"] == "") $error["password"] = $lang["usrmgr"]["add_err_nopass"];
			if ($_POST["password"] != $_POST["password2"]) $error["password2"] = $lang["usrmgr"]["add_err_wrong2ndpass"];
		}

    if (!$quick_signon) {
  		// Address Check
  		if (($_POST['addr1'] != "") || ($this->Needed("street"))){
  			$res = $signon->SplitStreet($_POST['addr1']);
  			$nr = $res["nr"];
  			$street = $res["street"];
  
  			if (($street == "") || ($nr == "")) $error["street"] = $lang["usrmgr"]["add_err_invalid_street"];
  			elseif ((int)$nr == 0) $error["street"] = $lang["usrmgr"]["add_err_invalid_nr"];
  		}
  
  		if (($_POST['addr2'] != "") || ($this->Needed("city"))) {
  			$res = $signon->SplitCity($_POST['addr2']);
  			$plz = $res["plz"];
  			$city = $res["city"];
  
  			if (($plz == "") || ($city == "")) $error["city"] = $lang["usrmgr"]["add_err_invalid_city"];
  			elseif ((strlen($plz) < 4) || ((int)$plz == 0)) $error["city"] = $lang["usrmgr"]["add_err_invalid_plz"];
  		}
  
  		// CheckPerso
  		$perso = $_POST["perso_1"] . "<<" . $_POST["perso_2"] . "<". $_POST["perso_3"] . "<<<<<<<" . $_POST["perso_4"];
  		if ($perso == "aaaaaaaaaaD<<bbbbbbb<ccccccc<<<<<<<d") $perso = "";
  		if ($perso == "<<<<<<<<<<") $perso = "";
  		if (($auth["type"] >= 2 or $missing_fields) and (($perso != '') or ($this->Needed("perso")))){
  			$perso_res = $signon->CheckPerso($perso);
  			switch ($perso_res){
  				case 2: $error["perso"] = str_replace("<", "&lt;", $lang["usrmgr"]["add_err_perso_format"]); break;
  				case 3: $error["perso"] = $lang["usrmgr"]["add_err_perso_cs"]; break;
  				case 4: $error["perso"] = $lang["usrmgr"]["add_err_perso_expired"]; break;
  			}
  		}
  
  		// Get Clandata
  		if ($_POST["clan_new"] != "") $_POST["clan"] = $_POST["clan_new"];
  
  		if ($_POST["clanurl"] == "http://") $_POST["clanurl"] = "";
  		if ($_POST["clanurl"] == "") {
  			$clandata = $db->query_first("SELECT clanurl
  				FROM {$config["tables"]["user"]}
  				WHERE (clan = '{$_POST["clan"]}') AND (clanurl != '') AND (clanurl != 'http://')
  				GROUP BY clan
  				");
  			$_POST["clanurl"] = $clandata["clanurl"];
  		}
  
  		if (($this->Needed("clan")) && ($_POST["clan"] == "")) $error["clan"] = $lang["usrmgr"]["add_err_no_clan"];
  		if (($this->Needed("clanurl")) && ($_POST["clanurl"] == "")) $error["clanurl"] = $lang["usrmgr"]["add_err_no_clanurl"];
  		if (($this->Needed("wwcl_id")) && ($_POST["wwcl_id"] == "")) $error["wwcl_id"] = $lang["usrmgr"]["add_err_no_wwclid"];
  		if (($this->Needed("ngl_id")) && ($_POST["ngl_id"] == "")) $error["ngl_id"] = $lang["usrmgr"]["add_err_no_nglid"];
  
  		// GetBirthdayTimestamp
  		if (($_POST["birthday_value_year"] == (date("Y") - 34)) && ($_POST["birthday_value_month"] == "1") && ($_POST["birthday_value_day"] == "1")) $birthday = 0;
  		else $birthday = $func->date2unixstamp($_POST["birthday_value_year"], $_POST["birthday_value_month"], $_POST["birthday_value_day"], 0, 0, 0);
  		if (($auth["type"] >= 2 or $missing_fields) and $this->Needed("birthday") and $birthday == 0) $error["birthday"] = $lang["usrmgr"]["add_err_no_birthday"];
  
  		if (($this->Needed("gender")) && ($_POST["gender"] == 0)) $error["gender"] = $lang["usrmgr"]["add_err_no_gender"];
  
  		if ($_POST["type"] > $auth["type"]) $error["type"] = $lang["usrmgr"]["add_err_less_rights"];
    }
    
		foreach ($error as $e_key => $e_val) if ($error[$e_key] != "") {
			$_GET['step']--;
			return;
		}
	}


	// Displays the form to add (or change) a user
	function ShowForm($action, $quick_signon = 0) {
		global $db, $config, $lang, $cfg, $dsp, $error, $templ, $birthday, $party, $auth, $missing_fields;

		$_SESSION['add_blocker_usrmgr'] = FALSE;

		$templ['ls']['row']['hidden_row']['options'] = $party->get_party_javascript();
		$dsp->AddModTpl("usrmgr","javascript");

    if (!$quick_signon) {
  		if (($auth["type"] >= 2 or $missing_fields)) {
  			$dsp->AddTextFieldRow("username", $lang["usrmgr"]["add_username"], $_POST["username"], $error["username"], "", 0);
  		} else $dsp->AddSingleRow($lang["usrmgr"]["add_limitedright_hint"]);

  		$dsp->AddTextFieldRow("firstname", $lang["usrmgr"]["add_firstname"], $_POST["firstname"], $error["firstname"], "", $this->Optional("firstname"));
  		$dsp->AddTextFieldRow("lastname", $lang["usrmgr"]["add_lastname"], $_POST["lastname"], $error["lastname"], "", $this->Optional("lastname"));
  		$dsp->AddHRuleRow();
  
  
  		if ($auth["type"] == 2) {
  			$type_array = array("1" => $lang["usrmgr"]["add_type_user"],
  				"2" => $lang["usrmgr"]["add_type_admin"]
  			);
  		}
  
  		if ($auth["type"] >= 3) {
  			$type_array = array("1" => $lang["usrmgr"]["add_type_user"],
  				"2" => $lang["usrmgr"]["add_type_admin"],
  				"3" => $lang["usrmgr"]["add_type_operator"]
  			);
  		}
  
  		if ($auth["type"] >= 2) {
  			$t_array = array();
  			if ($_POST["type"] == "") $_POST["type"] = 1;
  			while (list ($key, $val) = each ($type_array)) {
  				($_POST["type"] == $key) ? $selected = "selected" : $selected = "";
  				array_push ($t_array, "<option $selected value=\"$key\">$val</option>");
  			}
  			// Check Operator
  			$count = $db->query_first("SELECT COUNT(*) AS n FROM {$config["tables"]["user"]} WHERE type = 3");
  			if($count['n'] != 1 OR $action == "add" OR $_POST["type"] != 3){
  				$dsp->AddDropDownFieldRow("type\" onChange=\"change_type(this.options[this.options.selectedIndex].value)", $lang["usrmgr"]["add_type"], $t_array, $error["type"], 0);
  			}else{
  				$dsp->AddDoubleRow($lang["usrmgr"]["add_type"],$lang["usrmgr"]["add_type_operator"] . "<input type='hidden' name='type' value='3'>");
  			}

  			$t_array = array();
  			$module_list = $db->query("SELECT module.name, module.caption FROM {$config["tables"]["modules"]} AS module
  				LEFT JOIN {$config["tables"]["menu"]} AS menu ON menu.module = module.name				
  				WHERE menu.file != ''
  				GROUP BY menu.module");
  			while($row = $db->fetch_array($module_list)) {
  				$user_perm = $db->query_first("SELECT 1 AS found FROM {$config["tables"]["user_permissions"]} WHERE (module = '{$row["name"]}') AND (userid = '{$_GET["userid"]}')");
  				($user_perm["found"]) ? $selected = "selected" : $selected = "";
  				array_push ($t_array, "<option $selected value=\"{$row["name"]}\">{$row["caption"]}</option>");
  			}
  			
  			$templ['ls']['row']['hidden_row']['id'] = "type_1";
  			($_POST["type"] > 1) ? $templ['ls']['row']['hidden_row']['display'] = "" : $templ['ls']['row']['hidden_row']['display'] = "none";
  			
  			$dsp->AddModTpl("usrmgr","hiddenbox_start");
  			$dsp->AddSelectFieldRow("permissions", $lang['usrmgr']['add_permission'], $t_array, $error["permissions"], 0, 7);
  			$dsp->AddModTpl("usrmgr","hiddenbox_stop");

      }
 		}
      
  	$dsp->AddTextFieldRow("email", $lang["usrmgr"]["add_email"], $_POST["email"], $error["email"], 0);
    
		if ((!$cfg["signon_autopw"]) && ($action != "change")){
			$dsp->AddPasswordRow("password", $lang["usrmgr"]["add_password"], $_POST["password"], $error["password"], 0, "", "onKeyUp=\"checkInput()\"");
			$dsp->AddPasswordRow("password2", $lang["usrmgr"]["add_password2"], $_POST["password2"], $error["password2"], 0);
			$dsp->AddDoubleRow($lang["usrmgr"]["add_password_security"], str_replace("{default_design}", $_SESSION["auth"]["design"], $dsp->FetchModTPL("signon", "row_pw_security")));
		}

 		if ($auth["type"] >= 2) {
			$paid_array = array("0" => $lang["usrmgr"]["add_paid_no"],
				"1" => $lang["usrmgr"]["add_paid_vvk"],
				"2" => $lang["usrmgr"]["add_paid_ak"]
			);
			$t_array = array();
			if ($_POST["paid"] == "") $_POST["paid"] = 0;
			while (list ($key, $val) = each ($paid_array)) {
				($_POST["paid"] == $key) ? $selected = " selected" : $selected = "";
				array_push ($t_array, "<option value=\"$key\"$selected>$val</option>");
			}

			$dsp->AddCheckBoxRow("signon\" onClick=\"change_party(this.checked)", $lang["usrmgr"]["add_signon"], $lang["usrmgr"]["add_signon_detail"] . " <input type='hidden' name='seatcontrol' value='{$_POST['seatcontrol']}'>", "", 0, $_POST["signon"]);
			
			$templ['ls']['row']['hidden_row']['id'] = "party_1";
			($_POST["signon"] == 1) ? $templ['ls']['row']['hidden_row']['display'] = "" : $templ['ls']['row']['hidden_row']['display'] = "none";
			$dsp->AddModTpl("usrmgr","hiddenbox_start");
			
			$party->get_price_dropdown($_POST["group_id"], $_POST["price_id"], true);
			$dsp->AddDropDownFieldRow("paid", $lang["usrmgr"]["add_paid"], $t_array, $error["paid"], 0);
			$dsp->AddModTpl("usrmgr","hiddenbox_stop");

      $party->get_user_group_dropdown('NULL', 1, $_POST['group_id'], true);			

# 		$dsp->AddCheckBoxRow("platzpfand", $lang["usrmgr"]["add_platzpfand"], $lang["usrmgr"]["add_platzpfand_detail"], "", 1, $_POST["platzpfand"]);
			$dsp->AddHRuleRow();
    }
  
    if (!$quick_signon) {
  		$dsp->AddHRuleRow();
  
  		// Clan select
  		$clans_query = $db->query("SELECT clan, clanurl, COUNT(*) AS members
  				FROM {$config["tables"]["user"]}
  				WHERE (type >= 1) AND (clan != '') AND (clan != '---')
  				GROUP BY clan
  				ORDER BY clan
  				");
  		$t_array = array();
  		($_POST["clan"] == "") ? $selected = "selected" : $selected = "";
  		array_push ($t_array, "<option $selected value=\"\">---</option>");
  		while($row = $db->fetch_array($clans_query)) {
  			if ($_POST["clan"] == $row["clan"]){
  				$selected = "selected";
  				if ($_POST["clanurl"] == "") $_POST["clanurl"] = $row["clanurl"];
  			} else $selected = "";
  			array_push ($t_array, "<option $selected value=\"{$row["clan"]}\">{$row["clan"]} ({$row["members"]})</option>");
  		}
  		$dsp->AddDropDownFieldRow("clan", $lang["usrmgr"]["add_existing_clan"], $t_array, $error["clan"], $this->Optional("clan"));
  
  		$dsp->AddTextFieldRow("clan_new", $lang["usrmgr"]["add_create_clan"], $_POST["clan_new"], $error["clan_new"], "", $this->Optional("clan"));
  
  		$dsp->AddTextFieldRow("clanurl", $lang["usrmgr"]["add_clanurl"], $_POST["clanurl"], $error["clanurl"], "", $this->Optional("clan"));
  		$dsp->AddTextFieldRow("wwcl_id", $lang["usrmgr"]["add_wwcl_id"], $_POST["wwcl_id"], $error["wwcl_id"], "", $this->Optional("wwcl_id"));
  		$dsp->AddTextFieldRow("ngl_id", $lang["usrmgr"]["add_ngl_id"], $_POST["ngl_id"], $error["ngl_id"], "", $this->Optional("ngl_id"));
  		$dsp->AddHRuleRow();
  
  		$dsp->AddTextFieldRow("addr1", $lang["usrmgr"]["add_street"], $_POST['addr1'], $error["street"], "", $this->Optional("street"));
  		$dsp->AddTextFieldRow("addr2", $lang["usrmgr"]["add_city"], $_POST['addr2'], $error["city"], "", $this->Optional("city"));
  
  		if (($auth["type"] >= 2 or $missing_fields)) {
  			if ($_POST["perso_1"] == "") $_POST["perso_1"] = "aaaaaaaaaaD";
  			if ($_POST["perso_2"] == "") $_POST["perso_2"] = "bbbbbbb";
  			if ($_POST["perso_3"] == "") $_POST["perso_3"] = "ccccccc";
  			if ($_POST["perso_4"] == "") $_POST["perso_4"] = "d";
  
  			$templ['ls']['row']['textfield']['key']	= $lang["usrmgr"]["add_perso"];
  			$templ['ls']['row']['textfield']['name']	= "perso";
  			$templ['ls']['row']['textfield']['value1']	= $_POST["perso_1"];
  			$templ['ls']['row']['textfield']['value2']	= $_POST["perso_2"];
  			$templ['ls']['row']['textfield']['value3']	= $_POST["perso_3"];
  			$templ['ls']['row']['textfield']['value4']	= $_POST["perso_4"];
  			$templ['ls']['row']['textfield']['errortext']	= $dsp->errortext_prefix . $error["perso"] . $dsp->errortext_suffix;
  			if ($this->Optional("perso")) $templ['ls']['row']['textfield']['optional']	= "_optional";
  
  			$dsp->AddModTpl("signon", "row_perso");
  
  			if ($birthday == 0) $birthday = 1;
  			$dsp->AddDateTimeRow("birthday", $lang["usrmgr"]["add_birthday"], $birthday, $error["birthday"], "", "", (1970 - date("Y")), -5, 1, $this->Optional("birthday"), " onChange=\"WriteAge();\"");
  			$dsp->AddDoubleRow($lang["usrmgr"]["add_u18check"], $dsp->FetchModTPL("usrmgr", "u18check") . " Jahre");
  		}
  
  		$gender_array = array($lang["usrmgr"]["add_gender_no"],
  			$lang["usrmgr"]["add_gender_m"],
  			$lang["usrmgr"]["add_gender_f"]
  		);
  		$t_array = array();
  		if ($_POST["gender"] == "") $_POST["gender"] = 0;
  		while (list ($key, $val) = each ($gender_array)) {
  			($_POST["gender"] == $key) ? $selected = "selected" : $selected = "";
  			array_push ($t_array, "<option $selected value=\"$key\">$val</option>");
  		}
  		$dsp->AddDropDownFieldRow("gender", $lang["usrmgr"]["add_gender"], $t_array, $error["gender"], $this->Optional("gender"));
  
  		$dsp->AddCheckBoxRow("newsletter", $lang["usrmgr"]["add_newsletter"], $lang["usrmgr"]["add_newsletter_detail"], $error["newsletter"], $this->Optional("newsletter"), $_POST["newsletter"]);
  
  		if ($auth["type"] >= 2) {
  			$dsp->AddFileSelectRow("picture", $lang["usrmgr"]["add_picture"], $error["picture"], "", "", 1);
  			if (file_exists("ext_inc/user_pics/pic". $_GET["userid"] . ".jpg")) {
  				$dsp->AddDoubleRow("", "<img src=\"ext_inc/user_pics/pic". $_GET["userid"] . ".jpg\">");
  			}
  
  			$dsp->AddTextAreaPlusRow("comment", $lang["usrmgr"]["add_comment"], $_POST["comment"], $error["comment"], "", "", 1);
  		}
    }
    
		$dsp->AddFormSubmitRow("next");
		$dsp->AddBackButton("index.php?mod=usrmgr", "usrmgr/form"); 
		$dsp->AddContent();
	}


	// Writes the submitted data to the DB
	function WriteToDB($action, $quick_signon = 0) {
		global $db, $config, $lang, $cfg, $signon, $func, $birthday, $street, $nr, $plz, $city, $party, $auth, $missing_fields, $perso;

		if ($_SESSION['add_blocker_usrmgr']) $func->error("NO_REFRESH", "");
		else {
			$_SESSION['add_blocker_usrmgr'] = TRUE;

			// Eingecheckt?
			// $checkin = 0;
			// if ($_POST["type"] >= 2) $checkin = 1;

			// Bezahlt?
			// $paid = 0;
			// if ($_POST["type"] >= 2 && $_POST['paid'] == 0) $_POST['paid'] = 1;

      $db_set_fields = "
      email		= '{$_POST["email"]}',
			group_id	= '{$_POST["group_id"]}',
      ";

      if (!$quick_signon) { 
        $db_set_fields .= "
  				firstname	= '{$_POST["firstname"]}',
  				name 		= '{$_POST["lastname"]}',
  				clan		= '{$_POST["clan"]}',
  				clanurl		= '{$_POST["clanurl"]}',
  				wwclid		= '{$_POST["wwcl_id"]}',
  				nglid		= '{$_POST["ngl_id"]}',
  				street		= '$street',
  				hnr			= '$nr',
  				plz			= '$plz',
  				city		= '$city',
  				sex			= '{$_POST["gender"]}',
  				newsletter	= '{$_POST["newsletter"]}',
          ";

        // Every one may add a user with these fields, only orgas may change it
  			if ($auth["type"] >= 2 or $missing_fields)
  			  $db_set_fields .= "
  				username	= '{$_POST["username"]}',
  				perso		= '$perso',
  				birthday	= $birthday,
  				";

  			if ($auth["type"] >= 2)
  			  $db_set_fields .= "
  				type		= '{$_POST["type"]}',
  				comment		= '{$_POST["comment"]}',
  				";
      } else {
			  $db_set_fields .= "
				type		= '1',
 				";
      }
      
			if ($action == "change"){			
				if ($checkin) $checkin = "checkin = '$checkin',";
				else $checkin = "";

				$db->query("UPDATE {$config["tables"]["user"]} SET
					$db_set_fields
					changedate	= NOW()
					WHERE userid = {$_GET["userid"]}
					");

			} else { // Add
  			// generate / crypt password
				if ($cfg["signon_autopw"]) $_POST["password"] = $signon->GeneratePassword();
				$md5_password = md5($_POST["password"]);

				$db->query("INSERT INTO {$config["tables"]["user"]} SET
  	      $db_set_fields
  				password	= '$md5_password',
  				changedate	= NOW()
  				");
				$_GET["userid"] = $db->insert_id();

				$add_query2 = $db->query("INSERT INTO {$config["tables"]["usersettings"]} SET userid = '{$_GET["userid"]}'");
				
				$signon->WriteXMLStatFile();
			}

			// Update User-Perissions
			$db->query("DELETE FROM {$config["tables"]["user_permissions"]} WHERE userid = {$_GET["userid"]}");
			if ($_POST["permissions"]) foreach ($_POST["permissions"] as $perm) {
				$db->query("INSERT INTO {$config["tables"]["user_permissions"]} SET module = '$perm', userid = {$_GET["userid"]}");
			}

			// Update Party-Signon
			if (isset($_POST['signon']) && $_POST['signon'] == "1") $party->add_user_to_party($_GET["userid"], $_POST['price_id'], $_POST['paid'], $checkin);
			elseif(isset($_POST['signon']) && $auth["type"] > 1){
			 	$party->delete_user_from_party($_GET["userid"]);	
			}

			// Picture Upload
			if ($auth["type"] >= 2) if (file_exists($_FILES['picture']['tmp_name'])) {
				@unlink("ext_inc/user_pics/pic". $_GET["userid"] . ".jpg");
				@copy($_FILES["picture"]["tmp_name"], "ext_inc/user_pics/pic". $_GET["userid"] . ".jpg");
			}
		} // else blocker is TRUE
	}

} // End: Class
?>
