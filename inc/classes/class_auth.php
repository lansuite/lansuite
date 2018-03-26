<?php

/**
 * Class auth
 *
 * Authorisation and Cookie management for LanSuite
 *
 * @todo Change uniqkey from md5(password) to an extra Field
 */
class auth {

    /**
     * Userdata
     *
     * @var array
     */
    public $auth = [];

    /**
     * Time
     *
     * @var int
     */
    public $timestamp;

    /**
     * Cookie data
     *
     * @var array
     */
    public $cookie_data = [];

    /**
     * Cookie name
     *
     * @var string
     */
    public $cookie_name = 'LSAUTH';

    /**
     * Cookie version
     *
     * @var string
     */
    public $cookie_version = '1';

    /**
     * Domain
     *
     * @var string
     */
    public $cookie_domain = '';

    /**
     * Duration in days
     *
     * @var string
     */
    public $cookie_time = '30';

    /**
     * Cookie path
     *
     * @var string
     */
    public $cookie_path = '';

    /**
     * Crypt Cookie with AzDGCrypt
     *
     * @var bool
     */
    public $cookie_crypt = true;

    /**
     * Passphrase for AzDGCrypt
     *
     * @var string
     */
    public $cookie_crypt_pw = "iD9ww32e";

    /**
     * Array containing all users, currently online
     *
     * @var array
     */
    public $online_users = [];

    /**
     * Array containing all users, currently online by ajax, but no hit last 10min
     *
     * @var array
     */
    public $away_users = [];

    /**
     * auth constructor.
     * @param string $frmwrkmode Frameworkmode for switch Stats
     */
    public function __construct($frmwrkmode = "") {
        global $db;

        $this->auth["sessid"] = session_id();
        $this->auth["ip"] = $_SERVER['REMOTE_ADDR'];
        $this->timestamp = time();

        // Update statistics
        $this->update_visits($frmwrkmode);

        $last10Minutes = $this->timestamp - 60*10;
        $lastMinute = $this->timestamp - 60*1;

        $res = $db->qry(
            'SELECT
                `userid`,
                SUM(IF(`lasthit` > %int%, 1, 0)) AS `online`
            FROM %prefix%stats_auth
            WHERE
                login = "1"
                AND (
                    lasthit > %int%
                    OR lastajaxhit > %int%
                )
                AND userid > 0
            GROUP BY userid',
            $last10Minutes,
            $last10Minutes,
            $lastMinute
        );

        while ($row = $db->fetch_array($res)) {
            // If at the same time a user is logged in twice or multiple times
            // (e.g. via different browsers)
            // the field `online` will be more than 1.
            // But we don't care at this point, because we only care _which_
            // user is logged in  and not how many times.
            // Even if the user is logged in with Chrome, is inactive for more then 10 minutes
            // and the AJAX heartbeat kicks in _and_ the same user is logged in with Safari
            // and there the user is active, it will count as an online user.
            if ($row['online'] > 0) {
                $this->online_users[] = $row['userid'];

            } else {
                $this->away_users[] = $row['userid'];
            }
        }

        // Close sessions older than 1-2 hours.
        // ceil(x / $oneHour) * $oneHour for making query the same for one hour and therefore cacheable by MySQL
        // Do check first, for SELECT is faster than DELETE
        $oneHour = 60 * 60;
        $thirtyDays = 60 * 60 * 24 * 30;
        $row = $db->qry_first('SELECT 1 AS found FROM %prefix%stats_auth WHERE lasthit < %int%', ceil((time() - $oneHour) / $oneHour) * $oneHour);
        if ($row['found']) {
            $db->qry_first('DELETE FROM %prefix%stats_auth WHERE lasthit < %int%', ceil((time() - $oneHour) / $oneHour) * $oneHour);
            $db->qry_first('OPTIMIZE TABLE %prefix%stats_auth');

            // Delete cookie after 30 days
            // (TODO: Maybe make this time a config option)
            // (TODO: Maybe differ time for admins and non-admins)
            $row = $db->qry_first('SELECT 1 AS found FROM %prefix%cookie WHERE lastchange < %int%', ceil((time() - $thirtyDays) / $oneHour) * $oneHour);
            if ($row['found']) {
                $db->qry_first('DELETE FROM %prefix%cookie WHERE lastchange < %int%', ceil((time() - $thirtyDays) / $oneHour) * $oneHour);
                $db->qry_first('OPTIMIZE TABLE %prefix%cookie');
            }
        }
    }

    /**
     * Check User logon via Session or Cookie.
     * Check some security options and set or delete logon if any problem is found.
     *
     * @return array
     */
    public function check_logon()
    {
        // Possible cases
        // 1. Logged out. No Session. No Cookie
        // 1.5 Session exists, but no cookie
        // 2. No session, but a cookie (session times out)
        // 3. Logged in with session and cookie
        // 4. Logged in with session and cookie and user switch

        $CookieStatus = $this->cookie_read();

        // Look for SessionID in DB and load auth-data
        // Not found? Then look for valid cookie
            // Found? Then try cookie login
            // Not Found?: Cookie invalide. But no message, for maybe the user don't likes to log in
        if (!$this->loadAuthBySID() and $CookieStatus == 1) {
            $this->login_cookie($this->cookie_data['userid'], $this->cookie_data['uniqekey']);
        }

        return $this->auth;
    }

    /**
     * Check and Login a User.
     *
     * @param string $email
     * @param string $password
     * @param int $show_confirmation
     * @return array
     */
    public function login($email, $password, $show_confirmation = 1)
    {
        global $db, $func, $cfg, $party;

        $tmp_login_email = "";
        $tmp_login_pass = "";

        if ($email != "") {
            $tmp_login_email = strtolower(htmlspecialchars(trim($email)));
        }

        if ($password != "") {
            $tmp_login_pass = md5($password);
        }

        if ($tmp_login_email == "") {
            $func->information(t('Bitte gib deine E-Mail-Adresse oder deine Lansuite-ID ein.'), '', 1);

        } elseif ($tmp_login_pass == "") {
            $func->information(t('Bitte gib dein Kennwort ein.'), '', 1);

        } else {
            $is_email = strstr($tmp_login_email, '@');
            if (!$is_email) {
                $is_email = 0;

            } else {
                $is_email = 1;
            }

            // Search in cookie table for id + pw
            $cookierow = $db->qry_first('SELECT userid from %prefix%cookie WHERE cookieid = %int% AND password = %string%', $tmp_login_email, $tmp_login_pass);
            if ($cookierow['userid']) {
                $user = $db->qry_first(
                    'SELECT *, 1 AS found FROM %prefix%user WHERE (userid = %int%)',
                    $cookierow['userid']
                );

            // Not found in cookie table, then check for manual login (either with email, oder userid)
            } else {
                $user = $db->qry_first(
                    'SELECT *, 1 AS found, 1 AS user_login FROM %prefix%user
              WHERE ((userid = %int% AND 0 = %int%) OR LOWER(email) = %string%)',
                    $tmp_login_email,
                    $is_email,
                    $tmp_login_email
                );
            }

            // Needs to be a seperate query; WHERE (p.party_id IS NULL OR p.party_id=%int%) does not work when 2 parties exist
            if ($func->isModActive('party')) {
                $party_query = $db->qry_first('SELECT p.checkin AS checkin, p.checkout AS checkout FROM %prefix%party_user AS p WHERE p.party_id=%int% AND user_id=%int%', $party->party_id, $user['userid']);
            }

            // Count login errors
            $row = $db->qry_first('SELECT COUNT(*) AS anz
               FROM %prefix%login_errors
               WHERE userid = %int% AND (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(time) < 60)
               GROUP BY userid', $user['userid']);

            // Too many login trys?
            if ($row['anz'] >= 5) {
                $func->information(t('Du hast in der letzten Minute bereits 5 mal dein Passwort falsch eingegeben. Bitte warte einen Moment, bevor du es erneut versuchen darfst'), '', 1);

            // Email not found?
            } elseif (!$user["found"]) {
                $func->information(t('Dieser Benutzer existiert nicht in unserer Datenbank. Bitte prüfe die eingegebene Email/ID'), '', 1);
                $func->log_event(t('Falsche Email angegeben (%1)', $tmp_login_email), '2', 'Authentifikation');

            // Account disabled?
            } elseif ($user["type"] <= -1) {
                $func->information(t('Dein Account ist gesperrt. Melde dich bitte bei der Organisation.'), '', 1);
                $func->log_event(t('Login für %1 fehlgeschlagen (Account gesperrt).', $tmp_login_email), "2", "Authentifikation");

            // Account locked?
            } elseif ($user['locked']) {
                $func->information(t('Dieser Account ist noch nicht freigeschaltet. Bitte warte bis ein Organisator dich freigeschaltet hat.'), '', 1);
                $func->log_event(t('Account von %1 ist noch gesperrt. Login daher fehlgeschlagen.', $tmp_login_email), "2", "Authentifikation");

            // Mail not verified?
            } elseif ($cfg['sys_login_verified_mail_only'] == 2 and !$user['email_verified'] and $user["type"] < 2) {
                $func->information(t('Du hast deine Email-Adresse (%1) noch nicht verifiziert. Bitte folge dem Link in der dir zugestellten Email.', $user['email']).' <a href="index.php?mod=usrmgr&action=verify_email&step=2&userid='. $user['userid'] .'">'. t('Klicke hier, um die Mail erneut zu versenden</a>'), '', 1);
                $func->log_event(t('Login fehlgeschlagen. Email (%1) nicht verifiziert', $user['email']), "2", "Authentifikation");

            // User login and wrong password?
            } elseif ($user["user_login"] and $tmp_login_pass != $user["password"]) {
                ($cfg["sys_internet"])? $remindtext = t('Hast du dein Passwort vergessen?<br/><a href="./index.php?mod=usrmgr&action=pwrecover"/>Hier kannst du ein neues Passwort generieren</a>.') : $remindtext = t('Solltest du dein Passwort vergessen haben, wende dich bitte an die Organisation.');
                $func->information(t('Die von dir eingebenen Login-Daten sind fehlerhaft. Bitte überprüfe deine Eingaben.') . HTML_NEWLINE . HTML_NEWLINE . $remindtext, '', 1);
                $func->log_event(t('Login für %1 fehlgeschlagen (Passwort-Fehler).', $tmp_login_email), "2", "Authentifikation");
                $db->qry('INSERT INTO %prefix%login_errors SET userid = %int%, ip = INET6_ATON(%string%)', $user['userid'], $_SERVER['REMOTE_ADDR']);

            // Cookie login and no correct cookie supplied?
            } elseif (!$user["user_login"] and !$cookierow['userid']) {
                ($cfg["sys_internet"])? $remindtext = t('Hast du dein Passwort vergessen?<br/><a href="./index.php?mod=usrmgr&action=pwrecover"/>Hier kannst du ein neues Passwort generieren</a>.') : $remindtext = t('Solltest du dein Passwort vergessen haben, wende dich sich bitte an die Organisation.');
                $func->information(t('Deine Session ist abgelaufen und das bei dir gesetzte Cookie ist fehlerhaft. Leider konntest du damit nicht eingeloggt werden. Bitte logge dich erneut manuell ein'), '', 1);
                $func->log_event(t('Login für %1 fehlgeschlagen (Cookie-Fehler).', $tmp_login_email), "2", "Authentifikation");
                $db->qry('INSERT INTO %prefix%login_errors SET userid = %int%, ip = INET6_ATON(%string%)', $user['userid'], $_SERVER['REMOTE_ADDR']);
                $this->cookie_unset();

            // Not checked in?
            } elseif ($func->isModActive('party') and (!$party_query["checkin"] or $party_query["checkin"] == '0000-00-00 00:00:00') and $user["type"] < 2 and !$cfg["sys_internet"]) {
                $func->information(t('Du bist nicht eingecheckt. Im Intranetmodus ist ein Einloggen nur möglich, wenn du eingecheckt bist.') .HTML_NEWLINE. t('Bitte melden dich bei der Organisation.'), '', 1);
                $func->log_event(t('Login für %1 fehlgeschlagen (Account nicht eingecheckt).', $tmp_login_email), "2", "Authentifikation");

            // Already checked out?
            } elseif ($func->isModActive('party') and $party_query["checkout"] and $party_query["checkout"] != '0000-00-00 00:00:00' and $user["type"] < 2 and !$cfg["sys_internet"]) {
                $func->information(t('Du bist bereits ausgecheckt. Im Intranetmodus ist ein Einloggen nur möglich, wenn du eingecheckt bist.') .HTML_NEWLINE. t('Bitte melden dich bei der Organisation.'), '', 1);
                $func->log_event(t('Login für %1 fehlgeschlagen (Account ausgecheckt).', $tmp_login_email), "2", "Authentifikation");

            // Everything fine!
            } else {
                // Set Logonstats
                $db->qry('UPDATE %prefix%user SET logins = logins + 1, changedate = changedate, lastlogin = NOW() WHERE userid = %int%', $user['userid']);

                // If not logged in by cookie, generete new cookie and store it
                if (!$cookierow['userid']) {
                    $this->set_cookie_pw($user['userid']);
                }

                if ($cfg["sys_logoffdoubleusers"]) {
                    $db->qry('DELETE FROM %prefix%stats_auth WHERE userid = %int%', $user['userid']);
                    $db->qry('DELETE FROM %prefix%cookie WHERE userid = %int% AND cookieid != %int%', $user['userid'], $this->cookie_data['userid']);
                }

                // Set authdata
                $db->qry(
                    'REPLACE INTO %prefix%stats_auth
                  SET sessid = %string%, userid = %int%, login = \'1\', ip = %string%, logtime = %string%, logintime = %string%, lasthit = %string%',
                    $this->auth["sessid"],
                    $user["userid"],
                    $this->auth["ip"],
                    $this->timestamp,
                    $this->timestamp,
                    $this->timestamp
                );

                $this->loadAuthBySID();
                $this->auth['userid'] = $user['userid'];
                if (!in_array($user['userid'], $this->online_users)) {
                    $this->online_users[] = $user['userid'];
                }

                if ($show_confirmation) {
                    // Print Loginmessages
                    if ($_GET['mod']=='auth' and $_GET['action'] == 'login') {
                        $auth_backlink = "index.php?mod=home";
                    } else {
                        $auth_backlink = "";
                    }
                    $func->confirmation(t('Erfolgreich eingeloggt. Die Änderungen werden beim laden der nächsten Seite wirksam.'), $auth_backlink, '', 'FORWARD');

                    // Show error logins
                    $msg = '';
                    $res = $db->qry('SELECT INET6_NTOA(ip) AS ip, time
                                   FROM %prefix%login_errors
                                   WHERE userid = %int%', $user['userid']);
                    while ($row = $db->fetch_array($res)) {
                        $msg .= t('Am') .' '. $row['time'] .' von der IP: <a href="http://www.dnsstuff.com/tools/whois.ch?ip='. $row['ip'] .'" target="_blank">'. $row['ip'] .'</a>'. HTML_NEWLINE;
                    }
                    $db->free_result($res);
                    if ($msg != '') {
                        $func->information('<b>'. t('Fehlerhafte Logins') .'</b>'. HTML_NEWLINE .t('Es wurden fehlerhafte Logins seit deinem letzten erfolgreichen Login durchgeführt.'). HTML_NEWLINE . HTML_NEWLINE . $msg, NO_LINK, 1);
                    }
                    $db->qry('DELETE FROM %prefix%login_errors WHERE userid = %int%', $user['userid']);
                }
            }
        }
        return $this->auth;
    }

    /**
     * Login user via cookie e.g. if session is expired
     *
     * @param string $userid
     * @param string $uniquekey
     * @return void
     */
    public function login_cookie($userid, $uniquekey)
    {
        global $func;

        if ($userid == "") {
            $func->information(t('Keine Userid beim Login via Cookie erkannt.'), '', 1);

        } elseif ($uniquekey == "") {
            $func->information(t('Kein Uniquekey beim Login via Cookie erkannt.'), '', 1);

        } else {
            $this->login($userid, $uniquekey, 0);
        }
    }

    /**
     * Logout the user and delete Session data, cookie and authdata
     *
     * @return array
     */
    public function logout()
    {
        global $db, $func;

        // Delete entry from SID
        $db->qry('DELETE FROM %prefix%stats_auth WHERE sessid=%string%', $this->auth["sessid"]);
        $this->auth['login'] = "0";

        // Reset Cookiedata
        $this->cookie_read();
        $db->qry('DELETE FROM %prefix%cookie WHERE userid = %int% AND cookieid = %int%', $this->auth['userid'], $this->cookie_data['userid']);
        $this->cookie_unset();

        // Reset Sessiondata
        unset($this->auth);
        unset($_SESSION['auth']);
        $this->auth['login'] == "0";
        $this->auth["userid"] = "";
        $this->auth["email"] = "";
        $this->auth["username"] = "";
        $this->auth["userpassword"] = "";
        $this->auth["type"] = 0;

        $func->confirmation(t('Du wurdest erfolgreich ausgeloggt. Vielen dank für deinen Besuch.'), "", 1, FORWARD);
        return $this->auth;
    }

    /**
     * Switch to UserID
     * Switches to given UserID and stores a callback function in a Cookie and DB
     *
     * @param $target_id
     * @return void
     */
    public function switchto($target_id)
    {
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

            $func->confirmation(t('Benutzerwechsel erfolgreich. Die &Auml;nderungen werden beim laden der nächsten Seite wirksam.'), '', 1);  //FIX meldungen auserhalb/standart?!?

        } else {
            $func->error(t('Dein Benutzerlevel ist geringer, als das des Ziel-Benutzers. Ein Wechsel ist daher untersagt'), '', 1); //FIX meldungen auserhalb/standart?!
        }
    }

    /**
     * Switch back to Adminuser
     * Logout from the selected User and go back to the calling Adminuser
     *
     * @return void
     */
    public function switchback()
    {
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

                $func->confirmation(t('Benutzerwechsel erfolgreich. Die Änderungen werden beim laden der nächsten Seite wirksam.'), '', 1);

            } else {
                $func->information(t('Fehler: Falscher switch back code! Das kann daran liegen, dass dein Browser keine Cookies unterstützt.'), '', 1);
            }

        } else {
            $func->information(t('Fehler: Keine Switchbackdaten gefunden! Das kann daran liegen, dass dein Browser keine Cookies unterstützt.'), '', 1);
        }
    }

    /**
     * Check user rights and add a error message if needed
     *
     * @param int $requirement
     * @return int
     */
    public function authorized($requirement)
    {
        global $func;

        switch ($requirement) {
            // Logged in
            case 1:
                if ($this->auth['login']) {
                    return 1;

                } else {
                    $func->information('NO_LOGIN');
                }
                break;

            // Type is Admin, or Superadmin
            case 2:
                if ($this->auth['type'] > 1) {
                    return 1;

                } elseif (!$this->auth['login']) {
                    $func->information('NO_LOGIN');

                } else {
                    $func->information('ACCESS_DENIED');
                }
                break;

            // Type is Superadmin
            case 3:
                if ($this->auth['type'] > 2) {
                    return 1;

                } elseif (!$this->auth['login']) {
                    $func->information('NO_LOGIN');

                } else {
                    $func->information('ACCESS_DENIED');
                }
                break;

            // Type is User, or less
            case 4:
                if ($this->auth['type'] < 2) {
                    return 1;

                } else {
                    $func->information('ACCESS_DENIED');
                }
                break;

            // Logged out
            case 5:
                if (!$this->auth['login']) {
                    return 1;

                } else {
                    $func->information('ACCESS_DENIED');
                }
                break;

            default:
                return 1;
        }
    }

    /**
     * Returns the old Userid if one is set.
     *
     * @return int
     */
    public function get_olduserid()
    {
        return $this->cookie_data['olduserid'];
    }

    /**
     * Load all needed Auth-Data from DB and set to auth[]
     *
     * @return mixed
     */
    public function loadAuthBySID()
    {
        global $db;
        // Put all User-Data into $auth-Array
        $user_data = $db->qry_first('SELECT 1 AS found, session.userid, session.login, session.ip, user.*
            FROM %prefix%stats_auth AS session
            LEFT JOIN %prefix%user AS user ON user.userid = session.userid
            WHERE session.sessid=%string% ORDER BY session.lasthit', $this->auth["sessid"]);
        if (is_array($user_data)) {
            foreach ($user_data as $key => $val) {
                if (!is_numeric($key)) {
                    $this->auth[$key] = $val;
                }
            }
        }
        return $this->auth['login'];
    }

    /**
     * Update visit data in stats_auth Table
     *
     * @param string $frmwrkmode
     * @return void
     */
    public function update_visits($frmwrkmode = "")
    {
        global $db;
        if ($frmwrkmode != "ajax" && $frmwrkmode != "print" && $frmwrkmode != "popup" && $frmwrkmode != "base") {
            // Update visits, hits, IP and lasthit
            $visit_timeout = time() - 60*60;
            // If a session loaded no page for over one hour, this counts as a new visit
            $db->qry('UPDATE %prefix%stats_auth SET visits = visits + 1 WHERE (sessid=%string%) AND (lasthit < %int%)', $this->auth["sessid"], $visit_timeout);
            // Update user-stats and lasthit, so the timeout is resetted
            $db->qry('UPDATE %prefix%stats_auth SET lasthit=%int%, hits = hits + 1, ip=%string%, lasthiturl= %string% WHERE sessid=%string%', $this->timestamp, $this->auth["ip"], $_SERVER['REQUEST_URI'], $this->auth["sessid"]);
        }

        // Heartbeat
        if ($frmwrkmode == "ajax") {
            $db->qry('UPDATE %prefix%stats_auth SET lastajaxhit=%int% WHERE sessid=%string%', $this->timestamp, $this->auth["sessid"]);
        }
    }

    /**
     * Generate a new CookiePW and set it (in DB + Cookie)
     *
     * @param int $userid
     * @return void
     */
    public function set_cookie_pw($userid)
    {
        global $db;

        $password_cookie = $this->gen_rnd_key(40);
        $db->qry('INSERT INTO %prefix%cookie SET password = %string%, userid = %int%', md5($password_cookie), $userid);

        $this->cookie_data['userid'] = $db->insert_id();
        $this->cookie_data['uniqekey'] = $password_cookie;
        $this->cookie_data['version'] = $this->cookie_version;
        $this->cookie_data['olduserid'] = "";
        $this->cookie_data['sb_code'] = "";
        $this->cookie_set();
    }

    /**
     * Read and check user cookie
     *
     * @return int Return the Cookiestatus. 1=OK, 0=NOK
     */
    public function cookie_read()
    {
        $ok = 0;

        // Check for Cookie
        if (array_key_exists($this->cookie_name, $_COOKIE)) {
            $this->cookiedata_unpack($_COOKIE[$this->cookie_name]);

            // Look for correkt cookieformat
            if (is_numeric($this->cookie_data['userid']) and
                is_string($this->cookie_data['uniqekey']) and
                is_numeric($this->cookie_data['version']) and
                ($this->cookie_version == $this->cookie_data['version'])) {
                $ok = 1;
            }
        }

        return $ok;
    }

    /**
     * Set cookie for user
     *
     * @return void
     */
    public function cookie_set()
    {
        setcookie(
            $this->cookie_name,
            $this->cookiedata_pack(),
            time()+3600*24*$this->cookie_time,
            $this->cookie_path,
            $this->cookie_domain
        );
    }

    /**
     * Delete user cookie
     *
     * @return void
     */
    public function cookie_unset()
    {
        setcookie(
            $this->cookie_name,
            '',
            time()+1,
            $this->cookie_path,
            $this->cookie_domain
        );
    }

    /**
     * Pack and encrypt cookie data
     *
     * @return string
     */
    public function cookiedata_pack()
    {
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
     * Decrypt and unpack cookie data
     *
     * @param string $cookie
     * @return void
     */
    public function cookiedata_unpack($cookie)
    {
        // Crypt only via Config. See Construktor
        if ($this->cookie_crypt) {
            $crypt= new AzDGCrypt(md5($this->cookie_crypt_pw));
            $cookie = $crypt->decrypt($cookie);
        }

        list($this->cookie_data['userid'],
              $this->cookie_data['uniqekey'],
              $this->cookie_data['version'],
              $this->cookie_data['olduserid'],
              $this->cookie_data['sb_code']) = explode("|", $cookie);
    }

    /**
     * Generate simple random key
     *
     * @param int $count
     * @return string
     */
    public function gen_rnd_key($count)
    {
        $possible = '0123456789abcdefghijklmnopqrstuvwxyz';
        $key = '';
        for ($i = 0; $i < $count; $i++) {
            $key .= substr($possible, mt_rand(0, strlen($possible) - 1), 1);
        }
        return $key;
    }
}
