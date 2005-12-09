<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-----------------------------------------------
*
*	(c) 2001-2003 by One-Network.Org
*
*	Lansuite Version:		2.0.3
*	File Version:			2.2
*	Filename: 				class_auth.php
*	Module: 				Framework
*	Main editor: 			raphael@one-network.org
*   Sub editor:				marco@chuchi.tv (Genesis)
*	Last change: 			22.05.2005 18.00
*	Description: 			This file manages the login, logout and
*										permission system in lansuite
*	Remarks:
*
**************************************************************************/

// logtime =	Datum des ersten Eintrags zu dieser SessionID
// logintime =	Datum des letzten Einloggens dieser SessionID
// lasthit =	Datum des letzten Seitenaufrufes dieser SessionID

class auth {

	var $auth = array();
	var $timestamp;
	var $tocheck;
	var $err;

	// Constructor
	function auth($update = true) {
		global $db, $config,$func,$cfg;

		// Init-Vars
		$this->auth["sessid"] = session_id();
		$this->auth["ip"] = $_SERVER['REMOTE_ADDR'];
		$this->timestamp = time();

		// Insert SID to DB (if unknown) or update visits, even if not logged 
		$find_sid = $db->query("SELECT sessid FROM {$config["tables"]["stats_auth"]} WHERE sessid = '{$this->auth["sessid"]}'");
		if ($db->success) {
			if ($db->num_rows($find_sid) == 0) {
				 $db->query("INSERT INTO {$config["tables"]["stats_auth"]} SET
				sessid = '{$this->auth["sessid"]}',
				userid = '0',
				login = '0',
				ip = '{$this->auth["ip"]}',
				logtime = '{$this->timestamp}',
				logintime = '{$this->timestamp}',
				lasthit = '{$this->timestamp}',
				hits = 1,
				visits = 1
				");
			} elseif($update) {
				// Update visits
				$visit_timeout = time() - 60*60; // If a session loaded no page for over one hour, this counts as a new visit
				$db->query("UPDATE {$config["tables"]["stats_auth"]} SET visits = visits + 1 WHERE (sessid='{$this->auth["sessid"]}') AND (lasthit < $visit_timeout)");
				// Update user-stats and lasthit, so the timeout is resetted
				$db->query("UPDATE {$config["tables"]["stats_auth"]} SET lasthit='{$this->timestamp}', hits = hits + 1, ip='{$this->auth["ip"]}' WHERE sessid='{$this->auth["sessid"]}'");
			}
		}
		$db->free_result($find_sid);

		// Put all User-Data into $auth-Array
		$user_data = $db->query_first("SELECT session.userid, session.login, session.ip, user.*, user_set.design
			FROM {$config["tables"]["stats_auth"]} AS session
			LEFT JOIN {$config["tables"]["user"]} AS user ON user.userid = session.userid
			LEFT JOIN {$config["tables"]["usersettings"]} AS user_set ON user.userid = user_set.userid
			WHERE session.sessid='{$this->auth["sessid"]}' ORDER BY session.lasthit");
		$this->auth = array_merge($this->auth, $user_data);

		// If Login / Logout
		if ($_GET['mod'] == "logout") $this->logout();
		elseif (isset($_POST['login_x'])) $this->login("normal"); # Normal Login
		elseif (isset($_POST['save_x'])) $this->login("save"); # Login + Save
		elseif ($_COOKIE['auth']['email'] != "" and (!$this->auth['login'])) $this->login("cookie"); # Login via Coockie
		elseif ($_COOKIE['auth']['email'] != "" and $this->auth['login']) { # Reset Coockie-Timeout
			setcookie("auth[email]", $_COOKIE['auth']['email'], time() + (3600*24*365));
			setcookie("auth[userpassword]", $_COOKIE['auth']['userpassword'], time() + (3600*24*365));
		}

		// If not logged in, delete the sessions userdata
		if ($this->auth['login'] == "" or $this->auth['login'] == "0"){
			unset($this->auth);
			unset($_SESSION['auth']);
			$this->auth['login'] == "0";
			$this->auth["userid"] = "";
			$this->auth["email"] = "";
			$this->auth["username"] = "";
			$this->auth["userpassword"] = "";
			$this->auth["design"] = "";
			$this->auth["type"] = 0;
		}

		// Reset the design, if none is entered
		if (!$this->auth['design'] || $cfg['user_design_change'] == 0) $this->auth['design'] = $config['lansuite']['default_design'];
		if (!$this->auth['design']) $this->auth['design'] = "standard";

		// Set Session/Coockie-Vars (only for those mods, which still use this variables)
		foreach ($this->auth AS $key => $val){
			$_SESSION['auth'][$key] = $val;
			$_COOCKIE['auth'][$key] = $val;
		}
	}


	// When the User logs off
	function logout() {
		global $db, $config;

		$db->query("UPDATE {$config["tables"]["stats_auth"]} SET login='0' WHERE sessid='{$this->auth["sessid"]}'");

		$this->auth['login'] = "0";
		
		setcookie("auth[email]", "", time() - 3600);
		setcookie("auth[userpassword]", "", time() - 3600);
	}


	// When user logs in
	function login($loginart) {
		global $db, $func, $cfg, $config, $party, $lang;

		$tmp_login_email = "";
		$tmp_login_pass = "";

		if ($loginart == "cookie"){
			if ($_COOKIE['auth']['email'] != "") $tmp_login_email = $_COOKIE['auth']['email'];
			if ($_COOKIE['auth']['userpassword'] != "") $tmp_login_pass = $_COOKIE['auth']['userpassword'];
		} else {
			if ($_POST['email'] != "") $tmp_login_email = strtolower(htmlspecialchars(trim($_POST['email'])));
			if ($_POST['password'] != "") $tmp_login_pass = md5($_POST['password']);
		}

		if ($tmp_login_email == "") $func->information($lang['class_auth']['get_email_or_id'], "");
		elseif ($tmp_login_pass == "") $func->information($lang['class_auth']['get_pw'], "");
		else {
			$user = $db->query_first("SELECT userid, username, email, password, type
				FROM {$config["tables"]["user"]}
				WHERE ('". (int)$tmp_login_email."' = '".$tmp_login_email."' AND userid = '$tmp_login_email')
					OR LOWER(email) = '$tmp_login_email'");

			$party_query = $db->query("SELECT p.checkin, p.checkout FROM {$config["tables"]["party_user"]} AS p WHERE p.party_id={$party->party_id} AND user_id='{$user['userid']}'");

			// Check Checkin
			if ($db->num_rows($party_query) > 0){
				$party_data = $db->fetch_array($party_query);
				$user["checkin"] = $party_data['checkin'];
				$user["checkout"] = $party_data['checkout'];
			}

			// Wrong Password?
			if ($tmp_login_pass != $user["password"]){
				if ($cfg["sys_internet"]) $remindtext = $lang['class_auth']['wrong_pw_inet'];
				else $remindtext = $lang['class_auth']['wrong_pw_lan'];
				$func->information(HTML_FONT_ERROR . $lang['class_auth']['wrong_pw'] . HTML_FONT_END . HTML_NEWLINE . HTML_NEWLINE . $remindtext, "");
				$func->log_event(str_replace("%EMAIL%", $tmp_login_email, $lang['class_auth']['wrong_pw_log']), "2", "Authentifikation");

			// Account disabled?
			} elseif ($user["type"] <= -1) {
				$func->information($lang['class_auth']['closed'], "");
				$func->log_event(str_replace("%EMAIL%", $tmp_login_email, $lang['class_auth']['closed_log']), "2", "Authentifikation");

			// Not checked in?
			} elseif(!$user["checkin"] AND $user["type"] < 2 AND !$cfg["sys_internet"]){
				$func->information($lang['class_auth']['not_checkedin'], ""); 	
				$func->log_event(str_replace("%EMAIL%", $tmp_login_email, $lang['class_auth']['not_checkedin_log']), "2", "Authentifikation");

			// Already checked out?
			} elseif ($user["checkout"] AND $user["type"] < 2 AND !$cfg["sys_internet"]){
				$func->information($lang['class_auth']['checkedout'], "");
				$func->log_event(str_replace("%EMAIL%", $tmp_login_email, $lang['class_auth']['checkedout_log']), "2", "Authentifikation");

			// Everything fine!
			} else {
				$db->query("UPDATE {$config["tables"]["user"]} SET logins = logins + 1, changedate = changedate WHERE userid = '{$user["userid"]}'");
				if ($cfg["sys_logoffdoubleusers"]) $db->query("DELETE FROM {$config["tables"]["stats_auth"]} WHERE userid='{$user["userid"]}'");

				$db->query("UPDATE {$config["tables"]["stats_auth"]} SET
						userid='{$user["userid"]}',
						login='1',
						logintime='{$this->timestamp}'
						WHERE sessid='{$this->auth["sessid"]}'");

	 			// Put all User-Data into $auth-Array
				$user_data = $db->query_first("SELECT session.userid, session.login, session.ip, user.*, user_set.design
					FROM {$config["tables"]["stats_auth"]} AS session
					LEFT JOIN {$config["tables"]["user"]} AS user ON user.userid = session.userid
					LEFT JOIN {$config["tables"]["usersettings"]} AS user_set ON user.userid = user_set.userid
					WHERE session.sessid='{$this->auth["sessid"]}' ORDER BY session.lasthit");
				$this->auth = array_merge($this->auth, $user_data);

				if ($loginart == "save"){
					setcookie("auth[email]", $this->auth['email'], time() + (3600*24*365));
					setcookie("auth[userpassword]", $user_data['password'], time() + (3600*24*365));
				}
				

			}
		}
	}
}
?>