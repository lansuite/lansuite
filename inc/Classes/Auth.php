<?php

namespace LanSuite;

use LanSuite\AzDGCrypt;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class auth
 *
 * Authorisation and Cookie management for LanSuite
 *
 * @todo Change uniqkey from md5(password) to an extra Field
 */
class Auth
{

    /**
     * Userdata
     *
     * @var array
     */
    public $auth = [];

    /**
     * Time
     */
    private int $timestamp;

    /**
     * Cookie data
     */
    private array $cookie_data = [];

    /**
     * Cookie name
     */
    private string $cookie_name = 'LSAUTH';

    /**
     * Cookie version
     */
    private string $cookie_version = '1';

    /**
     * Domain
     */
    private string $cookie_domain = '';

    /**
     * Duration in days
     */
    private string $cookie_time = '30';

    /**
     * Cookie path
     */
    private string $cookie_path = '';

    /**
     * Crypt Cookie with AzDGCrypt
     */
    private bool $cookie_crypt = true;

    /**
     * Passphrase for AzDGCrypt
     */
    private string $cookie_crypt_pw = "iD9ww32e";

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
     * Request object
     */
    private Request $request;

    /**
     * Database
     */
    private Database $database;

    /**
     * auth constructor.
     * @param string $frmwrkmode Frameworkmode for switch Stats
     */
    public function __construct(Database $database, Request $request, $frmwrkmode = '')
    {
        $this->database = $database;
        $this->request = $request;

        // Setting default values
        $this->auth = $this->getAuthenticationDefaultValues();
        $this->timestamp = time();

        // Update statistics
        $this->update_visits($frmwrkmode);

        $last10Minutes = $this->timestamp - 60*10;
        $lastMinute = $this->timestamp - 60*1;

        $rows = $this->database->queryWithFullResult(
            'SELECT
                `userid`,
                SUM(IF(`lasthit` > ?, 1, 0)) AS `online`
            FROM %prefix%stats_auth
            WHERE
                login = "1"
                AND (
                    lasthit > ?
                    OR lastajaxhit > ?
                )
                AND userid > 0
            GROUP BY userid',
            [$last10Minutes, $last10Minutes, $lastMinute]
        );
        foreach ($rows as $row) {
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
        $row = $this->database->queryWithOnlyFirstRow('SELECT 1 AS found FROM %prefix%stats_auth WHERE lasthit < ?', [ceil((time() - $oneHour) / $oneHour) * $oneHour]);
        if ($row) {
            $this->database->query('DELETE FROM %prefix%stats_auth WHERE lasthit < ?', [ceil((time() - $oneHour) / $oneHour) * $oneHour]);
            $this->database->query('OPTIMIZE TABLE %prefix%stats_auth');

            // Delete cookie after 30 days
            // (TODO: Maybe make this time a config option)
            // (TODO: Maybe differ time for admins and non-admins)
            $row = $this->database->queryWithOnlyFirstRow('SELECT 1 AS found FROM %prefix%cookie WHERE lastchange < ?', [ceil((time() - $thirtyDays) / $oneHour) * $oneHour]);
            if ($row) {
                $this->database->query('DELETE FROM %prefix%cookie WHERE lastchange < ?', [ceil((time() - $thirtyDays) / $oneHour) * $oneHour]);
                $this->database->query('OPTIMIZE TABLE %prefix%cookie');
            }
        }
    }

    /**
     * Default values for global variable $auth
     */
    private function getAuthenticationDefaultValues(): array {
        return [
            'sessid' => session_id(),
            'ip' => $_SERVER['REMOTE_ADDR'],

            'login' => LS_AUTH_LOGIN_LOGGED_OUT,
            'type' => LS_AUTH_TYPE_ANONYMOUS,

            // In theory, all fields of the user database table should be present.
            // See loadAuthBySID()
            'userid' => 0,
            'email' => '',
            'username' => '',
            'userpassword' => '',
            'group_id' => 0,
            'design' => 'simple', // TODO Get design from default configuration
            'clanid' => 0,
        ];
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
        global $func, $cfg, $party;

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
            $cookierow = $this->database->queryWithOnlyFirstRow('SELECT userid from %prefix%cookie WHERE cookieid = ? AND password = ?', [$tmp_login_email, $tmp_login_pass]);
            if ($cookierow) {
                $user = $this->database->queryWithOnlyFirstRow(
                    'SELECT *, 1 AS found, 0 AS user_login FROM %prefix%user WHERE userid = ?',
                    [$cookierow['userid']]
                );

            // Not found in cookie table, then check for manual login (either with email, oder userid)
            } else {
                $user = $this->database->queryWithOnlyFirstRow('
                    SELECT
                        *,
                        1 AS found,
                        1 AS user_login
                    FROM %prefix%user
                    WHERE (
                        (userid = ? AND 0 = ?)
                        OR LOWER(email) = ?
                    )',
                    [$tmp_login_email, $is_email, $tmp_login_email]
                );
            }

            if (!$user) {
                $user = [
                    'userid' => 0,
                    'found' => 0,
                    'user_login' => 0,
                ];
            }

            // Needs to be a seperate query; WHERE (p.party_id IS NULL OR p.party_id=%int%) does not work when 2 parties exist
            if ($func->isModActive('party')) {
                $partyID = 0;
                if ($party) {
                    $partyID = $party->party_id;
                }
                $party_query = $this->database->queryWithOnlyFirstRow('
                    SELECT
                        p.checkin AS checkin,
                        p.checkout AS checkout
                    FROM %prefix%party_user AS p
                    WHERE
                        p.party_id = ?
                        AND user_id = ?',
                    [$partyID, $user['userid']]);
            }

            // Count login errors
            $row = $this->database->queryWithOnlyFirstRow('
                SELECT COUNT(*) AS anz
                FROM %prefix%login_errors
                WHERE
                    userid = ?
                    AND (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(time) < 60)
                GROUP BY userid',
                [$user['userid']]);

            // Too many login trys?
            if (is_array($row) && $row['anz'] >= 5) {
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
            } elseif ($user["user_login"] and !PasswordHash::verify($password, $user["password"])) {
                ($cfg["sys_internet"])? $remindtext = t('Hast du dein Passwort vergessen?<br/><a href="./index.php?mod=usrmgr&action=pwrecover"/>Hier kannst du ein neues Passwort generieren</a>.') : $remindtext = t('Solltest du dein Passwort vergessen haben, wende dich bitte an die Organisation.');
                $func->information(t('Die von dir eingebenen Login-Daten sind fehlerhaft. Bitte überprüfe deine Eingaben.') . HTML_NEWLINE . HTML_NEWLINE . $remindtext, '', 1);
                $func->log_event(t('Login für %1 fehlgeschlagen (Passwort-Fehler).', $tmp_login_email), "2", "Authentifikation");
                $this->database->query('INSERT INTO %prefix%login_errors SET userid = ?, ip = INET6_ATON(?)', [$user['userid'], $_SERVER['REMOTE_ADDR']]);

            // Cookie login and no correct cookie supplied?
            } elseif (!$user["user_login"] and !$cookierow['userid']) {
                ($cfg["sys_internet"])? $remindtext = t('Hast du dein Passwort vergessen?<br/><a href="./index.php?mod=usrmgr&action=pwrecover"/>Hier kannst du ein neues Passwort generieren</a>.') : $remindtext = t('Solltest du dein Passwort vergessen haben, wende dich sich bitte an die Organisation.');
                $func->information(t('Deine Session ist abgelaufen und das bei dir gesetzte Cookie ist fehlerhaft. Leider konntest du damit nicht eingeloggt werden. Bitte logge dich erneut manuell ein'), '', 1);
                $func->log_event(t('Login für %1 fehlgeschlagen (Cookie-Fehler).', $tmp_login_email), "2", "Authentifikation");
                $this->database->query('INSERT INTO %prefix%login_errors SET userid = ?, ip = INET6_ATON(?)', [$user['userid'], $_SERVER['REMOTE_ADDR']]);
                $this->cookie_unset();

            // Not checked in?
            } elseif ($func->isModActive('party') && (is_array($party_query) && (!$party_query["checkin"] || $party_query["checkin"] == '0000-00-00 00:00:00')) && $user["type"] < 2 && !$cfg["sys_internet"]) {
                $func->information(t('Du bist nicht eingecheckt. Im Intranetmodus ist ein Einloggen nur möglich, wenn du eingecheckt bist.') .HTML_NEWLINE. t('Bitte melden dich bei der Organisation.'), '', 1);
                $func->log_event(t('Login für %1 fehlgeschlagen (Account nicht eingecheckt).', $tmp_login_email), "2", "Authentifikation");

            // Already checked out?
            } elseif ($func->isModActive('party') && is_array($party_query) && $party_query["checkout"] && $party_query["checkout"] != '0000-00-00 00:00:00' && $user["type"] < 2 && !$cfg["sys_internet"]) {
                $func->information(t('Du bist bereits ausgecheckt. Im Intranetmodus ist ein Einloggen nur möglich, wenn du eingecheckt bist.') .HTML_NEWLINE. t('Bitte melden dich bei der Organisation.'), '', 1);
                $func->log_event(t('Login für %1 fehlgeschlagen (Account ausgecheckt).', $tmp_login_email), "2", "Authentifikation");

            // Everything fine!
            } else {
                if ($user["user_login"] and PasswordHash::needsRehash($user["password"])) {
                    try {
                        $this->database->query('UPDATE %prefix%user SET password = ? WHERE userid = ?', array(PasswordHash::hash($password), $user["userid"]));
                        $func->information(t('Es wurde ein Sicherheitsupgrade von deinem Passwort durchgeführt.'), '', 1);
                    } catch (Exception $e) {
                        $func->error(t('Sicherheitsupgrade von deinem Passwort ist fehlgeschlagen!'));
                    }
                }
                // Set Logonstats
                $this->database->query('UPDATE %prefix%user SET logins = logins + 1, changedate = changedate, lastlogin = NOW() WHERE userid = ?', [$user['userid']]);

                // If not logged in by cookie, generete new cookie and store it
                if (!$cookierow) {
                    $this->set_cookie_pw($user['userid']);
                }

                if ($cfg["sys_logoffdoubleusers"]) {
                    $this->database->query('DELETE FROM %prefix%stats_auth WHERE userid = ?', [$user['userid']]);
                    $this->database->query('DELETE FROM %prefix%cookie WHERE userid = ? AND cookieid != ?', [$user['userid'], $this->cookie_data['userid']]);
                }

                // Set authdata
                $this->database->query('
                    REPLACE INTO %prefix%stats_auth
                    SET
                        sessid = ?,
                        userid = ?,
                        login = \'1\',
                        ip = INET6_ATON(?),
                        logtime = ?,
                        logintime = ?,
                        lasthit = ?',
                    [
                        $this->auth["sessid"],
                        $user["userid"],
                        $this->auth["ip"],
                        $this->timestamp,
                        $this->timestamp,
                        $this->timestamp
                    ]
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
                    $func->confirmation(t('Erfolgreich eingeloggt. Die Änderungen werden beim Laden der nächsten Seite wirksam.'), $auth_backlink, '', 'FORWARD');

                    // Show error logins
                    $msg = '';
                    $errorRows = $this->database->queryWithFullResult('
                        SELECT INET6_NTOA(ip) AS ip, time
                        FROM %prefix%login_errors
                        WHERE userid = ?', [$user['userid']]);
                    foreach ($errorRows as $row) {
                        $msg .= t('Am') .' '. $row['time'] .' von der IP: <a href="https://dnsquery.org/ipwhois/'. $row['ip'] .'" target="_blank">'. $row['ip'] .'</a>'. HTML_NEWLINE;
                    }
                    if ($msg != '') {
                        $func->information('<b>'. t('Fehlerhafte Logins') .'</b>'. HTML_NEWLINE .t('Es wurden fehlerhafte Logins seit deinem letzten erfolgreichen Login durchgeführt.'). HTML_NEWLINE . HTML_NEWLINE . $msg, NO_LINK, 1);
                    }
                    $this->database->query('DELETE FROM %prefix%login_errors WHERE userid = ?', [$user['userid']]);
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
    private function login_cookie($userid, $uniquekey)
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
        global $func;

        // Delete entry from SID
        $this->database->query('DELETE FROM %prefix%stats_auth WHERE sessid = ?', [$this->auth["sessid"]]);
        $this->auth['login'] = "0";

        // Reset Cookiedata
        $this->cookie_read();
        $cookieUserId = $this->cookie_data['userid'] ?? 0;
        $this->database->query('DELETE FROM %prefix%cookie WHERE userid = ? AND cookieid = ?', [$this->auth['userid'], $cookieUserId]);
        $this->cookie_unset();

        // Reset Sessiondata
        unset($this->auth);
        unset($_SESSION['auth']);
        $this->auth = $this->getAuthenticationDefaultValues();

        $func->confirmation(t('Du wurdest erfolgreich ausgeloggt. Vielen dank für deinen Besuch.'), "", 1, 'FORWARD');
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
        global $func;

        // Get target user type
        $target_user = $this->database->queryWithOnlyFirstRow('SELECT type FROM %prefix%user WHERE userid = ?', [$target_id]);

        // Only highlevel to lowerlevel
        if ($this->auth["type"] > $target_user["type"]) {
            $switchbackcode = $this->gen_rnd_key(24); // Generate switch back code

            // Save old user ID & write cookie
            $this->cookie_data['olduserid'] = $this->auth['userid'];
            $this->cookie_data['sb_code'] = $switchbackcode;
            $this->cookie_set();

            // Store switch back code in current (admin) user data
            $this->database->query('UPDATE %prefix%user SET switch_back = ? WHERE userid = ?', [md5($switchbackcode), $this->auth["userid"]]);
            // Link session ID to new user ID
            $this->database->query('UPDATE %prefix%stats_auth SET userid = ?, login=\'1\' WHERE sessid = ?', [$target_id, $this->auth["sessid"]]);

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
        global $func;

        // Make sure that Cookiedata is loaded
        $this->cookie_read();
        if ($this->cookie_data['olduserid'] > 0) {
            // Check switch back code
            $admin_user = $this->database->queryWithOnlyFirstRow('SELECT switch_back FROM %prefix%user WHERE userid = ?', [$this->cookie_data["olduserid"]]);
            if (md5($this->cookie_data['sb_code']) == $admin_user["switch_back"]) {
                // Link session ID to origin user ID
                $this->database->query('UPDATE %prefix%stats_auth SET userid = ?, login=\'1\' WHERE sessid = ?', [$this->cookie_data["olduserid"], $this->auth["sessid"]]);
                // Delete switch back code in admins user data
                $this->database->query('UPDATE %prefix%user SET switch_back = \'\' WHERE userid = ?', [$this->cookie_data['olduserid']]);

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
     * @return boolean
     */
    public function authorized($requirement)
    {
        global $func;

        switch ($requirement) {
            // Logged in
            case 1:
                if ($this->auth['login']) {
                    return true;
                } else {
                    $func->information('NO_LOGIN');
                }
                break;

            // Type is Admin, or Superadmin
            case 2:
                if ($this->auth['type'] > 1) {
                    return true;
                } elseif (!$this->auth['login']) {
                    $func->information('NO_LOGIN');
                } else {
                    $func->information('ACCESS_DENIED');
                }
                break;

            // Type is Superadmin
            case 3:
                if ($this->auth['type'] > 2) {
                    return true;
                } elseif (!$this->auth['login']) {
                    $func->information('NO_LOGIN');
                } else {
                    $func->information('ACCESS_DENIED');
                }
                break;

            // Type is User, or less
            case 4:
                if ($this->auth['type'] < 2) {
                    return true;
                } else {
                    $func->information('ACCESS_DENIED');
                }
                break;

            // Logged out
            case 5:
                if (!$this->auth['login']) {
                    return true;
                } else {
                    $func->information('ACCESS_DENIED');
                }
                break;

            default:
                return true;
        }

        return false;
    }

    /**
     * Returns the old Userid if one is set.
     *
     * @return int
     */
    public function get_olduserid()
    {
        return $this->cookie_data['olduserid'] ?? 0;
    }

    /**
     * Load all needed Auth-Data from DB and set to auth[]
     *
     * @return mixed
     */
    private function loadAuthBySID()
    {
        // Put all User-Data into $auth-Array
        // TODO Replace * with an expanded list of all fields of the user database table
        // TODO Check if really all fields are required
        $user_data = $this->database->queryWithOnlyFirstRow('
            SELECT
                1 AS `found`,
                `session`.`userid`,
                `session`.`login`,
                INET6_NTOA(`session`.`ip`) AS `ip`,
                `user`.*
            FROM
                `%prefix%stats_auth` AS `session`
                LEFT JOIN `%prefix%user` AS `user` ON `user`.`userid` = `session`.`userid`
            WHERE
                `session`.`sessid` = ?
            ORDER BY `session`.`lasthit`', [$this->auth["sessid"]]);

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
    private function update_visits($frmwrkmode = "")
    {
        if ($frmwrkmode != "ajax" && $frmwrkmode != "print" && $frmwrkmode != "popup" && $frmwrkmode != "base") {
            // Update visits, hits, IP and lasthit
            $visit_timeout = time() - 60*60;
            // If a session loaded no page for over one hour, this counts as a new visit
            $this->database->query('UPDATE %prefix%stats_auth SET visits = visits + 1 WHERE sessid = ? AND lasthit < ?', [$this->auth["sessid"], $visit_timeout]);
            // Update user-stats and lasthit, so the timeout is resetted
            $this->database->query('
                UPDATE %prefix%stats_auth
                SET
                    lasthit = ?,
                    hits = hits + 1,
                    ip = INET6_ATON(?),
                    lasthiturl = ?
                WHERE sessid = ?',
                [
                    $this->timestamp,
                    $this->auth["ip"],
                    substr($_SERVER['REQUEST_URI'],0,100),
                    $this->auth["sessid"]
                ]);
        }

        // Heartbeat
        if ($frmwrkmode == "ajax") {
            $this->database->query('UPDATE %prefix%stats_auth SET lastajaxhit = ?% WHERE sessid = ?', [$this->timestamp, $this->auth["sessid"]]);
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
        $password_cookie = $this->gen_rnd_key(40);
        $queryStatement = $this->database->query('INSERT INTO %prefix%cookie SET password = ?, userid = ?', [md5($password_cookie), $userid]);

        $this->cookie_data['userid'] = $this->database->getStatementInsertID($queryStatement);
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
    private function cookie_read()
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
    private function cookie_set()
    {
        setcookie(
            $this->cookie_name,
            $this->cookiedata_pack(),
            [
                'expires' => time() + 3600 * 24 * $this->cookie_time,
                'path' => $this->cookie_path,
                'domain' => $this->cookie_domain,
                'secure' => $this->request->isSecure(),
                'httponly' => true,
            ]
        );
    }

    /**
     * Delete user cookie
     *
     * @return void
     */
    private function cookie_unset()
    {
        setcookie(
            $this->cookie_name,
            '',
            [
                'expires' => time() + 1,
                'path' => $this->cookie_path,
                'domain' => $this->cookie_domain,
                'secure' => $this->request->isSecure(),
                'httponly' => true,
            ]
        );
    }

    /**
     * Pack and encrypt cookie data
     *
     * @return string
     */
    private function cookiedata_pack()
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
    private function cookiedata_unpack($cookie)
    {
        // Crypt only via Config. See Construktor
        if ($this->cookie_crypt) {
            $crypt= new AzDGCrypt(md5($this->cookie_crypt_pw));
            $cookie = $crypt->decrypt($cookie);
        }

        [$this->cookie_data['userid'], $this->cookie_data['uniqekey'], $this->cookie_data['version'], $this->cookie_data['olduserid'], $this->cookie_data['sb_code']] = explode("|", $cookie);
    }

    /**
     * Generate simple random key
     *
     * @param int $count
     * @return string
     */
    private function gen_rnd_key($count)
    {
        $possible = '0123456789abcdefghijklmnopqrstuvwxyz';
        $key = '';
        for ($i = 0; $i < $count; $i++) {
            $key .= substr($possible, random_int(0, strlen($possible) - 1), 1);
        }
        return $key;
    }
}
