<?php
### Set Error Reporting & INI-Settings

    if (defined('E_STRICT')) {
        error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);
    } // Will work for PHP >= 5.3
    elseif (defined('E_DEPRECATED')) {
        error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
    } // Will work for PHP >= 5.3
    else {
        error_reporting(E_ALL ^ E_NOTICE);
    } // For PHP < 5.3
    if (function_exists('date_default_timezone_set')) {
        date_default_timezone_set('Europe/Berlin');
    } // As of PHP 5.3 this needs to be set. Otherwise some webservers will throw warnings
    if (function_exists('ini_set')) {
        #ini_set('display_errors', 0);
      #ini_set('log_errors', 1);
      #ini_set('error_log', 'log/php/');
      // Disable SID in URL
      ini_set('url_rewriter.tags', '');
    }

    function myErrorHandler($errno, $errstr, $errfile, $errline)
    {
        global $PHPErrors, $PHPErrorsFound, $db, $auth;

      // Only show errors, which sould be reported according to error_reporting
      // Also filters @ (for @ will have error_reporting "0")
      $rep = ini_get('error_reporting');
        if (!($rep & $errno)) {
            return false;
        }

      // Does the same as above for PHP7
      if (error_reporting() == 0) {
          return false;
      }


      // error_reporting setting currently doesn't show the following errors:
      // E_NOTICE
      // E_DEPRECATED
      // E_USER_NOTICE
      // E_USER_DEPRECATED
      // Should change in the future!

      switch ($errno) {
        case E_ERROR:               $errors = "Error";                  break; // not catched
        case E_WARNING:             $errors = "Warning";                break;
        case E_PARSE:               $errors = "Parse Error";            break; // not catched
        case E_NOTICE:              $errors = "Notice";                 break;
        case E_CORE_ERROR:          $errors = "Core Error";             break; // not catched
        case E_CORE_WARNING:        $errors = "Core Warning";           break; // not catched
        case E_COMPILE_ERROR:       $errors = "Compile Error";          break; // not catched
        case E_COMPILE_WARNING:     $errors = "Compile Warning";        break; // not catched
        case E_USER_ERROR:          $errors = "User Error";             break;
        case E_USER_WARNING:        $errors = "User Warning";           break;
        case E_USER_NOTICE:         $errors = "User Notice";            break;
        case E_STRICT:              $errors = "Strict Notice";          break; // catched only outside this file
        case E_RECOVERABLE_ERROR:   $errors = "Recoverable Error";      break;
        default:
          if (defined('E_DEPRECATED') and $errno == E_DEPRECATED) {
              $errors = "Deprecated";
          } elseif (defined('E_USER_DEPRECATED') and $errno == E_USER_DEPRECATED) {
              $errors = "User Deprecated";
          } else {
              $errors = "Unknown error ($errno)";
          }
        break;
      }

      // Store error, to print it later
      #$err = '<b>'. $errors .'</b>: '. $errstr .' in <b>'. $errfile .'</b> on line <b>'. $errline .'</b><br /><br />';
      $err = sprintf("PHP %s: %s in %s on line %d", $errors, $errstr, $errfile, $errline);

      // Write error to log file
      if (ini_get('log_errors')) {
          error_log($err);
      }

      // Write to $PHPError for onscreen output later
      $PHPErrors .= $err .'<br />';
        $PHPErrorsFound = 1;

      // Write to DB-Log
      // Attention: Be aware of loops!
      if (isset($db) and $db->success) {
          $db->qry('INSERT INTO %prefix%log
        SET date = NOW(), userid = %int%, type = 3, description = %string%, sort_tag = "PHP-Fehler"',
        (int)$auth['userid'], $err);
      }

        return true;
    }

    $PHPErrorsFound = 0;
    $PHPErrors = '';
    set_error_handler("myErrorHandler");

### Start session-management

    #session_save_path('ext_inc/session'); Leave to hosters default value, for some don't seam to empty it and data here counts against web space quota
    session_start();

### Initialise Frameworkclass for Basic output

    include_once("inc/classes/class_framework.php");
    $framework = new framework();
    $framework->fullscreen($_GET['fullscreen']); // Switch fullscreen via GET

    // Notlösung... design als base und popup sollen ganz verschwinden
    if ($_GET['design']=='base' or $_GET['design']=='popup' or $_GET['design']=='ajax' or $_GET['design']=='print' or $_GET['design']=='beamer') {
        $frmwrkmode = $_GET['design'];
    } // Set Popupmode via GET (base, popup)
    if ($_GET['frmwrkmode']) {
        $frmwrkmode = $_GET['frmwrkmode'];
    } // Set Popupmode via GET (base, popup)
    if (isset($frmwrkmode)) {
        $framework->set_modus($frmwrkmode);
    }
    // Ende Notlösung

### Set HTTP-Headers

    header('Content-Type: text/html; charset=utf-8');
    #header('Content-Type: application/xhtml+xml; charset=utf-8');
    #header("Cache-Control: no-cache, must-revalidate");

    include_once("ext_scripts/mobile_device_detect.php");
    $framework->IsMobileBrowser = mobile_device_detect();

    // For XHTML compatibility
    @ini_set('arg_separator.output', '&amp;');

### load $_POST and $_GET variables

    // Fallback for PHP < 4.1 (still needed?)
    if (!is_array($_POST)) {
        $_POST = $HTTP_POST_VARS;
    }
    if (!is_array($_GET)) {
        $_GET = $HTTP_GET_VARS;
    }
    if (!is_array($_COOKIE)) {
        $_COOKIE = $HTTP_COOKIE_VARS;
    }

    // Base Functions (anything that doesnt belong elsewere)
    require_once("inc/classes/class_func.php");
    $func        = new func;

    // Prevent XSS
    foreach ($_GET as $key => $val) {
        if (!is_array($_GET[$key])) {
            $_GET[$key] = $func->NoHTML($_GET[$key], 1);
        } else {
            foreach ($_GET[$key] as $key2 => $val2) {
                if (!is_array($_GET[$key][$key2])) {
                    $_GET[$key][$key2] = $func->NoHTML($_GET[$key][$key2], 1);
                } else {
                    foreach ($_GET[$key][$key2] as $key3 => $val3) {
                        $_GET[$key][$key2][$key3] = $func->NoHTML($_GET[$key][$key2][$key3], 1);
                    }
                }
            }
        }
    }
    $_SERVER['REQUEST_URI'] = $func->NoHTML($_SERVER['REQUEST_URI'], 1);
    $_SERVER['HTTP_REFERER'] = $func->NoHTML($_SERVER['HTTP_REFERER'], 1);
    $_SERVER['QUERY_STRING'] = $func->NoHTML($_SERVER['QUERY_STRING'], 1);

    // Save original Array
    if (get_magic_quotes_gpc()) {
        foreach ($_GET as $key => $val) {
            if (!is_array($_GET[$key])) {
                $__GET[$key] = stripslashes($_GET[$key]);
            }
        }
        foreach ($_POST as $key => $val) {
            if (!is_array($_POST[$key])) {
                $__POST[$key] = stripslashes($_POST[$key]);
            }
        }
        foreach ($_COOKIE as $key => $val) {
            if (!is_array($_COOKIE[$key])) {
                $__COOKIE[$key] = stripslashes($_COOKIE[$key]);
            }
        }
    } else {
        $__GET = $_GET;
        $__POST = $_POST;
        $__COOKIE = $_COOKIE;
    }

    // Emulate MQ, if disabled
    if (!get_magic_quotes_gpc()) {   // and !get_magic_quotes_runtime()
        foreach ($_GET as $key => $val) {
            if (!is_array($_GET[$key])) {
                $_GET[$key] = addslashes($_GET[$key]);
            }
        }
        foreach ($_POST as $key => $val) {
            if (!is_array($_POST[$key])) {
                $_POST[$key] = addslashes($_POST[$key]);
            }
        }
        foreach ($_COOKIE as $key => $val) {
            if (!is_array($_COOKIE[$key])) {
                $_COOKIE[$key] = addslashes($_COOKIE[$key]);
            }
        }
    }

    // Protect from XSS
    #foreach ($_GET as $key => $val) $_GET[$key] = preg_replace('#&lt;script(.)*>#sUi', '', $_GET[$key]);
    #foreach ($_POST as $key => $val) $_POST[$key] = preg_replace('#&lt;script(.)*>#sUi', '', $_POST[$key]);

### Read Config and Definitionfiles

    // Load Basic Config
    if (file_exists('inc/base/config.php')) {
        $config = parse_ini_file('inc/base/config.php', 1);

    // Default config. Will be used only until the wizard has created the config file
    } else {
        $config = array();

        $config['lansuite']['version'] = 'Nightly';
        $config['lansuite']['default_design'] = 'simple';
        $config['lansuite']['chmod_dir'] = '777';
        $config['lansuite']['chmod_file'] = '666';
        $config['lansuite']['debugmode'] = '0';

        $config['database']['server'] = 'localhost';
        $config['database']['user'] = 'root';
        $config['database']['passwd'] = '';
        $config['database']['database'] = 'lansuite';
        $config['database']['prefix'] = 'ls_';
        $config['database']['charset'] = 'utf8';

        $config['environment']['configured'] = 0;
    }

    include_once('inc/base/define.php');                // Read definition file


### Include and Initialize base classes

    $lang = array(); // For old $lang

    if ($config['lansuite']['debugmode'] > 0) {
        include_once "inc/classes/class_debug.php";       // Debug initialisieren
      $debug = new debug($config['lansuite']['debugmode']);
    }

    include_once("inc/classes/class_translation.php");  // Load Translationclass. No t()-Function before this point!
    $translation = new translation();

    include_once('ext_scripts/smarty/Smarty.class.php');
    $smarty = new Smarty();
    $smarty->template_dir = '.';
    $smarty->compile_dir = './ext_inc/templates_c/';
    $smarty->cache_dir = './ext_inc/templates_cache/';
    $smarty->caching = false;
    $smarty->cache_lifetime = 0; // sec
    #$smarty->compile_check = 0;

    if (isset($debug)) {
        $debug->tracker("Include and Init Smarty");
    }

    include_once("inc/classes/class_display.php");      // Display Functions (to load the lansuite-templates)
    $dsp = new display();

    include_once("inc/classes/class_db_mysql.php");     // DB Functions (to work with the databse)
    $db = new db;

    include_once("inc/classes/class_sec.php");          // Security Functions (to lock pages)
    $sec = new sec;

    if (isset($debug)) {
        $debug->tracker("Include and Init Base Classes");
    }

### Initalize Basic Parameters

    $language = $translation->get_lang(); // Set and Read Systemlanguage
    $smarty->assign('language', $language);

### Installingsystem or normal auth

    if ($config['environment']['configured'] == 0) {
        $translation->load_trans('xml', 'install'); // Filemode on Installation
        ### Prepare install

        // Force installwizard if LS not configured
        $_GET['mod']    = 'install';
        $_GET['action'] = 'wizard';

        // Silent connect
        $db->connect(1);
        $IsAboutToInstall = 1;

        // Force Adminrights for installing User
        $auth["type"]  = 3;
        $auth["login"] = 1;

        // Load DB-Data after installwizard step 3
        if ($_GET["action"] == "wizard" and $_GET["step"] > 3) {
            $cfg = $func->read_db_config();  // read Configtable
        }
    } else {
        ### Normal auth cycle and Database-init

        $db->connect(0);
        $IsAboutToInstall = 0;

        $translation->load_trans('db', $_GET['mod']); // DB-Mode on Running System
        // FIX : Add function to scan DB for correkt config and Tables (prefix etc.)

        // Reset DB-Success in Setup if no Adm.-Account was found, because a connection could work, but prefix is wrong
        if (!$func->admin_exists() and (($_GET["action"] == "wizard" and $_GET["step"] <= 3) or ($_GET["action"] == "ls_conf"))) {
            $db->success = 0;
        }

        $cfg = $func->read_db_config(); // Config-Tabelle aulesen
        $sec->check_blacklist();

        // Set timezone info (php + mysql)
        if ($cfg['sys_timezone'] and function_exists('date_default_timezone_set')) {
            #date_default_timezone_set($cfg['sys_timezone']);
          #$db->qry('SET SESSION time_zone = %string%', $cfg['sys_timezone']);
          ##$db->qry('SET SESSION time_zone = \'+0:00\'');
        }

        if (!$_GET['mod']) {
            $_GET['mod'] = 'home';
        }
        $func->getActiveModules();

        $framework->AddToPageTitle($cfg['sys_page_title']);
        if ($func->isModActive($_GET['mod'], $caption) && $_GET['mod'] != 'home') {
            $framework->AddToPageTitle($caption);
        }

        ### Start autentication, just if LS is working
        include_once("inc/classes/class_auth.php");
        $authentication = new auth($frmwrkmode);
        $auth      = $authentication->check_logon();    // Testet Cookie / Session ob User eingeloggt ist
        $olduserid = $authentication->get_olduserid();  // Olduserid for Switback on Boxes
    }

    // Initialize party
    // Needed also, when not configured for LanSurfer Import
    if ($func->isModActive('party')) {
        include_once("modules/party/class_party.php");
        $party = new party();
    } else { // If without party-module: just give a fake ID, for many modules need it
      class party
      {
          public $party_id;
      }
        $party = new party();
        $party->party_id = (int)$cfg['signon_partyid'];
    }

    if ($config['environment']['configured'] != 0) {
        if ($_GET['mod']=='auth') {
            switch ($_GET['action']) {
                case 'login':
                   $auth = $authentication->login($_POST['email'], $_POST['password']);
                break;
                case 'logout':
                    $auth = $authentication->logout();
                    $_GET['mod']='home';
                break;
                case 'switch_to': // Switch to user
                    $authentication->switchto($_GET["userid"]);
                break;
                case 'switch_back': // Switch back to Adminuser
                    $authentication->switchback();
                break;
            }
        }
    }

### Set Default-Design, if non is set

    /*
     * Initializes the design of lansuite.
     */
    function initializeDesign()
    {
        global $cfg, $auth, $config, $_SESSION, $_GET, $smarty;

      // If user is not allowed to use an own selected design, or none is selected, use default
      if (!$cfg['user_design_change'] or !$auth['design']) {
          $auth['design'] = $config['lansuite']['default_design'];
      }

      // Design switch by URL
      if ($_GET['design'] and $_GET['design'] != 'popup' and $_GET['design'] != 'base') {
          $auth['design'] = $_GET['design'];
      }

      // Fallback design is 'simple'
      if (!$auth['design'] or !file_exists('design/'. $auth['design'] .'/templates/main.htm')) {
          $auth['design'] = 'simple';
          if ($_GET['design'] != 'popup' and $_GET['design'] != 'base') {
              $_GET['design'] = 'simple';
          }
      }

      // For compaibility with old LS code
      $_SESSION['auth']['design'] = $auth['design'];

      // Assign
      $smarty->assign('default_design', $auth['design']);
    }
    initializeDesign();

### Load Rotation Banner

    if ($_GET['design'] != 'popup'
      and $_GET['action'] != 'wizard'
      and !$_SESSION['lansuite']['fullscreen']
      and $db->success
      and $func->isModActive('sponsor')
    ) {
        include_once("modules/sponsor/banner.php");
    }

### Create Boxes / load Boxmanager

    if (!$IsAboutToInstall and $_GET['design'] != 'base') {
        include_once("modules/boxes/boxes.php");
    }

### index_module.inc.php load the Modulactions and Codes

    $db->DisplayErrors();
    if ($PHPErrors) {
        $func->error($PHPErrors);
    }
    $PHPErrors = '';

    #$func->error($func->FormatFileSize(memory_get_usage()));
    #trigger_error(memory_get_usage(), E_USER_ERROR);
    include_once('index_module.inc.php');

### Complete Framework and Output HTML

    $framework->set_design($auth['design']);

    $db->DisplayErrors();
    if ($PHPErrors) {
        $func->error($PHPErrors);
    }
    $PHPErrors = '';

    $framework->add_content($FrameworkMessages);    // Add old Frameworkmessages (sollten dann ausgetauscht werden)
    $framework->add_content($MainContent);          // Add old MainContent-Variable (sollte auch bereinigt werden)

    // DEBUG:Alles
    if (isset($debug)) {
        $debug->addvar('$auth', $auth);
    }
    if (isset($debug)) {
        $debug->addvar('$cfg', $cfg);
    }
    if (isset($debug)) {
        $debug->tracker("All upto HTML-Output");
    }

    $framework->html_out();  // Output of all HTML
     unset($framework);
     unset($smarty);
     unset($templ);
     unset($dsp);

### Statistics will be updated only at scriptend, so pagesize and loadtime can be inserted

    if ($db->success) {

      // Statistic Functions (for generating server- and usage-statistics)
      include_once("modules/stats/class_stats.php");
        $stats = new stats();
        unset($stats);

      // Check Cronjobs
      if ($_GET['mod'] != 'install') {
          if (!isset($cron2)) {
              include_once('modules/cron2/class_cron2.php');
              $cron2 = new cron2();
          }
          $cron2->CheckJobs();
          unset($cron2);
      }

      // Disconnect DB
      $db->disconnect();
        unset($db);
    }
