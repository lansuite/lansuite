<?php
// logtime =	Datum des ersten Eintrags zu dieser SessionID
// logintime =	Datum des letzten Einloggens dieser SessionID
// lasthit =	Datum des letzten Seitenaufrufes dieser SessionID

class auth {

	var $auth = array();
	var $timestamp;
	var $tocheck;
	var $err;

	function LoadAuthData() {
	  global $db, $config;
		// Put all User-Data into $auth-Array
		$user_data = $db->query_first("SELECT session.userid, session.login, session.ip, user.*, user_set.design
			FROM {$config["tables"]["stats_auth"]} AS session
			LEFT JOIN {$config["tables"]["user"]} AS user ON user.userid = session.userid
			LEFT JOIN {$config["tables"]["usersettings"]} AS user_set ON user.userid = user_set.userid
			WHERE session.sessid='{$this->auth["sessid"]}' ORDER BY session.lasthit");
		if (is_array($user_data)) foreach ($user_data as $key => $val) if (!is_numeric($key)) $this->auth[$key] = $val;
  }


	// Constructor
	function GetAuthData($update = true) {
		global $db, $config, $func, $cfg;

		// Init-Vars
		$this->auth["sessid"] = session_id();
		$this->auth["ip"] = $_SERVER['REMOTE_ADDR'];
		$this->timestamp = time();

		// Update visits, hits, IP and lasthit
		if ($update) {
  		// Update visits
  		$visit_timeout = time() - 60*60; // If a session loaded no page for over one hour, this counts as a new visit
  		$db->query("UPDATE {$config["tables"]["stats_auth"]} SET
        visits = visits + 1
        WHERE (sessid='{$this->auth["sessid"]}') AND (lasthit < $visit_timeout)");
  
  		// Update user-stats and lasthit, so the timeout is resetted
  		$db->query("UPDATE {$config["tables"]["stats_auth"]} SET
        lasthit='{$this->timestamp}',
        hits = hits + 1,
        ip='{$this->auth["ip"]}'
        WHERE sessid='{$this->auth["sessid"]}'");
		}

    $this->LoadAuthData();

    // Set Coockie from session
    if ($_SESSION['email'] and $_SESSION['password']) {
    	setcookie("auth[email]", $_SESSION['email'], time() + (3600*24*365));
    	setcookie("auth[userpassword]", $_SESSION['password'], time() + (3600*24*365));
    	unset($_SESSION['email']);
    	unset($_SESSION['password']);
    }

		// If Login / Logout
		if ($_GET['mod'] == "logout") $this->logout();
		elseif (isset($_POST['login']) and isset($_POST['password'])) $this->login('save'); # Normal Login
		elseif ($_COOKIE['auth']['email'] != "" and (!$this->auth['login'])) $this->login('cookie'); # Login via Coockie

    // Reset Coockie-Timeout
    if ($_COOKIE['auth']['email'] != '' and $this->auth['login']) {
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

		// Set Session-Vars (only for those mods, which still use this variables)
		foreach ($this->auth AS $key => $val) $_SESSION['auth'][$key] = $val;
		
    return $this->auth;
	}


	// When the User logs off
	function logout() {
		global $db, $config, $ActiveModules;

    // Delete entry from SID table
		$db->query("DELETE FROM {$config['tables']['stats_auth']} WHERE sessid='{$this->auth["sessid"]}'");
		$this->auth['login'] = "0";

		setcookie("auth[email]", "", time() - 3600);
		setcookie("auth[userpassword]", "", time() - 3600);

		// The User will be logged out on the phpBB Board if the modul is available, configured and active.
		if (in_array('board2', $ActiveModules) and $config['board2']['configured']) {
			include_once ('./modules/board2/class_board2.php');
			$board2 = new board2();
			$board2->logoutPhpBB($this->auth['userid']);
		}
	}


	// When user logs in
	function login($loginart = 'save') {
		global $db, $func, $cfg, $config, $party, $lang, $auth, $board_config, $ActiveModules;

		$auth['design'] = $config['lansuite']['default_design'];
		
		$tmp_login_email = "";
		$tmp_login_pass = "";

		if ($loginart == "cookie"){
			if ($_COOKIE['auth']['email'] != "") $tmp_login_email = $_COOKIE['auth']['email'];
			if ($_COOKIE['auth']['userpassword'] != "") $tmp_login_pass = md5($_COOKIE['auth']['userpassword']);
		} else {
			if ($_POST['email'] != "") $tmp_login_email = strtolower(htmlspecialchars(trim($_POST['email'])));
			if ($_POST['password'] != "") $tmp_login_pass = md5($_POST['password']);
		}

		if ($tmp_login_email == "") $func->information($lang['class_auth']['get_email_or_id'], "", '', 1);
		elseif ($tmp_login_pass == "") $func->information($lang['class_auth']['get_pw'], "", '', 1);
		else {

			$user = $db->query_first("SELECT 1 AS found, userid, username, email, password, type, locked
				FROM {$config["tables"]["user"]}
				WHERE ('". (int)$tmp_login_email."' = '".$tmp_login_email."' AND userid = '$tmp_login_email')
					OR LOWER(email) = '$tmp_login_email'");

			$party_query = $db->query("SELECT p.checkin, p.checkout FROM {$config["tables"]["party_user"]} AS p WHERE p.party_id=". (int)$party->party_id ." AND user_id='{$user['userid']}'");

			// Check Checkin
			if ($db->num_rows($party_query) > 0){
				$party_data = $db->fetch_array($party_query);
				$user["checkin"] = $party_data['checkin'];
				$user["checkout"] = $party_data['checkout'];
			}

			$row = $db->qry_first('SELECT COUNT(*) AS anz FROM %prefix%login_errors WHERE userid = %int% AND (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(time) < 60) GROUP BY userid', $user['userid']);

      // Too many login trys
      if ($row['anz'] >= 5) {
        $func->information(t('Sie haben in der letzten Minute bereits 5 mal Ihr Passwort falsch eingegeben. Bitte waren Sei einen Moment, bevor Sie es erneut versuchen dürfen'), '', '', 1);

			// Email not found?
			} elseif (!$user["found"]) {
				$func->information(t('Dieser Benutzer existiert nicht in unserer Datenbank. Bitte prüfen Sie die eingegebene Email/ID'), '', '', 1);
				$func->log_event(str_replace("%EMAIL%", $tmp_login_email, t('Falsche Email angegeben')), '2', 'Authentifikation');

			// Account disabled?
			} elseif ($user["type"] <= -1) {
				$func->information($lang['class_auth']['closed'], "", '', 1);
				$func->log_event(str_replace("%EMAIL%", $tmp_login_email, $lang['class_auth']['closed_log']), "2", "Authentifikation");

			// Account locked?
			} elseif ($user['locked']){
				$func->information($lang['class_auth']['locked'], '', '', 1);
				$func->log_event(str_replace("%EMAIL%", $tmp_login_email, $lang['class_auth']['locked_log']), "2", "Authentifikation");

			// Wrong Password?
			} elseif ($tmp_login_pass != $user["password"]){
				($cfg["sys_internet"])? $remindtext = $lang['class_auth']['wrong_pw_inet'] : $remindtext = $lang['class_auth']['wrong_pw_lan'];
				$func->information(t('Die von Ihnen eingebenen Login-Daten sind fehlerhaft. Bitte überprüfen Sie Ihre Eingaben.') . HTML_NEWLINE . HTML_NEWLINE . $remindtext, "", '', 1);
				$func->log_event(str_replace("%EMAIL%", $tmp_login_email, $lang['class_auth']['wrong_pw_log']), "2", "Authentifikation");
				$db->qry('INSERT INTO %prefix%login_errors SET userid = %int%, ip = %string%, time = NOW()', $user['userid'], $_SERVER['REMOTE_ADDR']);

			// Not checked in?
			} elseif(!$user["checkin"] and $user["checkin"] != '0000-00-00 00:00:00' AND $user["type"] < 2 AND !$cfg["sys_internet"]){
				$func->information(t('Sie sind nicht eingecheckt. Im Intranetmodus ist ein Einloggen nur möglich, wenn Sie eingecheckt sind.') .HTML_NEWLINE. t('Bitte melden Sie sich bei der Organisation.'), "", '', 1);
				$func->log_event(str_replace("%EMAIL%", $tmp_login_email, $lang['class_auth']['not_checkedin_log']), "2", "Authentifikation");

			// Already checked out?
			} elseif ($user["checkout"] and $user["checkout"] != '0000-00-00 00:00:00' AND $user["type"] < 2 AND !$cfg["sys_internet"]){
				$func->information(t('Sie sind bereits ausgecheckt. Im Intranetmodus ist ein Einloggen nur möglich, wenn Sie eingecheckt sind.') .HTML_NEWLINE. t('Bitte melden Sie sich bei der Organisation.'), "", '', 1);
				$func->log_event(str_replace("%EMAIL%", $tmp_login_email, $lang['class_auth']['checkedout_log']), "2", "Authentifikation");

			// Everything fine!
			} else {
				$db->query("UPDATE {$config["tables"]["user"]} SET logins = logins + 1, changedate = changedate WHERE userid = '{$user["userid"]}'");
				if ($cfg["sys_logoffdoubleusers"]) $db->query("DELETE FROM {$config["tables"]["stats_auth"]} WHERE userid='{$user["userid"]}'");

  			$db->query("REPLACE INTO {$config["tables"]["stats_auth"]} SET
    			sessid = '{$this->auth["sessid"]}',
  				userid = '{$user["userid"]}',
    			login = '1',
    			ip = '{$this->auth["ip"]}',
    			logtime = '{$this->timestamp}',
    			logintime = '{$this->timestamp}',
    			lasthit = '{$this->timestamp}'
          ");

	 			$this->LoadAuthData();

        if ($loginart = 'save') {
          $_SESSION['email'] = $this->auth['email'];
          $_SESSION['password'] = $_POST['password'];
        }

				$this->auth['userid'] = $user['userid'];

        // Show error logins
				$msg = '';
				$res = $db->qry('SELECT ip, time FROM %prefix%login_errors WHERE userid = %int%', $user['userid']);
				while ($row = $db->fetch_array($res)) $msg .= t('Am') .' '. $row['time'] .' von der IP: <a href="http://www.dnsstuff.com/tools/whois.ch?ip='. $row['ip'] .'" target="_blank">'. $row['ip'] .'</a>'. HTML_NEWLINE;
        $db->free_result($res);
        if ($msg != '') $func->information('<b>'. t('Fehlerhafte Logins') .'</b>'. HTML_NEWLINE .t('Es wurden fehlerhafte Logins seit Ihrem letzten erfolgreichen Login durchgeführt.'). HTML_NEWLINE . HTML_NEWLINE . $msg, NO_LINK, '', 1);
				$db->qry('DELETE FROM %prefix%login_errors WHERE userid = %int%', $user['userid']);

				// The User will be logged in on the phpBB Board if the modul is available, configured and active.
		    if (in_array('board2', $ActiveModules) and $config["board2"]["configured"]) {
					include_once ('./modules/board2/class_board2.php');
					$board2 = new board2();
					$board2->loginPhpBB($this->auth['userid']);
				}
			}
		}
	}
	
	function isCurrentUserOperator($module) {
		global $db, $config, $func;
		if ($this->auth['type'] != 2)
			return 0;
		
		$count = $db->num_rows($db->query('SELECT userid FROM ' . $config['database']['prefix'] . 'user_permissions WHERE module = \'' . $module . '\' AND userid = \'' . $this->auth['userid'] . '\''));
		if ($count > 0) //If the user is operator of the module.
			return 1;
		
		$count2 = $db->num_rows($db->query('SELECT userid FROM ' . $config['database']['prefix'] . 'user_permissions WHERE module = \'' . $module . '\''));
		if ($count2 == 0) //when no user is operator of this module, every user is operator of the module.
			return 1;

		return 0;
	}
}
?>
