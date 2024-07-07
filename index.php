<?php
// Composer autoloading
require __DIR__ . '/vendor/autoload.php';

use Smarty\Smarty;
use Symfony\Component\Cache;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\HttpClient;

$request = Request::createFromGlobals();
$filesystem = new Filesystem();
$httpClient = HttpClient::create();

define('ROOT_DIRECTORY', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

// Set error_reporting.
// It is set to this value on purpose, because otherwise
// LanSuite might not work properly anymore.
// Right now we depend on it. This will change in future
// when the development of LanSuite continues and is getting modernized.
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);

$PHPErrors = '';

// Load Basic Config
if (file_exists('inc/base/config.php')) {
    $config = parse_ini_file('inc/base/config.php', 1);

// Default config. Will be used only until the wizard has created the config file
} else {
    $config = [];

    $config['lansuite']['default_design'] = 'simple';
    $config['lansuite']['chmod_dir'] = '777';
    $config['lansuite']['chmod_file'] = '666';
    $config['lansuite']['debugmode'] = '0';
    $config['lansuite']['uid'] = uniqid('ls_', true);

    $config['database']['server'] = 'localhost';
    $config['database']['dbport'] = 3306;
    $config['database']['user'] = 'root';
    $config['database']['passwd'] = '';
    $config['database']['database'] = 'lansuite';
    $config['database']['prefix'] = 'ls_';
    $config['database']['charset'] = 'utf8mb4';

    $config['environment']['configured'] = 0;
}

// Check cache pool name - generate one if not existing
if (!array_key_exists('uid', $config['lansuite']) || $config['lansuite']['uid'] == '')
{
    $config['lansuite']['uid'] = uniqid('ls_', true);

    // Write config to file to ensure that uid is persistent
    $install = new \LanSuite\Module\Install\Install();
    $install->WriteConfig();
}

// Initialize Cache. Go for APCu first, filebased otherwise. DB adaptor to be used when we implement PDO.
if (extension_loaded('apcu')) {
    $cache = new Symfony\Component\Cache\Adapter\ApcuAdapter($config['lansuite']['uid'], 600);
} else {
    $cache = new Symfony\Component\Cache\Adapter\FilesystemAdapter($config['lansuite']['uid'], 600);
}

if (!isset($config['environment'])) {
    $config['environment']['configured'] = 0;
}

// If the debug mode is disabled, we launch the original error handler.
// The original error handler shows PHP Warnings in a typical red box
if (!$config['lansuite']['debugmode']) {
    $PHPErrorsFound = 0;

    // We only can set the error handler, once the system is set up.
    // The error handler writes into the database. If the environment is
    // not set up yet, we don't have the respective database tables.
    if ($config['environment']['configured']) {
        set_error_handler("myErrorHandler");
    }

// If the debug mode is enabled, we register the Symonfy/Debug component.
// This component shows the error in a nice stack trace.
// More information here: https://symfony.com/components/Debug
} elseif ($config['lansuite']['debugmode'] > 0) {
    Debug::enable();

    // TODO Once LanSuite is notice free, we set the $errorReportingLevel back to E_ALL
    // We need to re-set error_reporting here, because
    // the error handler component sets error_reporting(-1).
    // At the moment (2023-06-02) we only care about PHP Warning and above.
    error_reporting(E_ALL & ~E_NOTICE);
}

// Start session-management
session_start();

// Initialise Frameworkclass for Basic output
$MainContent = '';
$framework = new \LanSuite\Framework($request);
if (isset($_GET['fullscreen'])) {
    $framework->fullscreen($_GET['fullscreen']);
}

// Compromise ... design as base and popup should be deprecated
$design = $request->query->get('design');
$frmwrkmode = '';

if ($design == \LanSuite\Framework::DISPLAY_MODUS_BASE ||
    $design == \LanSuite\Framework::DISPLAY_MODUS_POPUP ||
    $design == \LanSuite\Framework::DISPLAY_MODUS_AJAX ||
    $design == \LanSuite\Framework::DISPLAY_MODUS_PRINT ||
    $design == \LanSuite\Framework::DISPLAY_MODUS_BEAMER) {
    $frmwrkmode = $design;
}
// Set Popupmode via GET (base, popup)
if (isset($_GET['frmwrkmode']) && $_GET['frmwrkmode']) {
    $frmwrkmode = $_GET['frmwrkmode'];
}
// Set Popupmode via GET (base, popup)
if (isset($frmwrkmode)) {
    $framework->setDisplayModus($frmwrkmode);
}

// Set HTTP-Headers
header('Content-Type: text/html; charset=utf-8');
header('X-Frame-Options: sameorigin');
// TODO: This header is still useful - Once we verified to send the correct MIME types, enable this header
// header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin');
// TODO Set Content-Security-Policy header

// Enforce HSTS if browsing via HTTPS
if ($request->isSecure()) {
    header('Strict-Transport-Security: max-age=86400');
}

include_once("ext_scripts/mobile_device_detect.php");
$framework->IsMobileBrowser = mobile_device_detect();

// For XHTML compatibility
@ini_set('arg_separator.output', '&amp;');

// Base Functions (anything that doesnt belong elsewere)
$func = new \LanSuite\Func();

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
// TODO Remove this get_magic_quotes_gpc function check, once this project had 7.4 as a minimum requirement.
// See
// - Setting: https://www.php.net/manual/en/info.configuration.php#ini.magic-quotes-gpc
// - Function: https://www.php.net/manual/en/function.get-magic-quotes-gpc.php
if (!function_exists('get_magic_quotes_gpc') || !get_magic_quotes_gpc()) {
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

// Include and Initialize base classes
$lang = [];

// Initialize debug mode
if ($config['lansuite']['debugmode'] > 0) {
    $debug = new \LanSuite\Debug($config['lansuite']['debugmode']);
    $framework->setDebugMode($debug);
}

// Load Translationclass. No t()-Function before this point!
$translation = new \LanSuite\Translation();

$smarty = new Smarty();
$smarty->setTemplateDir('.');
$smarty->setCompileDir('./ext_inc/templates_c/');
$smarty->setCacheDir('./ext_inc/templates_cache/');
// TODO Think about enabling caching for templates in production mode, but not in development mode
// See https://smarty-php.github.io/smarty/stable/api/caching/basics/
$smarty->setCaching(Smarty::CACHING_OFF);

$framework->setTemplateEngine($smarty);

if (isset($debug)) {
    $debug->tracker("Include and Init Smarty");
}

// Global counter for \LanSuite\Module\MasterSearch2\MasterSearch2 class
$ms_number = 0;

// Display Functions (to load the lansuite-templates)
$dsp = new \LanSuite\Display();

// DB Functions (to work with the database)
// TODO Remove the "old" database class, once everything is migrated to the new one (see below)
$db = new \LanSuite\DB();

// Loading the new database class with standard support for prepared statements.
$sqlmode = '';
if (array_key_exists('sqlmode', $config['database'])) {
    $sqlmode = $config['database']['sqlmode'];
}
$database = new \LanSuite\Database(
    $config['database']['server'],
    $config['database']['dbport'] ?? 3306,
    $config['database']['user'],
    $config['database']['passwd'],
    $config['database']['database'],
    $config['database']['charset'] ?? 'utf8mb4',
    $sqlmode
);
try {
    $database->connect();
    $database->setTablePrefix($config['database']['prefix']);
} catch(\mysqli_sql_exception $e) {
    $databaseError = $e->getMessage() . ' (' . $e->getCode() . ')';
    echo HTML_FONT_ERROR . t('Die Verbindung zur Datenbank ist fehlgeschlagen. LanSuite wird abgebrochen. Zurückgegebener MySQL-Fehler: ' . $databaseError) . HTML_FONT_END;
    exit();
}

// Security Functions (to lock pages)
$sec = new \LanSuite\Security();

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
    try {
        $db->connect(1);
    } catch (\mysqli_sql_exception $e) {
        //ignore connection error, this wil be dealt with later in the installation
    }

    $IsAboutToInstall = 1;

    // Force Admin rights for installing User
    $auth['type'] = \LS_AUTH_TYPE_SUPERADMIN;
    $auth["login"] = 1;
    $auth['userid'] = 0;

    // At this point in time, we are in the installation mode and the global
    // configuration $cfg does not exist yet, but it is accessed through the codebase already.
    // Lets load the default XML configuration from the installation module.
    $installConfigFile = 'modules/install/mod_settings/config.xml';
    $configLoader = new \LanSuite\Configuration();
    $configLoader->loadConfigurationFromXML($installConfigFile, 'install');
    $cfg = $configLoader->getConfigurationAsArray();

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

    //load $cfg from cache
    $cfgCache = $cache->getItem('cfg');
    if (!$cfgCache->isHit()) {
        $cfg = $func->read_db_config();
        $cfgCache->set($cfg);
        $cache->save($cfgCache);
    }
    $cfg = $cfgCache->get();

    // TODO Reload language
    // At this point, we have the configured language from the database, but the translations are
    // loaded already. Thats why the configured default language is used and loaded, even
    // if another language is selected in the install module.

    $message = $sec->check_blacklist();

    if (strlen($message) > 0) {
        die($message);
    }

    if (!array_key_exists('mod', $_GET) || !$_GET['mod']) {
        $_GET['mod'] = 'home';
    }
    $func->getActiveModules();

    $framework->addToPageTitle($cfg['sys_page_title']);
    if ($func->isModActive($_GET['mod'], $caption) && $_GET['mod'] != 'home') {
        $framework->addToPageTitle($caption);
    }

    // Start authentication, just if LS is working
    $authentication = new \LanSuite\Auth($database, $request, $frmwrkmode);
    // Test Cookie / Session if user is logged in
    $auth = $authentication->check_logon();
    // Olduserid for Switback on Boxes
    $olduserid = $authentication->get_olduserid();
}

// If the current user is not an admin, we suppress PHP warnings
// We keep them for Admins and Superadmins, because they might be interested in a fully functional system.
// We assume those errors get reported back to the developers.
if ($auth['type'] < \LS_AUTH_TYPE_ADMIN) {
    $errorlevel = error_reporting();
    error_reporting($errorlevel & ~E_WARNING);
}

// Initialize party
// Needed also, when not configured for LanSuite Import
if ($func->isModActive('party')) {
    $party = new \LanSuite\Module\Party\Party();

// If without party-module: Just give a fake ID, for many modules need it
} else {
    $party = new class {
        public $party_id;
    };
    // Just a random high number
    $party->party_id = (int) ($cfg['signon_partyid'] ?? 1683925);
}

if ($config['environment']['configured'] != 0) {
    if ($_GET['mod']=='auth') {
        switch ($_GET['action']) {
            case 'login':
                $emailValue = $_POST['email'] ?? '';
                $passwordValue = $_POST['password'] ?? '';
                $auth = $authentication->login($emailValue, $passwordValue);
                break;
            case 'logout':
                $auth = $authentication->logout();

                // At the moment we did not migrate fully to "Symfony\Component\HttpFoundation\Request".
                // LanSuite has the behaviour to write into superglobals, like $_GET.
                // HttpFoundation initiates from the superglobal only once.
                // In a regular case, writes to the superglobals won't be respected by HttpFoundation.
                // For the time being (until we fully migrate), we need to double write:
                // Once to the superglobal, once to HttpFoundation.
                $_GET['mod'] = 'home';
                $request->query->set('mod', 'home');
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
    global $cfg, $auth, $config, $_SESSION, $_GET, $smarty, $request;

    $design = $request->query->get('design');

    $user_design_change = false;
    if (array_key_exists('user_design_change', $cfg)) {
        $user_design_change = $cfg['user_design_change'];
    }
    // If user is not allowed to use an own selected design, or none is selected, use default
    if (!$user_design_change || (array_key_exists('design', $auth) && !$auth['design'])) {
        $auth['design'] = $config['lansuite']['default_design'];
    }

    // Design switch by URL
    if ($design && $design != 'popup' && $design != 'base') {
        $auth['design'] = $design;
    }

    // Fallback design is 'simple'
    if (!array_key_exists('design', $auth) || !file_exists('design/' . $auth['design'] . '/templates/main.htm')) {
        $auth['design'] = 'simple';
        if (!isset($_GET['design']) || ($_GET['design'] != 'popup' && $_GET['design'] != 'base')) {
            $design = 'simple';
        }
    }

    // For compaibility with old LS code
    $_SESSION['auth']['design'] = $auth['design'];

    // Assign
    $smarty->assign('default_design', $auth['design']);
}
initializeDesign();

// Load Rotation Banner
$actionParameter = $request->query->get('action');
$fullscreenSessionParameter = false;
if (array_key_exists('lansuite', $_SESSION) && array_key_exists('fullscreen', $_SESSION['lansuite'])) {
    $fullscreenSessionParameter = $_SESSION['lansuite']['fullscreen'];
}
if ($design != 'popup' && $actionParameter != 'wizard' && !$fullscreenSessionParameter && $db->success && $func->isModActive('sponsor')) {
    include_once("modules/sponsor/banner.php");
}

// Create Boxes / load Boxmanager
if (!$IsAboutToInstall && $design != 'base') {
    include_once("modules/boxes/boxes.php");
}

$db->DisplayErrors();
if ($PHPErrors) {
    $func->error($PHPErrors);
}
$PHPErrors = '';

include_once('index_module.inc.php');

// Complete Framework and Output HTML
$framework->setDesign($auth['design']);

$db->DisplayErrors();
if ($PHPErrors) {
    $func->error($PHPErrors);
}
$PHPErrors = '';

// Add old Frameworkmessages (should be deprecated)
if (isset($FrameworkMessages)) {
    $framework->addContent($FrameworkMessages);
}
// Add old MainContent-Variable (should be deprecated)
$framework->addContent($MainContent);

    // DEBUG:Alles
if (isset($debug)) {
    $debug->addvar('$auth', $auth);
    $debug->addvar('$cfg', $cfg);
    $debug->tracker("All up to HTML-Output");
}

// Output of all HTML
$framework->sendHTMLOutput();
unset($framework);
unset($smarty);
unset($templ);
unset($dsp);

// Statistics will be updated only at scriptend, so pagesize and loadtime can be inserted
if ($db->success) {
    // Statistic Functions (for generating server- and usage-statistics)
    $stats = new LanSuite\Module\Stats\Stats($request);
    unset($stats);

    // Check Cronjobs
    if ($_GET['mod'] != 'install') {
        if (!isset($cron2)) {
            $cron2 = new LanSuite\Module\Cron2\Cron2();
        }
        $cron2->CheckJobs();
        unset($cron2);
    }

    // Disconnect DB
    $db->disconnect();
    unset($db);
}
