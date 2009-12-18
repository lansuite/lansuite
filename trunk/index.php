<?php
### Set Error Reporting & INI-Settings

    if (defined('E_DEPRECATED')) error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED); // Will work for PHP >= 5.3
    else error_reporting(E_ALL ^ E_NOTICE); // For PHP < 5.3
    if (function_exists('ini_set')) {
      #ini_set('display_errors', 0);
      #ini_set('log_errors', 1);
      #ini_set('error_log', 'log/php/');
      // Disable SID in URL
      ini_set('url_rewriter.tags', '');
    }

    function myErrorHandler($errno, $errstr, $errfile, $errline) {
      global $PHPErrors, $PHPErrorsFound, $db, $auth;

      // Only show errors, which sould be reported according to error_reporting
      // Also filters @ (for @ will have error_reporting "0")
      $rep = ini_get('error_reporting');
      if(!($rep & $errno)) return false;

      // error_reporting setting currently doesn't show the following errors:
      // E_NOTICE
      // E_USER_NOTICE
      // E_DEPRECATED
      // E_USER_NOTICE
      // E_STRICT
      // E_DEPRECATED
      // Should change in the future!

      switch($errno){
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
            if (defined('E_DEPRECATED') and $errno == E_DEPRECATED) $errors = "Deprecated";
            else $errors = "Unknown error ($errno)";
          break;
      }

      // Store error, to print it later
      #$err = '<b>'. $errors .'</b>: '. $errstr .' in <b>'. $errfile .'</b> on line <b>'. $errline .'</b><br /><br />';
      $err = sprintf("PHP %s:  %s in %s on line %d", $errors, $errstr, $errfile, $errline);
      $PHPErrors .= $err;
      
      $PHPErrorsFound = 1;

      // Write to DB-Log
      // Attention: Be aware of loops!
      if (isset($db) and $db->success) $db->qry('INSERT INTO %prefix%log
        SET date = NOW(), userid = %int%, type = 3, description = %string%, sort_tag = "PHP-Fehler"',
        (int)$auth['userid'], $err);

      // Write error to log file
      if (ini_get('log_errors')) error_log($err);

      return true;
    }

    $PHPErrorsFound = 0;
    $PHPErrors = '';
    set_error_handler("myErrorHandler");

### Start session-management
    
    session_save_path('ext_inc/session');
    session_start();
    
### Initialise Frameworkclass for Basic output
    
    include_once("inc/classes/class_framework.php");
    $framework = new framework();
    $framework->fullscreen($_GET['fullscreen']);                // Switch fullscreen via GET
    // Notlösung... design als base und popup sollen ganz verschwinden
    if ($_GET['design']=='base' OR $_GET['design']=='popup' OR $_GET['design']=='ajax' OR $_GET['design']=='print') $frmwrkmode = $_GET['design']; // Set Popupmode via GET (base, popup)
    if ($_GET['frmwrkmode']) $frmwrkmode = $_GET['frmwrkmode']; // Set Popupmode via GET (base, popup)
    if (isset($frmwrkmode)) $framework->set_modus($frmwrkmode);
    // Ende Notlösung

### Set HTTP-Headers (still needed?)
    
    header('Content-Type: text/html; charset=utf-8');
    #header('Content-Type: application/xhtml+xml; charset=utf-8');
    #header("Cache-Control: no-cache, must-revalidate");

    include_once("ext_scripts/mobile_device_detect.php");
    $framework->IsMobileBrowser = mobile_device_detect();

    // For XHTML compatibility
    @ini_set('arg_separator.output', '&amp;');

### load $_POST and $_GET variables
    
    if (!is_array($_POST)) $_POST = $HTTP_POST_VARS;
    if (!is_array($_GET)) $_GET = $HTTP_GET_VARS;
    if (!is_array($_COOKIE)) $_COOKIE = $HTTP_COOKIE_VARS;

    // Base Functions (anything that doesnt belong elsewere)
    include_once("inc/classes/class_func.php");
    $func        = new func;

    // Prevent XSS
    foreach ($_GET as $key => $val) if (!is_array($_GET[$key])) $_GET[$key] = $func->NoHTML($_GET[$key], 1);
    else foreach ($_GET[$key] as $key2 => $val2) if (!is_array($_GET[$key][$key2])) $_GET[$key][$key2] = $func->NoHTML($_GET[$key][$key2], 1);
    else foreach ($_GET[$key][$key2] as $key3 => $val3) $_GET[$key][$key2][$key3] = $func->NoHTML($_GET[$key][$key2][$key3], 1);
    $_SERVER['REQUEST_URI'] = $func->NoHTML($_SERVER['REQUEST_URI'], 1);
    $_SERVER['HTTP_REFERER'] = $func->NoHTML($_SERVER['HTTP_REFERER'], 1);
    $_SERVER['QUERY_STRING'] = $func->NoHTML($_SERVER['QUERY_STRING'], 1);

    // Save original Array
    if (get_magic_quotes_gpc()) {
        foreach ($_GET as $key => $val) if (!is_array($_GET[$key])) $__GET[$key] = stripslashes($_GET[$key]);
        foreach ($_POST as $key => $val) if (!is_array($_POST[$key])) $__POST[$key] = stripslashes($_POST[$key]);
        foreach ($_COOKIE as $key => $val) if (!is_array($_COOKIE[$key])) $__COOKIE[$key] = stripslashes($_COOKIE[$key]);
    } else {
        $__GET = $_GET;
        $__POST = $_POST;
        $__COOKIE = $_COOKIE;
    }

    // Emulate MQ, if disabled
    if (!get_magic_quotes_gpc()) {   // and !get_magic_quotes_runtime()
        foreach ($_GET as $key => $val) if (!is_array($_GET[$key])) $_GET[$key] = addslashes($_GET[$key]);
        foreach ($_POST as $key => $val) if (!is_array($_POST[$key])) $_POST[$key] = addslashes($_POST[$key]);
        foreach ($_COOKIE as $key => $val) if (!is_array($_COOKIE[$key])) $_COOKIE[$key] = addslashes($_COOKIE[$key]);
    }

    // Protect from XSS
    #foreach ($_GET as $key => $val) $_GET[$key] = preg_replace('#&lt;script(.)*>#sUi', '', $_GET[$key]);
    #foreach ($_POST as $key => $val) $_POST[$key] = preg_replace('#&lt;script(.)*>#sUi', '', $_POST[$key]);
    
### Read Config and Definitionfiles
    
    $config = parse_ini_file('inc/base/config.php', 1);     // Load Basic Config
    include_once('inc/base/define.php');                    // Read definition file
    // Exit if no Configfile
    if (!$config) {
        echo HTML_FONT_ERROR. 'Öffnen oder Lesen der Konfigurations-Datei nicht möglich. Lansuite wird beendet.' .HTML_NEWLINE . "
        Überprüfen Sie die Datei <b>config.php</b> im Verzeichnis inc/base/" .HTML_FONT_END;
        exit();
    }
    $lang = array(); // For old $lang 

### Include base classes
    
    // Load Translationclass. No t()-Function before this point!
    include_once("inc/classes/class_translation.php");
    $translation = new translation();

    include_once("inc/classes/class_db_mysql.php");
    include_once("inc/classes/class_auth.php");
    include_once("inc/classes/class_xml.php");
    include_once("inc/classes/class_display.php");
    include_once("inc/classes/class_gd.php");
    include_once("inc/classes/class_sec.php");
    include_once("modules/party/class_party.php");
    include_once("modules/mail/class_mail.php");
    #include_once("modules/msgsys2/class_msgsys.php");
    include_once("modules/stats/class_stats.php");
    include_once("modules/seating/class_seat.php");
    include_once("modules/cron2/class_cron2.php");
    include_once('ext_scripts/smarty/Smarty.class.php');

### Initialize base classes

    $gd          = new gd;               // GD Functions (for graphical outputs)
    $dsp         = new display();        // Display Functions (to load the lansuite-templates)
    $mail        = new mail();           // Mail Functions (for sending mails to lansuite-users)
    #$msgsys         = new msgsys;           // Msgsys Functions (for sending mails to lansuite-users, manage the buddylist and the messenger)
    $xml         = new xml;              // XML Functions (to maintain XML-Ex-/Imports)
    $db          = new db;               // DB Functions (to work with the databse)
    $sec         = new sec;              // Security Functions (to lock pages)
    $cron2       = new cron2();          // Load Cronjob
    $seat2       = new seat2();          // Load Seat-Controll Class
    $smarty      = new Smarty();
    $smarty->template_dir = '.';
    $smarty->compile_dir = './ext_inc/templates_c/';
    $smarty->cache_dir = './ext_inc/templates_cache/';
    $smarty->caching = false;
    $smarty->cache_lifetime = 0; // sec
    #$smarty->compile_check = 0;

### Initalize Basic Parameters

    $language = $translation->get_lang(); // Set and Read Systemlanguage
    // Load Base-Lang-File. OLD!!! Only for old $lang in Systemfolders
    if (file_exists("inc/language/language_$language.php")) include_once("inc/language/language_$language.php");
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

        // Need class_party for LanSurfer Import
        if ($db->success) $party = new party();

    } else {
        ### Normal auth cycle and Database-init

        $db->connect(0);
        $IsAboutToInstall = 0;

        $translation->load_trans('db', $_GET['mod']); // DB-Mode on Running System
        // FIX : Add function to scan DB for correkt config and Tables (prefix etc.)

        // Reset DB-Success in Setup if no Adm.-Account was found, because a connection could work, but prefix is wrong
        if (!func::admin_exists() and (($_GET["action"] == "wizard" and $_GET["step"] <= 3) or ($_GET["action"] == "ls_conf"))) $db->success = 0;

        $cfg = $func->read_db_config(); // Config-Tabelle aulesen
        $sec->check_blacklist();
        
        // Set timezone info (php + mysql)
        if ($cfg['sys_timezone'] and function_exists('date_default_timezone_set')) {
          #date_default_timezone_set($cfg['sys_timezone']);
          #$db->qry('SET SESSION time_zone = %string%', $cfg['sys_timezone']);
          ##$db->qry('SET SESSION time_zone = \'+0:00\'');
        }
        
        if (!$_GET['mod']) $_GET['mod'] = 'home';
        // FIX : Maybe its a good Idea make a func::get_activemodules()
        // Fetch all names of active modules
        $ActiveModules = array();
        $framework->AddToPageTitle($cfg['sys_page_title']);
        $res = $db->qry('SELECT name, caption FROM %prefix%modules WHERE active = 1');
        while($row = $db->fetch_array($res)) {
          $ActiveModules[] = $row['name'];
          if ($_GET['mod'] == $row['name']) $framework->AddToPageTitle($row['caption']);
        }
        $db->free_result($res);
        $ActiveModules[] = 'helplet';
        $ActiveModules[] = 'popups';
        $ActiveModules[] = 'auth';
        
        ### Start autentication, just if LS is working
        
        $authentication = new auth($frmwrkmode);
        $auth      = $authentication->check_logon();    // Testet Cookie / Session ob User eingeloggt ist
        $olduserid = $authentication->get_olduserid();  // Olduserid for Switback on Boxes

        // Initialize party
        $party = new party();

        if ($_GET['mod']=='auth'){
           switch ($_GET['action']){
                case 'login':
                   $auth = $authentication->login($_POST['email'],$_POST['password']);
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
               
        // Statistic Functions (for generating server- and usage-statistics)
        if ($db->success) $stats = new stats();
    }

### Set Default-Design, if non is set

    initializeDesign();

### Load Rotation Banner

    include_once("modules/sponsor/banner.php");

### Create Boxes / load Boxmanager
    
    if (!$IsAboutToInstall and $_GET['design'] != 'base') include_once("modules/boxes/boxes.php");

### index_module.inc.php load the Modulactions and Codes

    $db->DisplayErrors();
    if ($PHPErrors) $func->error($PHPErrors);
    $PHPErrors = '';
    
    include_once('index_module.inc.php');

### Complete Framework and Output HTML

    $framework->set_design($auth['design']); 

    $db->DisplayErrors();
    if ($PHPErrors) $func->error($PHPErrors);
    $PHPErrors = '';

    $framework->add_content($FrameworkMessages);    // Add old Frameworkmessages (sollten dann ausgetauscht werden)
    $framework->add_content($MainContent);          // Add oll MainContent-Variable (sollte auch bereinigt werden)

    $framework->html_out();  // Output of all HTML
    
### Statistics will be updated only at scriptend, so pagesize and loadtime can be insert

    if ($db->success) {
      //if ($_GET['design'] != 'base' AND !$_GET['mod']=="install") $stats->update($sitetool->out_work(), 0);
      // Check Cronjobs
      if (!$_GET['mod']=="install") $cron2->CheckJobs();
      $db->disconnect();
    }
    
    /*
     * Initializes the design of lansuite. 
     */
    function initializeDesign() {
        global $cfg, $auth, $config, $_SESSION, $_GET, $smarty;
        
        // If user is not allowed to use an own selected design, or none is selected, use default
        if (!$cfg['user_design_change'] or !$auth["design"]) $auth['design'] = $config['lansuite']['default_design'];
        if (!$auth["design"]) $auth["design"] = "simple"; // Default if none
        if (!file_exists("design/{$auth["design"]}/templates/main.htm")) $auth["design"] = "simple"; // Default if not availible
        $_SESSION["auth"]["design"] = $auth["design"]; // For compaibility with old LS code
        // folgendes betrifft momentan wohl nur Beamer
        if ($_GET['design'] and $_GET['design'] != 'popup' and $_GET['design'] != 'base') $auth['design'] = $_GET['design'];
        $smarty->assign('default_design', $auth['design']);
    }

?>
