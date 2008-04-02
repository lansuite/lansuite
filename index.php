<?php

// Set Error Reporting to "all, but notices"
error_reporting(E_ALL ^ E_NOTICE);
#ini_set('display_errors', 0);
#ini_set('log_errors', 1);
#ini_set('error_log', 'log/php/');


// Sitetool (for compressing the content sending it to the browser)
if (!isset($_GET['design']) or $_GET['design'] != 'base') {
  include_once("inc/classes/class_sitetool.php");
  $sitetool = new sitetool('');
}

$FrameworkMessages = '';

/*
if ($_GET['load_file']) {
  if (strpos($_GET['load_file'], 'ext_inc/') === false) exit;
  $_GET['load_file'] = str_replace('..', '', $_GET['load_file']);

  header('Content-type: application/octetstream'); # Others: application/octet-stream # application/force-download
  header('Content-Disposition: attachment; filename="'. substr($_GET['load_file'], strrpos($_GET['load_file'], '/') + 1, strlen($_GET['load_file'])) .'"');
  header("Content-Length: " .(string)(filesize($_GET['load_file'])));
  readfile($_GET['load_file']);
  exit;
}
*/
// HTTP-Headers
header('Content-Type: text/html; charset=utf-8');
#header('Content-Type: application/xhtml+xml; charset=utf-8');
#header("Cache-Control: no-cache, must-revalidate");

// For XHTML compatibility
@ini_set('arg_separator.output', '&amp;');

// Start session-management
session_save_path('ext_inc/session');
session_start();

// Analyze current URL
$CurentURL = '';
$CurentURLBase = '';
$CurentURLMod = '';
if (isset($_SERVER['REQUEST_URI'])) {
    $CurentURL = @parse_url($_SERVER['REQUEST_URI']);
    $CurentURLBase = str_replace('&contentonly=1', '', $CurentURL['path'].'?'.$CurentURL['query']);
    preg_match('#mod=([a-zA-z0-9]*)[&]*#', $CurentURLBase, $treffer);
    $CurentURLMod = $treffer[1];
}

// load $_POST and $_GET variables
if (!is_array($_POST)) $_POST = $HTTP_POST_VARS;
if (!is_array($_GET)) $_GET = $HTTP_GET_VARS;
if (!is_array($_COOKIE)) $_COOKIE = $HTTP_COOKIE_VARS;

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

/*
// Delete Statements from URL, which could manipulate an SQL-WHERE-Clause
foreach ($_GET as $key => $val) if (!is_array($_GET[$key])) {
  $_GET[$key] = eregi_replace(' and ', '', $_GET[$key]);
  $_GET[$key] = eregi_replace(' and\(', '', $_GET[$key]);
  $_GET[$key] = eregi_replace(' or ', '', $_GET[$key]);
  $_GET[$key] = eregi_replace(' or\(', '', $_GET[$key]);
}
*/

// For compatibilty of old LS-Modules
$vars = array_merge((array)$_GET, (array)$_POST);

// Save Path
$script_filename = '';
#$script_filename = substr($_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"], "/") + 1, strlen($_SERVER["SCRIPT_NAME"]));
#$script_filename = substr($_SERVER["PATH_TRANSLATED"], strrpos($_SERVER["PATH_TRANSLATED"], "/") + 1, strlen($_SERVER["PATH_TRANSLATED"]));
if (isset($_SERVER['REQUEST_URI'])) $script_filename = substr($_SERVER["REQUEST_URI"], strrpos($_SERVER["REQUEST_URI"], "/") + 1, strlen($_SERVER["REQUEST_URI"]));
$script_filename = substr($script_filename, 0, strpos($script_filename, "?"));


// Vollbild per GET Parameter ein/ausschalten
if (isset($_GET['fullscreen'])) {
    if ($_GET['fullscreen'] == 'yes')   $_SESSION['lansuite']['fullscreen'] = true;
    elseif ($_GET['fullscreen'] == 'no')    $_SESSION['lansuite']['fullscreen'] = false;
}

// Read config-file
$config = parse_ini_file('inc/base/config.php', 1);

// Read definition file
include_once('inc/base/define.php');

$lang = array();

if (!$config) {
    echo HTML_FONT_ERROR. 'Ã–ffnen oder Lesen der Konfigurations-Datei nicht mÃ¶glich. Lansuite wird beendet.' .HTML_NEWLINE . "
    ÃœberprÃ¼fen Sie die Datei <b>config.php</b> im Verzeichnis inc/base/" .HTML_FONT_END;
    exit();
}

//// Load Base-Lang-File
// 1) Include "de"
if (file_exists("inc/language/language_de.php")) include_once("inc/language/language_de.php");
if (file_exists("modules/mastersearch/language/mastersearch_lang_de.php")) include_once("modules/mastersearch/language/mastersearch_lang_de.php");

### Include base classes

    if (extension_loaded("mysqli")) include_once("inc/classes/class_db_mysqli.php");
    else include_once("inc/classes/class_db_mysql.php");
    include_once("inc/classes/class_func.php");
    include_once("inc/classes/class_auth.php");
    include_once("inc/classes/class_xml.php");
    include_once("inc/classes/class_display.php");
    include_once("inc/classes/class_gd.php");
    include_once("inc/classes/class_sec.php");
    include_once("inc/classes/class_barcode.php");
    include_once("inc/classes/class_translation.php");
    
    include_once("modules/party/class_party.php");
    include_once("modules/install/class_install.php");
    if (file_exists("modules/mastersearch/class_mastersearch.php")) include_once("modules/mastersearch/class_mastersearch.php");
    include_once("modules/mail/class_mail.php");
    include_once("modules/stats/class_stats.php");
    include_once("modules/seating/class_seat.php");
    include_once("modules/cron2/class_cron2.php");

### Initialize base classes

    $func       = new func;         // Base Functions (anything that doesnt belong elsewere)
    $gd         = new gd;           // GD Functions (for graphical outputs)
    $dsp        = new display();    // Display Functions (to load the lansuite-templates)
    $mail       = new mail();       // Mail Functions (for sending mails to lansuite-users)
    $xml        = new xml;          // XML Functions (to maintain XML-Ex-/Imports)
    $install    = new Install();    // Install Functions (Some basic Setup-Routines)
    $db         = new db;           // DB Functions (to work with the databse)
    $sec        = new sec;          // Security Functions (to lock pages)
    $cron2      = new cron2();      // Load Cronjob
    $seat2      = new seat2();      // Load Seat-Controll Class

### Installingsystem or normal auth

    if ($config['environment']['configured'] == 0) {

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
            $install->SetTableNames();       // Load SQL-Tables used by each page
            $cfg = $func->read_db_config();  // read Configtable
        }
        $language = func::get_lang(false); // Set Install language by Wizard defaultstep

    } else {

        ### Normal auth cycle and Database-init

        $db->connect(0);
        $IsAboutToInstall = 0;

        // FIX : Add function to scan DB for correkt config and Tables (prefix etc.)

        // Reset DB-Success in Setup if no Adm.-Account was found, because a connection could work, but prefix is wrong
        if (!func::admin_exists() and (($_GET["action"] == "wizard" and $_GET["step"] <= 3) or ($_GET["action"] == "ls_conf"))) $db->success = 0;

        $install->SetTableNames();      // Load SQL-Tables used by each page to $config['tables']['name'] = "prefix_name"
        $cfg = $func->read_db_config(); // Config-Tabelle aulesen
        $sec->check_blacklist();
        
        // FIX : Maybe its a good Idea make a func::get_activemodules()
        // Fetch all names of active modules
        $ActiveModules = array();
        $res = $db->query("SELECT name FROM {$config["tables"]["modules"]} WHERE active = 1");
        while($row = $db->fetch_array($res)) $ActiveModules[] = $row['name'];
        $db->free_result($res);
        $ActiveModules[] = 'helplet';
        $ActiveModules[] = 'popups';
        $ActiveModules[] = 'auth';
        
        $language = func::get_lang(true); // Get and set Language
        
        // Start autentication, just if LS is working
        $authentication = new auth();
        $auth      = $authentication->check_logon();    // Testet Cookie / Session ob User eingeloggt ist
        $olduserid = $authentication->get_olduserid();  // Olduserid for Switback on Boxes

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
        
        // Initialize party
        $party = new party();
        
        // Statistic Functions (for generating server- and usage-statistics)
        if ($db->success) $stats = new stats();
    }



// Load Barcode System
$barcode    = new barcode_system(); // Barcode System

//// Load Base-Lang-File
// 2) Overwrite with $language
if ($language != "de" and file_exists("inc/language/language_$language.php")) include_once("inc/language/language_$language.php");
if ($language != "de" and file_exists("modules/mastersearch/language/mastersearch_lang_$language.php")) include_once("modules/mastersearch/language/mastersearch_lang_$language.php");
if ($language != "de" and file_exists("modules/boxes/language/boxes_lang_$language.php")) include_once("modules/boxes/language/boxes_lang_$language.php");
if ($language != "de" and file_exists("modules/install/language/install_lang_$language.php")) include_once("modules/install/language/install_lang_$language.php");



// Show Blocked Site
if($cfg['sys_blocksite'] == 1 and $auth['type'] < 2) $siteblock = true;

// Set Default-Design, if non is set
if (!$auth["design"]) $auth["design"] = "simple";
if (!file_exists("design/{$auth["design"]}/templates/index.php")) $auth["design"] = "simple";
$_SESSION["auth"]["design"] = $auth["design"];
if ($_GET['design'] and $_GET['design'] != 'popup' and $_GET['design'] != 'base') $auth['design'] = $_GET['design'];

// Boxes
if (!$IsAboutToInstall and !$_GET['contentonly'] and $_GET['design'] != 'base') include_once("modules/boxes/class_boxes.php");

if ($_GET['design'] != 'base') include_once('design/'. $auth['design'] .'/templates/index.php');
else include_once('index_module.inc.php');

// Aktualisierung der Statistik wird erst am Schluss durchgefÃ¼hrt, damit SeitengrÃ¶sse und Berechnungsdauer eingetragen werden kÃ¶nnen.
if ($db->success) {
  if ($_GET['design'] != 'base' AND !$_GET['mod']=="install") $stats->update($sitetool->out_work(), 0);

  // Check Cronjobs
  if (!$_GET['mod']=="install") $cron2->CheckJobs();
  $db->disconnect();
}
?>