<?php
// Composer autoloading
require __DIR__ . '/vendor/autoload.php';

// Load global functions
// TODO Switch to function autoloading later on
require_once('inc/Functions/T.php');
require_once('inc/Functions/FetchDataRow.php');
require_once('inc/Functions/FetchPostRow.php');
require_once('inc/Functions/MasterCommentEditAllowed.php');
require_once('inc/Functions/CheckValidEmail.php');


// Set error_reporting.
// It is set to this value on purpose, because otherwise
// LanSuite might not work properly anymore.
// Right now we depend on it. This will change in future
// when the development of LanSuite continues and is getting modernized.
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);

if (function_exists('ini_set')) {
    // Disable SID in URL
    ini_set('url_rewriter.tags', '');
}

function myErrorHandler($errno, $errstr, $errfile, $errline) {
    global $PHPErrors, $PHPErrorsFound, $db, $auth;

    // Only show errors, which sould be reported according to error_reporting
    // Also filters @ (for @ will have error_reporting "0")
    // Why this is necessary at all?
    // From the PHP docs of "set_error_handler"
    //      It is important to remember that the standard PHP error handler is completely bypassed for the error types specified by error_types unless the callback function returns FALSE.
    // Source: https://secure.php.net/manual/en/function.set-error-handler.php
    // LanSuite is _at the moment_ (2018-01-13) not PHP Notice free.
    // We are working on this. Once this is done, we can remove the next two
    // conditions and move along.
    // Until this time we have to keep it.
    // Otherwise the system might not be usable at all.
    $rep = ini_get('error_reporting');
    if (!($rep & $errno)) {
        return false;
    }

    if (error_reporting() == 0) {
        return false;
    }

    switch ($errno) {
        case E_ERROR:
            $errors = "Error";
            break;
        case E_WARNING:
            $errors = "Warning";
            break;
        case E_PARSE:
            $errors = "Parse Error";
            break;
        case E_NOTICE:
            $errors = "Notice";
            break;
        case E_CORE_ERROR:
            $errors = "Core Error";
            break;
        case E_CORE_WARNING:
            $errors = "Core Warning";
            break;
        case E_COMPILE_ERROR:
            $errors = "Compile Error";
            break;
        case E_COMPILE_WARNING:
            $errors = "Compile Warning";
            break;
        case E_USER_ERROR:
            $errors = "User Error";
            break;
        case E_USER_WARNING:
            $errors = "User Warning";
            break;
        case E_USER_NOTICE:
            $errors = "User Notice";
            break;
        case E_STRICT:
            $errors = "Strict Notice";
            break;
        case E_RECOVERABLE_ERROR:
            $errors = "Recoverable Error";
            break;
        default:
            if ($errno == E_DEPRECATED) {
                $errors = "Deprecated";
            } elseif ($errno == E_USER_DEPRECATED) {
                $errors = "User Deprecated";
            } else {
                $errors = "Unknown error ($errno)";
            }
    }

    $err = sprintf("PHP %s: %s in %s on line %d", $errors, $errstr, $errfile, $errline);
    if (ini_get('log_errors')) {
        error_log($err);
    }

    $PHPErrors .= $err .'<br />';
    $PHPErrorsFound = 1;

    // Write to DB-Log
    if (isset($db) and $db->success) {
        $db->qry('
            INSERT INTO %prefix%log
            SET date = NOW(),
                userid = %int%,
                type = 3,
                description = %string%,
                sort_tag = "PHP-Fehler"',
            (int) $auth['userid'],
            $err
        );
    }

    return true;
}

$PHPErrorsFound = 0;
$PHPErrors = '';
set_error_handler("myErrorHandler");

// Start session-management
session_start();

// Initialise Frameworkclass for Basic output
$framework = new framework();
if (isset($_GET['fullscreen'])) {
    $framework->fullscreen($_GET['fullscreen']);
}

// Compromise ... design as base and popup should be deprecated
if (isset($_GET['design']) && ($_GET['design'] == 'base' || $_GET['design'] == 'popup' || $_GET['design'] == 'ajax' || $_GET['design'] == 'print' || $_GET['design'] == 'beamer')) {
    $frmwrkmode = $_GET['design'];
}
// Set Popupmode via GET (base, popup)
if (isset($_GET['frmwrkmode']) && $_GET['frmwrkmode']) {
    $frmwrkmode = $_GET['frmwrkmode'];
}
// Set Popupmode via GET (base, popup)
if (isset($frmwrkmode)) {
    $framework->set_modus($frmwrkmode);
}

// Set HTTP-Headers
header('Content-Type: text/html; charset=utf-8');

include_once("ext_scripts/mobile_device_detect.php");
$framework->IsMobileBrowser = mobile_device_detect();

// For XHTML compatibility
@ini_set('arg_separator.output', '&amp;');

// Base Functions (anything that doesnt belong elsewere)
$func = new func();

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
if (isset($_SERVER['HTTP_REFERER'])) {
    $_SERVER['HTTP_REFERER'] = $func->NoHTML($_SERVER['HTTP_REFERER'], 1);
}
$_SERVER['QUERY_STRING'] = $func->NoHTML($_SERVER['QUERY_STRING'], 1);

// Save original Array
// This is use in modules mails and popups and in ./inc/classes/class_masterform.php
// TODO investigate why this is needed
$__POST = $_POST;

// Emulate MQ, if disabled
if (!get_magic_quotes_gpc()) {
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

// Read Config and Definitionfiles
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

// Read definition file
include_once('inc/base/define.php');

// Include and Initialize base classes
$lang = [];

// Debug initialisieren
if ($config['lansuite']['debugmode'] > 0) {
    require_once('inc/Functions/Debug.php');
    $debug = new debug($config['lansuite']['debugmode']);
}

// Load Translationclass. No t()-Function before this point!
$translation = new translation();

$smarty = new Smarty();
$smarty->template_dir = '.';
$smarty->compile_dir = './ext_inc/templates_c/';
$smarty->cache_dir = './ext_inc/templates_cache/';
$smarty->caching = false;
// Lifetime is in seconds
$smarty->cache_lifetime = 0;

if (isset($debug)) {
    $debug->tracker("Include and Init Smarty");
}

// Display Functions (to load the lansuite-templates)
$dsp = new display();

// DB Functions (to work with the databse)
$db = new db();

// Security Functions (to lock pages)
$sec = new sec();

if (isset($debug)) {
    $debug->tracker("Include and Init Base Classes");
}

// Initalize Basic Parameters
// Set and Read Systemlanguage
$language = $translation->get_lang();
$smarty->assign('language', $language);

// Installingsystem or normal auth
if ($config['environment']['configured'] == 0) {
    // Filemode on Installation
    $translation->load_trans('xml', 'install');

    // Prepare install
    // Force installwizard if LS not configured
    $_GET['mod'] = 'install';
    $_GET['action'] = 'wizard';

    // Silent connect
    $db->connect(1);
    $IsAboutToInstall = 1;

    // Force Adminrights for installing User
    $auth["type"] = 3;
    $auth["login"] = 1;

    // Load DB-Data after installwizard step 3
    if ($_GET["action"] == "wizard" && isset($_GET["step"]) && $_GET["step"] > 3) {
        $cfg = $func->read_db_config();
    }

} else {
    // Normal auth cycle and Database-init
    $db->connect(0);
    $IsAboutToInstall = 0;
    if (isset($_GET['mod'])) {
        $translation->load_trans('db', $_GET['mod']);
    }

    // Reset DB-Success in Setup if no Adm.-Account was found, because a connection could work, but prefix is wrong
    if (!$func->admin_exists() && isset($_GET["action"]) && (($_GET["action"] == "wizard" && $_GET["step"] <= 3) || ($_GET["action"] == "ls_conf"))) {
        $db->success = 0;
    }

    $cfg = $func->read_db_config();
    $message = $sec->check_blacklist();
    if (strlen($message) > 0) {
        die($message);
    }

    if (!$_GET['mod']) {
        $_GET['mod'] = 'home';
    }
    $func->getActiveModules();

    $framework->AddToPageTitle($cfg['sys_page_title']);
    if ($func->isModActive($_GET['mod'], $caption) && $_GET['mod'] != 'home') {
        $framework->AddToPageTitle($caption);
    }

    // Start authentication, just if LS is working
    $authentication = new auth($frmwrkmode);
    // Test Cookie / Session if user is logged in
    $auth = $authentication->check_logon();
    // Olduserid for Switback on Boxes
    $olduserid = $authentication->get_olduserid();
}

// Initialize party
// Needed also, when not configured for LanSuite Import
if ($func->isModActive('party')) {
    include_once("modules/party/class_party.php");
    $party = new party();

// If without party-module: Just give a fake ID, for many modules need it
} else {
    class party
    {
        public $party_id;
    }
    $party = new party();
    $party->party_id = (int) $cfg['signon_partyid'];
}

if ($config['environment']['configured'] != 0) {
    if ($_GET['mod']=='auth') {
        switch ($_GET['action']) {
            case 'login':
                $auth = $authentication->login($_POST['email'], $_POST['password']);
                break;
            case 'logout':
                $auth = $authentication->logout();
                $_GET['mod'] = 'home';
                break;
            // Switch to user
            case 'switch_to':
                $authentication->switchto($_GET["userid"]);
                break;
            // Switch back to Adminuser
            case 'switch_back':
                $authentication->switchback();
                break;
        }
    }
}

/*
 * Initializes the design of lansuite.
 */
function initializeDesign()
{
    global $cfg, $auth, $config, $_SESSION, $_GET, $smarty;

    // If user is not allowed to use an own selected design, or none is selected, use default
    if (!$cfg['user_design_change'] || !$auth['design']) {
        $auth['design'] = $config['lansuite']['default_design'];
    }

    // Design switch by URL
    if (isset($_GET['design']) && $_GET['design'] != 'popup' && $_GET['design'] != 'base') {
        $auth['design'] = $_GET['design'];
    }

    // Fallback design is 'simple'
    if (!$auth['design'] || !file_exists('design/' . $auth['design'] . '/templates/main.htm')) {
        $auth['design'] = 'simple';
        if (!isset($_GET['design']) || ($_GET['design'] != 'popup' && $_GET['design'] != 'base')) {
            $_GET['design'] = 'simple';
        }
    }

    // For compaibility with old LS code
    $_SESSION['auth']['design'] = $auth['design'];

    // Assign
    $smarty->assign('default_design', $auth['design']);
}
initializeDesign();

// Load Rotation Banner
if ($_GET['design'] != 'popup' && $_GET['action'] != 'wizard' && !$_SESSION['lansuite']['fullscreen'] && $db->success && $func->isModActive('sponsor')) {
    include_once("modules/sponsor/banner.php");
}

// Create Boxes / load Boxmanager
if (!$IsAboutToInstall && $_GET['design'] != 'base') {
    include_once("modules/boxes/boxes.php");
}

$db->DisplayErrors();
if ($PHPErrors) {
    $func->error($PHPErrors);
}
$PHPErrors = '';

include_once('index_module.inc.php');

// Complete Framework and Output HTML
$framework->set_design($auth['design']);

$db->DisplayErrors();
if ($PHPErrors) {
    $func->error($PHPErrors);
}
$PHPErrors = '';

// Add old Frameworkmessages (should be deprecated)
$framework->add_content($FrameworkMessages);
// Add old MainContent-Variable (should be deprecated)
$framework->add_content($MainContent);

    // DEBUG:Alles
if (isset($debug)) {
    $debug->addvar('$auth', $auth);
    $debug->addvar('$cfg', $cfg);
    $debug->tracker("All up to HTML-Output");
}

// Output of all HTML
$framework->html_out();
unset($framework);
unset($smarty);
unset($templ);
unset($dsp);

// Statistics will be updated only at scriptend, so pagesize and loadtime can be inserted
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
