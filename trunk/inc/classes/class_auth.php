<?php

require_once("inc/classes/class.crypt.php");


/**
 * auth
 *
 * @package lansuite
 * @author bytekilla
 * @version $Id$
 * @access public
 */
class auth {

  /**#@+
   * Intern Variables
   * @access private
   * @var mixed
   */
    var $auth = array();                 // Userdaten im Array
    var $timestamp;                      // Zeit
    var $cookie_data =     array();      // Cookiedaten
    var $cookie_name =     "LSAUTH";     // Cookiename
    var $cookie_version =  "1";          // Cookieversion
    var $cookie_domain =   "";           // Domain
    var $cookie_time =     "30";         // Dauer in Tagen
    var $cookie_path =     "/";          // Domainpfad
  /**#@-*/
  /**
   * Constructor
   *
   */
    function auth() {
        // Init-Vars
        $this->auth["sessid"] = session_id();
        $this->auth["ip"] = $_SERVER['REMOTE_ADDR'];
        $this->timestamp = time();
        $this->update_visits(); // Update Statistik
        // $this->load_authdata(); // Load Sessiondata if Possible
        // $this->cookie_read();   // Read Cookiedata
    }

  /**
   * auth::check_logon()
   *
   */
    function check_logon() {
        global $func;
        // Mögliche Fälle
        // 1. ausgeloggt.. keine Session, kein Cookie
        // 1.5 Session aber kein Cookie (Cookies nicht erlaubt, nur User)
        // 2. Keine session aber Cookie (session abgelaufen)
        // 3. Eingeloggt Session und Cookie
        // 4. Eingeloggt Session und Cookie und userswitch

        $this->load_authdata();                  // Load Sessiondata if Possible
        $cookie_status = $this->cookie_read();   // Read Cookiedata
        $cookie_valid = $this->cookie_valid();   // Validate Cookie

        if ($this->auth['login'] == 1) {
        // Session active
            if ($this->auth["type"] > 1) {
            // Procedure for Admin
                // Check Cookie
                if ($cookie_status == 1 AND $cookie_valid == 1) {
                    // Cookie OK
                } else {
                    // Cookie NOK
                    $this->logout();
                    $func->information('Fehlerhafte Cookiedaten. Sie wurden ausgeloggt.', "", '', 1);
                }
            } else {
            // Procedure for User
                // Check Cookie
                if ($cookie_status == 1 AND $cookie_valid == 1) {
                    // Cookie OK
                } else {
                    // Cookie NOK
                    $this->logout();
                    $func->information('Fehlerhafte Cookiedaten. Sie wurden ausgeloggt.', "", '', 1);
                }
            }
        } else {
        // Session inactive, check for Cookielogin
            if (array_key_exists($this->cookie_name, $_COOKIE)) {
                // Check Cookie
                if ($cookie_status == 1) {
                    // Cookie OK
                    // Login per Cookie, Session setzten
                    $this->login_cookie($this->cookie_data['userid'], $this->cookie_data['uniqekey']);
                } else {
                    // Cookie NOK
                    // Cookie löschen
                    // Meldung
                    // Logeintrag
                }
            }

        }

        return $this->auth;
    }

  /**
   * Login auth
   *
   * @param mixed Useremail
   * @param mixed Userpassword
   * @return array Returns the auth-dataarray
   */
    function login($email, $password) {
        global $db, $func, $cfg, $config, $party, $lang, $board_config, $ActiveModules;
        $this->auth['design'] = $config['lansuite']['default_design'];
        $tmp_login_email = "";
        $tmp_login_pass = "";        

        if ($email != "") $tmp_login_email = strtolower(htmlspecialchars(trim($email)));
        if ($password != "") $tmp_login_pass = md5($password);

        if     ($tmp_login_email == "") $func->information($lang['class_auth']['get_email_or_id'], "", '', 1);
        elseif ($tmp_login_pass == "") $func->information($lang['class_auth']['get_pw'], "", '', 1);
        else {
            // Go on if email and password
            $user = $db->query_first("SELECT 1 AS found, userid, username, email, password, type, locked
                                      FROM {$config["tables"]["user"]}
                                      WHERE ('". (int)$tmp_login_email."' = '".$tmp_login_email."' AND userid = '$tmp_login_email')
                                      OR LOWER(email) = '$tmp_login_email'");
            
            $user2 = $db->query_first("SELECT email_verified
                                       FROM {$config["tables"]["user"]}
                                       WHERE ('". (int)$tmp_login_email."' = '".$tmp_login_email."' AND userid = '$tmp_login_email')
                                       OR LOWER(email) = '$tmp_login_email'");

            $party_query = $db->query("SELECT p.checkin, p.checkout
                                       FROM {$config["tables"]["party_user"]} AS p
                                       WHERE p.party_id=". (int)$party->party_id ." AND user_id='{$user['userid']}'");
            // Check Checkin
            if ($db->num_rows($party_query) > 0){
                $party_data = $db->fetch_array($party_query);
                $user["checkin"] = $party_data['checkin'];
                $user["checkout"] = $party_data['checkout'];
            }
            $row = $db->qry_first('SELECT COUNT(*) AS anz
                                   FROM %prefix%login_errors
                                   WHERE userid = %int% AND (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(time) < 60)
                                   GROUP BY userid', $user['userid']);

            // Too many login trys
            if ($row['anz'] >= 5) {
                $func->information(t('Sie haben in der letzten Minute bereits 5 mal Ihr Passwort falsch eingegeben. Bitte waren Sei einen Moment, bevor Sie es erneut versuchen dÃ¼rfen'), '', '', 1);
            // Email not found?
            } elseif (!$user["found"]) {
                $func->information(t('Dieser Benutzer existiert nicht in unserer Datenbank. Bitte prÃ¼fen Sie die eingegebene Email/ID'), '', '', 1);
                $func->log_event(str_replace("%EMAIL%", $tmp_login_email, t('Falsche Email angegeben')), '2', 'Authentifikation');
            // Account disabled?
            } elseif ($user["type"] <= -1) {
                $func->information($lang['class_auth']['closed'], "", '', 1);
                $func->log_event(str_replace("%EMAIL%", $tmp_login_email, $lang['class_auth']['closed_log']), "2", "Authentifikation");
            // Account locked?
            } elseif ($user['locked']){
                $func->information($lang['class_auth']['locked'], '', '', 1);
                $func->log_event(str_replace("%EMAIL%", $tmp_login_email, $lang['class_auth']['locked_log']), "2", "Authentifikation");
            // Mail not verified
            } elseif ($cfg['sys_login_verified_mail_only'] == 2 and !$user['email_verified'] and $user["type"] < 2) {
                $func->information(t('Sie haben Ihre Email-Adresse (%1) noch nicht verifiziert. Bitte folgen Sie dem Link in der Ihnen zugestellten Email', $user['email']), '', '', 1);
                $func->log_event(str_replace("%EMAIL%", $tmp_login_email, t('Login fehlgeschlagen. Email (%1) nicht verifiziert', $user['email'])), "2", "Authentifikation");
            // Wrong Password?
            } elseif ($tmp_login_pass != $user["password"]){
                ($cfg["sys_internet"])? $remindtext = $lang['class_auth']['wrong_pw_inet'] : $remindtext = $lang['class_auth']['wrong_pw_lan'];
                $func->information(t('Die von Ihnen eingebenen Login-Daten sind fehlerhaft. Bitte Ã¼berprÃ¼fen Sie Ihre Eingaben.') . HTML_NEWLINE . HTML_NEWLINE . $remindtext, "", '', 1);
                $func->log_event(str_replace("%EMAIL%", $tmp_login_email, $lang['class_auth']['wrong_pw_log']), "2", "Authentifikation");
                $db->qry('INSERT INTO %prefix%login_errors SET userid = %int%, ip = %string%, time = NOW()', $user['userid'], $_SERVER['REMOTE_ADDR']);
            // Not checked in?
            } elseif((!$user["checkin"] or $user["checkin"] == '0000-00-00 00:00:00') AND $user["type"] < 2 AND !$cfg["sys_internet"]){                $func->information(t('Sie sind nicht eingecheckt. Im Intranetmodus ist ein Einloggen nur mÃ¶glich, wenn Sie eingecheckt sind.') .HTML_NEWLINE. t('Bitte melden Sie sich bei der Organisation.'), "", '', 1);
                $func->log_event(str_replace("%EMAIL%", $tmp_login_email, $lang['class_auth']['not_checkedin_log']), "2", "Authentifikation");
            // Already checked out?
            } elseif ($user["checkout"] and $user["checkout"] != '0000-00-00 00:00:00' AND $user["type"] < 2 AND !$cfg["sys_internet"]){
                $func->information(t('Sie sind bereits ausgecheckt. Im Intranetmodus ist ein Einloggen nur mÃ¶glich, wenn Sie eingecheckt sind.') .HTML_NEWLINE. t('Bitte melden Sie sich bei der Organisation.'), "", '', 1);
                $func->log_event(str_replace("%EMAIL%", $tmp_login_email, $lang['class_auth']['checkedout_log']), "2", "Authentifikation");
            // Everything fine!
            } else {
                // Set Logonstats
                $db->query("UPDATE {$config["tables"]["user"]} SET logins = logins + 1, changedate = changedate WHERE userid = '{$user["userid"]}'");
                if ($cfg["sys_logoffdoubleusers"]) $db->query("DELETE FROM {$config["tables"]["stats_auth"]} WHERE userid='{$user["userid"]}'");
                
                // Set authdata
                $db->query("REPLACE INTO {$config["tables"]["stats_auth"]}
                            SET sessid = '{$this->auth["sessid"]}',
                                userid = '{$user["userid"]}',
                                login = '1',
                                ip = '{$this->auth["ip"]}',
                                logtime = '{$this->timestamp}',
                                logintime = '{$this->timestamp}',
                                lasthit = '{$this->timestamp}'");

                $this->load_authdata();
                $this->auth['userid'] = $user['userid'];

                $this->cookie_data['userid'] = $user['userid'];
                $this->cookie_data['uniqekey'] = md5($user['password']);
                $this->cookie_data['version'] = $this->cookie_version;
                $this->cookie_data['olduserid'] = "";
                $this->cookie_data['sb_code'] = "";
                $this->cookie_set();

                // Print Loginmessages
                if ($_GET['mod']=='auth' AND $_GET['action'] == 'login') $auth_backlink = "?mod=home"; 
                    else $auth_backlink = "";
                $func->information(t('Erfolgreich eingeloggt. Die Änderungen werden beim laden der nächsten Seite wirksam.'), $auth_backlink,'',1);

                // Show error logins
                $msg = '';
                $res = $db->qry('SELECT ip, time
                                 FROM %prefix%login_errors
                                 WHERE userid = %int%', $user['userid']);
                while ($row = $db->fetch_array($res)) $msg .= t('Am') .' '. $row['time'] .' von der IP: <a href="http://www.dnsstuff.com/tools/whois.ch?ip='. $row['ip'] .'" target="_blank">'. $row['ip'] .'</a>'. HTML_NEWLINE;
                $db->free_result($res);
                if ($msg != '') $func->information('<b>'. t('Fehlerhafte Logins') .'</b>'. HTML_NEWLINE .t('Es wurden fehlerhafte Logins seit Ihrem letzten erfolgreichen Login durchgefÃ¼hrt.'). HTML_NEWLINE . HTML_NEWLINE . $msg, NO_LINK, '', 1);
                $db->qry('DELETE FROM %prefix%login_errors WHERE userid = %int%', $user['userid']);

                // The User will be logged in on the phpBB Board if the modul is available, configured and active.
                if (in_array('board2', $ActiveModules) and $config["board2"]["configured"]) {
                    include_once ('./modules/board2/class_board2.php');
                    $board2 = new board2();
                    $board2->loginPhpBB($this->auth['userid']);
                }
            }
        }
        return $this->auth; // For global setting $auth
    }

  /**
   * Login auth via Cookie
   *
   * @access private
   * @param mixed Userid
   * @param mixed Uniquekey
   * @return array Returns the auth-dataarray
   */
    function login_cookie($userid, $uniquekey) {
        global $db, $func, $cfg, $config;

        if     ($userid == "") $func->information(t('Keine Userid beim Login via Cookie erkannt.'), "", '', 1);
        elseif ($uniquekey == "") $func->information(t('Kein Uniquekey beim Login via Cookie erkannt.'), "", '', 1);
        else {

            $user = $db->query_first("SELECT 1 AS found, userid, username, email, password, type, locked
                                      FROM {$config["tables"]["user"]}
                                      WHERE userid = '$userid'");

            if ($uniquekey == (md5($user['password']))) {
                // Set Logonstats
                $db->query("UPDATE {$config["tables"]["user"]} SET logins = logins + 1, changedate = changedate WHERE userid = '{$user["userid"]}'");
                if ($cfg["sys_logoffdoubleusers"]) $db->query("DELETE FROM {$config["tables"]["stats_auth"]} WHERE userid='{$user["userid"]}'");

                // Set authdata
                $db->query("REPLACE INTO {$config["tables"]["stats_auth"]}
                             SET sessid = '{$this->auth["sessid"]}',
                                 userid = '{$user["userid"]}',
                                 login = '1',
                                 ip = '{$this->auth["ip"]}',
                                 logtime = '{$this->timestamp}',
                                 logintime = '{$this->timestamp}',
                                 lasthit = '{$this->timestamp}'");

                 $this->load_authdata();
                 $this->auth['userid'] = $user['userid'];

            } else {
                // DEBUG
                $func->information(t("Uniquekey fehlerhaft"), '','',1);
            }
        }
    }

  /**
   * Logout
   *
   * @return array Returns the cleared auth-dataarray
   */
    function logout() {
        global $db, $config, $ActiveModules;

        // Delete entry from SID table
        $db->query("DELETE FROM {$config['tables']['stats_auth']} WHERE sessid='{$this->auth["sessid"]}'");
        $this->auth['login'] = "0";

        // Reset Cookiedata
        $this->cookie_unset();

        // Reset Sessiondata
        unset($this->auth);
        unset($_SESSION['auth']);
        $this->auth['login'] == "0";
        $this->auth["userid"] = "";
        $this->auth["email"] = "";
        $this->auth["username"] = "";
        $this->auth["userpassword"] = "";
        $this->auth["design"] = "";
        $this->auth["type"] = 0;

        // The User will be logged out on the phpBB Board if the modul is available, configured and active.
        if (in_array('board2', $ActiveModules) and $config['board2']['configured']) {
            include_once ('./modules/board2/class_board2.php');
            $board2 = new board2();
            $board2->logoutPhpBB($this->auth['userid']);
        } 
        return $this->auth;                // For overwrite global $auth
    }

  /**
   * Switch to UserID
   * Switches to given UserID and stores a callbackfunktion in a Cookie and DB
   *
   * @param mixed $target_id
   */
    function switchto($target_id) {
        global $db, $config, $lang, $func;

        // Get target user type
        $target_user = $db->query_first("SELECT type, password FROM {$config["tables"]["user"]} WHERE userid = {$target_id}");

        // Only highlevel to lowerlevel
        if ($this->auth["type"] > $target_user["type"]) {
            // Generate switch back code
            for ($x = 0; $x <= 24; $x++) $switchbackcode .= chr(mt_rand(65, 90));
            // Save old user ID & write cookie
            $this->cookie_data['userid'] = $target_id;
            $this->cookie_data['uniqekey'] = md5($target_user["password"]); // FIX abfrage nach neuen uniqekey
            $this->cookie_data['version'] = $this->cookie_version;
            $this->cookie_data['olduserid'] = $this->auth['userid'];
            $this->cookie_data['sb_code'] = $switchbackcode;
            $this->cookie_set();
            // Store switch back code in current (admin) user data
            $db->query("UPDATE {$config["tables"]["user"]} SET switch_back = '". $switchbackcode ."' WHERE userid = {$this->auth["userid"]}");
            // Link session ID to new user ID
            $db->query("UPDATE {$config["tables"]["stats_auth"]}
                        SET userid='{$target_id}',
                            login='1'
                        WHERE sessid='{$this->auth["sessid"]}'");
            $func->information(t('Benutzerwechsel erfolgreich. Die Ã„nderungen werden beim laden der nÃ¤chsten Seite wirksam.'), $func->internal_referer,'',1);  //FIX meldungen auserhalb/standart?!?
        } else {
            $func->error(t('Ihr Benutzerlevel ist geringer, als das des Ziel-Benutzers. Ein Wechsel ist daher untersagt'), $func->internal_referer,1); //FIX meldungen auserhalb/standart?!
        }
    }

  /**
   * Switchback to Adminuser
   * Logout from the selectet User and go back to the calling Adminuser
   *
   */
    function switchback() {
        global $db, $config, $lang, $func;
        // Make sure that Cookiedata is loaded
        $this->cookie_read();
        if ($this->cookie_data['olduserid'] > 0){
            // Check switch back code
            $admin_user = $db->query_first("SELECT switch_back, password
                                            FROM {$config["tables"]["user"]}
                                            WHERE userid = {$this->cookie_data["olduserid"]}");
            if ($this->cookie_data['sb_code'] == $admin_user["switch_back"]) {
                // Link session ID to origin user ID
                $db->query("UPDATE {$config["tables"]["stats_auth"]}
                            SET userid='{$this->cookie_data["olduserid"]}',
                                login='1'
                            WHERE sessid='{$this->auth["sessid"]}'");
                // Delete switch back code in admins user data
                $db->query("UPDATE {$config["tables"]["user"]} SET switch_back = '' WHERE userid = {$this->cookie_data["olduserid"]}"); 
                $this->cookie_data['userid'] = $this->cookie_data["olduserid"];
                $this->cookie_data['uniqekey'] = md5($admin_user["password"]); // FIX abfrage nach neuen uniqekey
                $this->cookie_data['version'] = $this->cookie_version;
                $this->cookie_data['olduserid'] = '-1';
                $this->cookie_data['sb_code'] = '-1';
                $this->cookie_set();
                $func->information(t('Benutzerwechsel erfolgreich. Die Ã„nderungen werden beim laden der nÃ¤chsten Seite wirksam.'), $func->internal_referer,'',1);
            } else {
                $func->error(t('Fehler: Falscher switch back code! Das kann daran liegen, dass dein Browser keine Cookies unterstÃ¼tzt.'), $func->internal_referer,1);
            }
        } else {
            $func->error(t('Fehler: Keine Switchbackdaten gefunden! Das kann daran liegen, dass dein Browser keine Cookies unterstÃ¼tzt.'), $func->internal_referer,1);
        }
    }

  /**
   * Returns the old Userid if one is set.
   *
   * @return void Olduserid
   */
    function get_olduserid(){
        return $this->cookie_data['olduserid'];
    }


  /**
   * load_authdata
   *
   * @access private
   */
    function load_authdata() {
        global $db, $config;
        // Put all User-Data into $auth-Array
        $user_data = $db->query_first("SELECT session.userid, session.login, session.ip, user.*, user_set.design
            FROM {$config["tables"]["stats_auth"]} AS session
            LEFT JOIN {$config["tables"]["user"]} AS user ON user.userid = session.userid
            LEFT JOIN {$config["tables"]["usersettings"]} AS user_set ON user.userid = user_set.userid
            WHERE session.sessid='{$this->auth["sessid"]}' ORDER BY session.lasthit");
        if (is_array($user_data)) foreach ($user_data as $key => $val) if (!is_numeric($key)) $this->auth[$key] = $val;
        if ($this->auth['design'] == '') $this->auth['design'] = $config['lansuite']['default_design'];
    }

  /**
   * update_visits
   *
   * @access private
   */
    function update_visits() {
        global $db, $config;
        // Update visits, hits, IP and lasthit
        $visit_timeout = time() - 60*60;
        // If a session loaded no page for over one hour, this counts as a new visit
        $db->query("UPDATE {$config["tables"]["stats_auth"]}
                    SET visits = visits + 1
                    WHERE (sessid='{$this->auth["sessid"]}') AND (lasthit < $visit_timeout)");
        // Update user-stats and lasthit, so the timeout is resetted
        $db->query("UPDATE {$config["tables"]["stats_auth"]}
                    SET lasthit='{$this->timestamp}',
                        hits = hits + 1,
                        ip='{$this->auth["ip"]}'
                    WHERE sessid='{$this->auth["sessid"]}'");
    }

  /**
   * Set Cookie for Installadmin
   *
   * @return void
   */
    function set_install_cookie($email, $password){
        global $db, $config;
        $email = strtolower(htmlspecialchars(trim($email)));
        $user = $db->query_first("SELECT userid, password
                                  FROM {$config["tables"]["user"]}
                                  WHERE LOWER(email) = '$email'");
        $this->cookie_data['userid'] = $user['userid'];
        $this->cookie_data['uniqekey'] = md5($user['password']); // FIX
        $this->cookie_data['version'] = $this->cookie_version;
        $this->cookie_data['olduserid'] = "";
        $this->cookie_data['sb_code'] = "";
        $this->cookie_set();
    }

  /**
   * Validate Usercookie
   *
   * @return int Return the Vailidity. 1=OK, 0=NOK
   * @access private
   */
    function cookie_valid() {
        global $db, $config;
        // Get target user type
        if ($this->cookie_data['userid']>=1) $user_row = $db->query_first("SELECT password FROM {$config["tables"]["user"]} WHERE userid = {$this->cookie_data['userid']}");
        $ok = 0;
        // Check for Cookie
        if ($this->cookie_data['uniqekey'] == md5($user_row['password'])) $ok = 1;
        if ($ok==0) $this->cookie_unset();
        return $ok;
    }

  /**
   * Read and check Usercookie
   *
   * @return int Return the Cookiestatus. 1=OK, 0=NOK
   * @access private
   */
    function cookie_read() {
        $ok = 0;
        // Check for Cookie
        if (array_key_exists($this->cookie_name, $_COOKIE)) {
            $this->cookiedata_unpack($_COOKIE[$this->cookie_name]);
            // Look for correkt cookieformat
            if (is_numeric($this->cookie_data['userid']) AND
                is_string($this->cookie_data['uniqekey']) AND
                is_numeric($this->cookie_data['version']) AND
                ($this->cookie_version == $this->cookie_data['version'])) $ok = 1;
        }
        if ($ok==0) $this->cookie_unset();
        return $ok;
    }

  /**
   * Set Cookie for User
   *
   * @access private
   */
    function cookie_set() {
        setcookie($this->cookie_name,
                  $this->cookiedata_pack(),
                  time()+3600*24*$this->cookie_time,
                  $this->cookie_path,
                  $this->cookie_domain);
    }

  /**
   * Delete Usercookie
   *
   * @access private
   */
    function cookie_unset() {
        setcookie($this->cookie_name,
                  '',
                  time()+1,
                  $this->cookie_path,
                  $this->cookie_domain);
    }

  /**
   * Pack and encrypt Cookiedata
   *
   * @return mixed Encryptet Cookiedata
   * @access private
   */
    function cookiedata_pack() {
        $data = array($this->cookie_data['userid'],
                      $this->cookie_data['uniqekey'], 
                      $this->cookie_data['version'],
                      $this->cookie_data['olduserid'],
                      $this->cookie_data['sb_code']);
        $cookie = implode("|", $data);
        $crypt= new AzDGCrypt(md5("synergycookie"));
        return $crypt->crypt($cookie);
    }

  /**
   * Decrypt and unpack Cookiedata
   *
   * @param mixed Encryptet Cookiedata
   * @return mixed Decryptet Cookiedata as array
   */
    function cookiedata_unpack($cookie) {
        $crypt= new AzDGCrypt(md5("synergycookie"));
        $cookie = $crypt->decrypt($cookie);
        list ($this->cookie_data['userid'], 
              $this->cookie_data['uniqekey'], 
              $this->cookie_data['version'],
              $this->cookie_data['olduserid'],
              $this->cookie_data['sb_code']) = explode("|", $cookie);
    }
}

?>