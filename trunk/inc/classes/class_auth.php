<?php

require_once("inc/classes/class.crypt.php");


/**
 * Authorisation and Cookiemanagement for Lansuite
 *
 * @package lansuite_core
 * @author bytekilla
 * @version $Id$
 * @access public
 * @todo Change uniqkey from md5(password) to an extra Field
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
    var $cookie_path =     "";           // Cookiepath. Left blank for autodetect
    var $cookie_crypt =    true;         // Crypt Cookie with AzDGCrypt
    var $cookie_crypt_pw = "iD9ww32e";   // Passphrase for AzDGCrypt
    var $online_users = array();         // Array containing all users, currently online
 /**#@-*/
  
  /**
   * CONSTRUCTOR : Initialize basic Variables for Authorisation
   * @param mixed Frameworkmode for switch Stats
   *
   */
    function auth($frmwrkmode="") {
        global $db;
        
        // Init-Vars
        $this->auth["sessid"] = session_id();
        $this->auth["ip"] = $_SERVER['REMOTE_ADDR'];
        $this->timestamp = time();              // Timestamp for Statistik
        $this->update_visits($frmwrkmode);      // Update Statistik
        //$this->cookie_crypt = $cfg[''];       // Crypt via Config
        //$this->cookie_crypt_pw = $cfg[''];    // CryptPW via Config => uniqekey
        
        // Better handle it here, otherwise its an DB-Query for each $dsp->FetchUserIcon()
        $res = $db->qry('SELECT userid FROM %prefix%stats_auth
            WHERE login = "1" AND lasthit > %int% AND userid > 0
            GROUP BY userid',
            time() - 60*10);
        while ($row = $db->fetch_array($res)) $this->online_users[] = $row['userid'];
        
        // Close sessions older than 1-2 hours.
        // ceil(x / $oneHour) * $oneHour for making query the same for one hour and therefore cacheable by MySQL
        // Do check first, for SELECT is faster than DELETE
        $oneHour = 60 * 60;
        $thirtyDays = 60 * 60 * 24 * 30;
        $row = $db->qry_first('SELECT 1 AS found FROM %prefix%stats_auth WHERE lasthit < %int%', ceil((time() - $oneHour) / $oneHour) * $oneHour);
        if ($row['found']) {
            $row = $db->qry_first('DELETE FROM %prefix%stats_auth WHERE lasthit < %int%', ceil((time() - $oneHour) / $oneHour) * $oneHour);
            $row = $db->qry_first('OPTIMIZE TABLE %prefix%stats_auth');
            
            // Delete cookie after 30 days
            // (TODO: Maybe make this time a config option)
            // (TODO: Maybe differ time for admins and non-admins)
            $row = $db->qry_first('SELECT 1 AS found FROM %prefix%cookie WHERE lastchange < %int%', ceil((time() - $thirtyDays) / $oneHour) * $oneHour);
            if ($row['found']) {
                $row = $db->qry_first('DELETE FROM %prefix%cookie WHERE lastchange < %int%', ceil((time() - $thirtyDays) / $oneHour) * $oneHour);
                $row = $db->qry_first('OPTIMIZE TABLE %prefix%cookie');
            }
        }
    }

  /**
   * Check Userlogon via Session or Cookie. Check some Security Options
   * and set or delete logon if any Problem are found.
   * 
   * @todo Set more securityfunctions
   * @return array Returns the auth-Array
   */
    function check_logon() {
        global $func;
        // Mögliche Fälle
        // 1. ausgeloggt.. keine Session, kein Cookie
        // 1.5 Session aber kein Cookie (Cookies nicht erlaubt, nur User)
        // 2. Keine session aber Cookie (session abgelaufen)
        // 3. Eingeloggt Session und Cookie
        // 4. Eingeloggt Session und Cookie und userswitch

        // Read Cookiedata
        $CookieStatus = $this->cookie_read();
        
        // Look for SessionID in DB and load auth-data
        // Not found? Then look for valid cookie
            // Found? Then try cookie login
            // Not Found?: Cookie invalide. But no message, for maybe the user don't likes to log in
        if (!$this->loadAuthBySID() and $CookieStatus == 1) $this->login_cookie($this->cookie_data['userid'], $this->cookie_data['uniqekey']);

        return $this->auth;
    }

  /**
   * Check and Login a User.
   *
   * @todo Get the Messages out of the Class, just make Strings
   * @param mixed Useremail
   * @param mixed Userpassword
   * @return array Returns the auth-dataarray
   */
    function login($email, $password, $show_confirmation = 1) {
        global $db, $func, $cfg, $party, $ActiveModules;

        $tmp_login_email = "";
        $tmp_login_pass = "";        

        if ($email != "") $tmp_login_email = strtolower(htmlspecialchars(trim($email)));
        if ($password != "") $tmp_login_pass = md5($password);

        if     ($tmp_login_email == "") $func->information(t('Bitte geben Sie Ihre E-Mail-Adresse oder Ihre Lansuite-ID ein.'), "", '', 1);
        elseif ($tmp_login_pass == "") $func->information(t('Bitte geben Sie Ihr Kennwort ein.'), "", '', 1);
        else {
            $is_email = strstr($tmp_login_email, '@');
            if(!$is_email) $is_email = 0; else $is_email = 1;

            // Search in cookie table for id + pw
            $cookierow = $db->qry_first('SELECT userid from %prefix%cookie WHERE cookieid = %int% AND password = %string%', $tmp_login_email, $tmp_login_pass);
            if ($cookierow['userid']) $user = $db->qry_first('SELECT 1 AS found, u.*, p.checkin, p.checkout
              FROM %prefix%user AS u
              LEFT JOIN %prefix%party_user AS p ON u.userid = p.user_id
              WHERE (u.userid = %int%) AND (p.party_id IS NULL OR p.party_id=%int%)',
              $cookierow['userid'], $party->party_id);
 
            // Not found in cookie table, then check for manual login (either with email, oder userid)
            else $user = $db->qry_first('SELECT 1 AS found, 1 AS user_login, u.*, p.checkin, p.checkout
              FROM %prefix%user AS u
              LEFT JOIN %prefix%party_user AS p ON u.userid = p.user_id
              WHERE ((u.userid = %int% AND 0 = %int%) OR LOWER(u.email) = %string%) AND (p.party_id IS NULL OR p.party_id=%int%)',
              $tmp_login_email, $is_email, $tmp_login_email, $party->party_id);

            // Count login errors
            $row = $db->qry_first('SELECT COUNT(*) AS anz
               FROM %prefix%login_errors
               WHERE userid = %int% AND (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(time) < 60)
               GROUP BY userid', $user['userid']);

            // Too many login trys?
            if ($row['anz'] >= 5) {
                $func->information(t('Sie haben in der letzten Minute bereits 5 mal Ihr Passwort falsch eingegeben. Bitte waren Sei einen Moment, bevor Sie es erneut versuchen dürfen'), '', '', 1);
            // Email not found?
            } elseif (!$user["found"]) {
                $func->information(t('Dieser Benutzer existiert nicht in unserer Datenbank. Bitte prüfen Sie die eingegebene Email/ID'), '', '', 1);
                $func->log_event(t('Falsche Email angegeben (%1)', $tmp_login_email), '2', 'Authentifikation');
            // Account disabled?
            } elseif ($user["type"] <= -1) {
                $func->information(t('Ihr Account ist gesperrt. Melden Sie sich bitte bei der Organisation.'), "", '', 1);
                $func->log_event(t('Login für %1 fehlgeschlagen (Account gesperrt).', $tmp_login_email), "2", "Authentifikation");
            // Account locked?
            } elseif ($user['locked']){
                $func->information(t('Dieser Account ist noch nicht freigeschaltet. Bitte warten Sie bis ein Organisator Sie freigeschaltet hat.'), '', '', 1);
                $func->log_event(t('Account von %1 ist noch gesperrt. Login daher fehlgeschlagen.', $tmp_login_email), "2", "Authentifikation");
            // Mail not verified?
            } elseif ($cfg['sys_login_verified_mail_only'] == 2 and !$user['email_verified'] and $user["type"] < 2) {
                $func->information(t('Sie haben Ihre Email-Adresse (%1) noch nicht verifiziert. Bitte folgen Sie dem Link in der Ihnen zugestellten Email.', $user['email']).' <a href="index.php?mod=usrmgr&action=verify_email&step=2&userid='. $user['userid'] .'">'. t('Klicken Sie hier, um die Mail erneut zu versenden</a>'), '', '', 1);
                $func->log_event(t('Login fehlgeschlagen. Email (%1) nicht verifiziert', $user['email']), "2", "Authentifikation");
            // User login and wrong password?
            } elseif ($user["user_login"] and $tmp_login_pass != $user["password"]) {
                ($cfg["sys_internet"])? $remindtext = t('Haben Sie ihr Passwort vergessen?<br/><a href="/index.php?mod=usrmgr&action=pwrecover"/>Hier können Sie sich ein neues Passwort generieren</a>.') : $remindtext = t('Sollten Sie ihr Passwort vergessen haben, wenden Sie sich bitte an die Organisation.');
                $func->information(t('Die von Ihnen eingebenen Login-Daten sind fehlerhaft. Bitte überprüfen Sie Ihre Eingaben.') . HTML_NEWLINE . HTML_NEWLINE . $remindtext, "", '', 1);
                $func->log_event(t('Login für %1 fehlgeschlagen (Passwort-Fehler).', $tmp_login_email), "2", "Authentifikation");
                $db->qry('INSERT INTO %prefix%login_errors SET userid = %int%, ip = %string%, time = NOW()', $user['userid'], $_SERVER['REMOTE_ADDR']);
            // Cookie login and no correct cookie supplied?
            } elseif (!$user["user_login"] and !$cookierow['userid']) {
                ($cfg["sys_internet"])? $remindtext = t('Haben Sie ihr Passwort vergessen?<br/><a href="/index.php?mod=usrmgr&action=pwrecover"/>Hier können Sie sich ein neues Passwort generieren</a>.') : $remindtext = t('Sollten Sie ihr Passwort vergessen haben, wenden Sie sich bitte an die Organisation.');
                $func->information(t('Ihre Session ist abgelaufen und das bei Ihnen gesetzte Cookie ist fehlerhaft. Leider konnten Sie damit nicht eingeloggt werden. Bitte loggen Sie sich neben erneut manuell ein'), "", '', 1);
                $func->log_event(t('Login für %1 fehlgeschlagen (Cookie-Fehler).', $tmp_login_email), "2", "Authentifikation");
                $db->qry('INSERT INTO %prefix%login_errors SET userid = %int%, ip = %string%, time = NOW()', $user['userid'], $_SERVER['REMOTE_ADDR']);
                $this->cookie_unset();
            // Not checked in?
            } elseif((!$user["checkin"] or $user["checkin"] == '0000-00-00 00:00:00') AND $user["type"] < 2 AND !$cfg["sys_internet"]){                $func->information(t('Sie sind nicht eingecheckt. Im Intranetmodus ist ein Einloggen nur möglich, wenn Sie eingecheckt sind.') .HTML_NEWLINE. t('Bitte melden Sie sich bei der Organisation.'), "", '', 1);
                $func->log_event(t('Login für %1 fehlgeschlagen (Account nicht eingecheckt).', $tmp_login_email), "2", "Authentifikation");
            // Already checked out?
            } elseif ($user["checkout"] and $user["checkout"] != '0000-00-00 00:00:00' AND $user["type"] < 2 AND !$cfg["sys_internet"]){
                $func->information(t('Sie sind bereits ausgecheckt. Im Intranetmodus ist ein Einloggen nur möglich, wenn Sie eingecheckt sind.') .HTML_NEWLINE. t('Bitte melden Sie sich bei der Organisation.'), "", '', 1);
                $func->log_event(t('Login für %1 fehlgeschlagen (Account ausgecheckt).', $tmp_login_email), "2", "Authentifikation");
            // Everything fine!
            } else {

                // Set Logonstats
                $db->qry('UPDATE %prefix%user SET logins = logins + 1, changedate = changedate WHERE userid = %int%', $user['userid']);
                
                // If not logged in by cookie, generete new cookie and store it
                if (!$cookierow['userid']) {
                    $password_cookie = $this->gen_rnd_key(40);
                    $db->qry('INSERT INTO %prefix%cookie SET password = %string%, userid = %int%', md5($password_cookie),  $user['userid']);
                    $this->cookie_data['userid'] = $db->insert_id();
                    $this->cookie_data['uniqekey'] = $password_cookie;
                    $this->cookie_data['version'] = $this->cookie_version;
                    $this->cookie_data['olduserid'] = "";
                    $this->cookie_data['sb_code'] = "";
                    $this->cookie_set();
                }

                if ($cfg["sys_logoffdoubleusers"]) {
                    $db->qry('DELETE FROM %prefix%stats_auth WHERE userid = %int%', $user['userid']);
                    $db->qry('DELETE FROM %prefix%cookie WHERE userid = %int% AND cookieid != %int%', $user['userid'], $this->cookie_data['userid']);
                }
                
                // Set authdata
                $db->qry('REPLACE INTO %prefix%stats_auth
                  SET sessid = %string%, userid = %int%, login = \'1\', ip = %string%, logtime = %string%, logintime = %string%, lasthit = %string%',
                  $this->auth["sessid"], $user["userid"], $this->auth["ip"], $this->timestamp, $this->timestamp, $this->timestamp);

                $this->loadAuthBySID();
                $this->auth['userid'] = $user['userid'];
                if (!in_array($user['userid'], $this->online_users)) $this->online_users[] = $user['userid'];

                if ($show_confirmation) { 
                  // Print Loginmessages
                  if ($_GET['mod']=='auth' AND $_GET['action'] == 'login') $auth_backlink = "?mod=home"; 
                      else $auth_backlink = "";
                  $func->confirmation(t('Erfolgreich eingeloggt. Die Änderungen werden beim laden der nächsten Seite wirksam.'), $auth_backlink,'', 'FORWARD');
  
                  // Show error logins
                  $msg = '';
                  $res = $db->qry('SELECT ip, time
                                   FROM %prefix%login_errors
                                   WHERE userid = %int%', $user['userid']);
                  while ($row = $db->fetch_array($res)) $msg .= t('Am') .' '. $row['time'] .' von der IP: <a href="http://www.dnsstuff.com/tools/whois.ch?ip='. $row['ip'] .'" target="_blank">'. $row['ip'] .'</a>'. HTML_NEWLINE;
                  $db->free_result($res);
                  if ($msg != '') $func->information('<b>'. t('Fehlerhafte Logins') .'</b>'. HTML_NEWLINE .t('Es wurden fehlerhafte Logins seit Ihrem letzten erfolgreichen Login durchgeführt.'). HTML_NEWLINE . HTML_NEWLINE . $msg, NO_LINK, '', 1);
                  $db->qry('DELETE FROM %prefix%login_errors WHERE userid = %int%', $user['userid']);
                }

                // The User will be logged in on the phpBB Board if the modul is available, configured and active.
                $this->loginPhpbb();
            }
        }
        return $this->auth; // For global setting $auth
    }

  /**
   * Login User via Cookie e.g. if Session is expired
   *
   * @access private
   * @param mixed Userid
   * @param mixed Uniquekey
   * @return array Returns the auth-dataarray
   */
    function login_cookie($userid, $uniquekey) {
        global $db, $func, $cfg;

        if     ($userid == "") $func->information(t('Keine Userid beim Login via Cookie erkannt.'), "", '', 1);
        elseif ($uniquekey == "") $func->information(t('Kein Uniquekey beim Login via Cookie erkannt.'), "", '', 1);
        else {
          $this->login($userid, $uniquekey, 0);
        }
    }
    
    /**
     * Logs the user on the phpbb board on, if the board was integrated.
     */
    function loginPhpbb($userid = '') {
        global $config, $ActiveModules;

        if ($userid == '')
            $userid = $this->auth['userid'];

        // The User will be logged in on the phpBB Board if the modul is available, configured and active.
        if ($config['environment']['configured'])
        {
            if (in_array('board2', $ActiveModules) and $config["board2"]["configured"]) {
                include_once ('./modules/board2/class_board2.php');
                $board2 = new Board2();
                $board2->loginPhpBB($userid);
            }
        }
    }
    
    /**
     * Logout the User and delete Sessiondata, Cookie and Authdata
     *
     * @return array Returns the cleared auth-dataarray
     */
    function logout() {
        global $db, $ActiveModules, $func;

        // Delete entry from SID
        $db->qry('DELETE FROM %prefix%stats_auth WHERE sessid=%string%', $this->auth["sessid"]);
        $this->auth['login'] = "0";

        // Reset Cookiedata
        $this->cookie_read();
        $db->qry('DELETE FROM %prefix%cookie WHERE userid = %int% AND cookieid = %int%', $this->auth['userid'], $this->cookie_data['userid']);
        $this->cookie_unset();
        
        // Logs the user from the board2 off.
        $this->logoutPhpbb();

        // Reset Sessiondata
        unset($this->auth);
        unset($_SESSION['auth']);
        $this->auth['login'] == "0";
        $this->auth["userid"] = "";
        $this->auth["email"] = "";
        $this->auth["username"] = "";
        $this->auth["userpassword"] = "";
        $this->auth["type"] = 0;

        $func->confirmation(t('Sie wurden erfolgreich ausgeloggt. Vielen dank für ihren Besuch.'), "", 1, FORWARD);
        return $this->auth;                // For overwrite global $auth
    }
    
    /**
     * Logs the user from the phpbb board off, if it was integrated.
     */
    function logoutPhpbb() {
        global $config, $ActiveModules;
 
        // The User will be logged out on the phpBB Board if the modul is available, configured and active.
        if (in_array('board2', $ActiveModules) and $config['board2']['configured'] and $this->auth['userid'] != '') {
            include_once ('./modules/board2/class_board2.php');
            $board2 = new board2();
            $board2->logoutPhpBB($this->auth['userid']);
        } 
    }

  /**
   * Switch to UserID
   * Switches to given UserID and stores a callbackfunktion in a Cookie and DB
   *
   * @param mixed $target_id
   */
    function switchto($target_id) {
        global $db, $func;

        // Get target user type
        $target_user = $db->qry_first('SELECT type FROM %prefix%user WHERE userid = %int%', $target_id);

        // Only highlevel to lowerlevel
        if ($this->auth["type"] > $target_user["type"]) {
            $switchbackcode = $this->gen_rnd_key(24); // Generate switch back code

            // Save old user ID & write cookie
            $this->cookie_data['olduserid'] = $this->auth['userid'];
            $this->cookie_data['sb_code'] = $switchbackcode;
            $this->cookie_set();
            
            // Store switch back code in current (admin) user data
            $db->qry('UPDATE %prefix%user SET switch_back = %string% WHERE userid = %int%', md5($switchbackcode), $this->auth["userid"]);
            // Link session ID to new user ID
            $db->qry('UPDATE %prefix%stats_auth SET userid=%int%, login=\'1\' WHERE sessid=%string%', $target_id, $this->auth["sessid"]);
            
            // Logs the auser out on the board2 and logs the new user on
            //TODO: fix switch user phpbb logon
            //$this->logoutPhpbb();
            //$this->loginPhpbb($target_id);
            
            $func->confirmation(t('Benutzerwechsel erfolgreich. Die &Auml;nderungen werden beim laden der nächsten Seite wirksam.'), $func->internal_referer, 1);  //FIX meldungen auserhalb/standart?!?
        } else {
            $func->error(t('Ihr Benutzerlevel ist geringer, als das des Ziel-Benutzers. Ein Wechsel ist daher untersagt'), $func->internal_referer, 1); //FIX meldungen auserhalb/standart?!
        }
    }

    /**
     * Switchback to Adminuser
     * Logout from the selectet User and go back to the calling Adminuser
     */
    function switchback() {
        global $db, $func;
        // Make sure that Cookiedata is loaded
        $this->cookie_read();
        if ($this->cookie_data['olduserid'] > 0) {
            // Check switch back code
            $admin_user = $db->qry_first('SELECT switch_back FROM %prefix%user WHERE userid = %int%', $this->cookie_data["olduserid"]);
            if (md5($this->cookie_data['sb_code']) == $admin_user["switch_back"]) {
                // Link session ID to origin user ID
                $db->qry('UPDATE %prefix%stats_auth SET userid=%int%, login=\'1\' WHERE sessid=%string%', $this->cookie_data["olduserid"], $this->auth["sessid"]);
                // Delete switch back code in admins user data
                $db->qry('UPDATE %prefix%user SET switch_back = \'\' WHERE userid = %int%', $this->cookie_data['olduserid']); 

                // Unset switch cookie data
                $this->cookie_data['olduserid'] = '';
                $this->cookie_data['sb_code'] = '';
                $this->cookie_set();
                
                // Logs the new user out on the board2 and logs the admin user on again
                //TODO: fix switch user phpbb logon
                //$this->logoutPhpbb();
                //$this->loginPhpbb($this->cookie_data['userid']);
                
                $func->confirmation(t('Benutzerwechsel erfolgreich. Die Änderungen werden beim laden der nächsten Seite wirksam.'), $func->internal_referer,1);
            } else {
                $func->information(t('Fehler: Falscher switch back code! Das kann daran liegen, dass dein Browser keine Cookies unterstützt.'), $func->internal_referer,'',1);
            }
        } else {
            $func->information(t('Fehler: Keine Switchbackdaten gefunden! Das kann daran liegen, dass dein Browser keine Cookies unterstützt.'), $func->internal_referer,'',1);
        }
    }

  /**
   * Check Userrights and add a Errormessage if needed
   *
   * @param mixed $requirement
   * @return
   */
    function authorized($requirement) {
        global $func;
    
        switch ($requirement) {
            case 1: // Logged in
                if ($this->auth['login']) return 1;
                else $func->error('NO_LOGIN', '');
            break;
    
            case 2: // Type is Admin, or Superadmin
                if ($this->auth['type'] > 1)   return 1;
                elseif (!$this->auth['login']) $func->error('NO_LOGIN', '');
                else   $func->error('ACCESS_DENIED', '');
            break;
    
            case 3: // Type is Superadmin
                if ($this->auth['type'] > 2) return 1;
                elseif (!$this->auth['login']) $func->error('NO_LOGIN', '');
                else $func->error('ACCESS_DENIED', '');
            break;
    
            case 4: // Type is User, or less
                if ($this->auth['type'] < 2) return 1;
                else $func->error('ACCESS_DENIED', '');
            break;
    
            case 5: // Logged out
                if (!$this->auth['login']) return 1;
                else $func->error('ACCESS_DENIED', '');
            break;
    
            default:
                return 1;
            break;
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
   * Load all needed Auth-Data from DB and set to auth[]
   *
   * @access private
   */
    function loadAuthBySID() {
        global $db;
        // Put all User-Data into $auth-Array
        $user_data = $db->qry_first('SELECT 1 AS found, session.userid, session.login, session.ip, user.*
            FROM %prefix%stats_auth AS session
            LEFT JOIN %prefix%user AS user ON user.userid = session.userid
            WHERE session.sessid=%string% ORDER BY session.lasthit', $this->auth["sessid"]);
        if (is_array($user_data)) foreach ($user_data as $key => $val) if (!is_numeric($key)) $this->auth[$key] = $val;
        return $this->auth['login'];
    }

  /**
   * Update Visitdata in stats_auth Table
   *
   * @access private
   */
    function update_visits($frmwrkmode="") {
        global $db;
        if($frmwrkmode != "ajax" OR $frmwrkmode != "print" OR $frmwrkmode != "popup" OR $frmwrkmode != "base") {
            // Update visits, hits, IP and lasthit
            $visit_timeout = time() - 60*60;
            // If a session loaded no page for over one hour, this counts as a new visit
            $db->qry('UPDATE %prefix%stats_auth SET visits = visits + 1 WHERE (sessid=%string%) AND (lasthit < %int%)', $this->auth["sessid"], $visit_timeout);
            // Update user-stats and lasthit, so the timeout is resetted
            $db->qry('UPDATE %prefix%stats_auth SET lasthit=%int%, hits = hits + 1, ip=%string%, lasthiturl= %string% WHERE sessid=%string%', $this->timestamp, $this->auth["ip"], $_SERVER['REQUEST_URI'], $this->auth["sessid"]);
        }
    }

  /**
   * Set Cookie for Installadmin
   *
   * @return void
   */
    function set_install_cookie($email, $password) {
        global $db;

        $email = strtolower(htmlspecialchars(trim($email)));
        $user = $db->qry_first('SELECT userid, password FROM %prefix%user WHERE LOWER(email) = %string%', $email);

        // Generate cookie PW
        $password_cookie = $this->gen_rnd_key(40);

        // Set new Cookie PW
        $db->qry('UPDATE %prefix%user SET password_cookie = %string% WHERE userid = %int%', md5($password_cookie), $user['userid']);

        $this->cookie_data['userid'] = $user['userid'];
        $this->cookie_data['uniqekey'] = $password_cookie;
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
        global $db;

        if ($this->cookie_data['userid'] >= 1) $user_row = $db->qry_first('SELECT password FROM %prefix%cookie WHERE userid = %int%', $this->cookie_data['userid']);
        if (md5($this->cookie_data['uniqekey']) == $user_row['password']) return 1;
        else {
            $this->cookie_unset();
            return 0;
        }
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
        if (!$ok) {
            // i.e. user don't like to log in
            // But still we maybe need Cookies for not logged in users. So just keep it.
            #$this->cookie_unset();
            #$func->error(t('Fehlerhafte Cookie-Daten. Cookie wurde gelöscht'), "", '', 1);
            #$func->log_event(t('Fehlerhafte Cookie-Daten. Cookie wurde gelöscht'), "2", "Authentifikation");
        }
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
        // Crypt only via Config. See Construktor
        if ($this->cookie_crypt) {
            $crypt= new AzDGCrypt(md5($this->cookie_crypt_pw));
            $cookie = $crypt->crypt($cookie);
        }
        return $cookie;
    }

  /**
   * Decrypt and unpack Cookiedata
   *
   * @param mixed Encryptet Cookiedata
   * @return mixed Decryptet Cookiedata as array
   * @access private
   */
    function cookiedata_unpack($cookie) {
        // Crypt only via Config. See Construktor
        if ($this->cookie_crypt) {
            $crypt= new AzDGCrypt(md5($this->cookie_crypt_pw));
            $cookie = $crypt->decrypt($cookie);
        }
        // TODO : Check Vars
        list ($this->cookie_data['userid'], 
              $this->cookie_data['uniqekey'], 
              $this->cookie_data['version'],
              $this->cookie_data['olduserid'],
              $this->cookie_data['sb_code']) = explode("|", $cookie);
    }


  /**
   * Generate simple Randomkey
   * @param integer How many Chars to generate
   * @return mixed Generated Randomkey
   * @access private
   */
    function gen_rnd_key($count) {
        $possible = '0123456789abcdefghijklmnopqrstuvwxyz';
        $key = '';
        for ($i = 0; $i < $count; $i++) $key .= substr($possible, mt_rand(0, strlen($possible) - 1), 1);
        return $key;
    }
}

?>