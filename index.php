<?php

// HTTP-Headers
header('Content-Type: text/html; charset=utf-8');
#header("Cache-Control: no-cache, must-revalidate");

// Error Reporting auf "Alles außer Hinweise" setzen
error_reporting(E_ALL ^ E_NOTICE);

// Start session-management
session_start();

// Analyze current URL
$CurentURL = @parse_url($_SERVER['REQUEST_URI']);
$CurentURLBase = str_replace('&contentonly=1', '', $CurentURL['path'].'?'.$CurentURL['query']);
preg_match('#mod=([a-zA-z0-9]*)[&]*#', $CurentURLBase, $treffer);
$CurentURLMod = $treffer[1];

// load $_POST and $_GET variables
if (!is_array($_POST)) $_POST = $HTTP_POST_VARS;
if (!is_array($_GET)) $_GET = $HTTP_GET_VARS;

// Save original Array
if (get_magic_quotes_gpc()) {
  foreach ($_GET as $key => $val) if (!is_array($_GET[$key])) $__GET[$key] = stripslashes($_GET[$key]);
  foreach ($_POST as $key => $val) if (!is_array($_POST[$key])) $__POST[$key] = stripslashes($_POST[$key]);
} else {
  $__GET = $_GET;
  $__POST = $_POST;
}

// Emulate MQ, if disabled
if (!get_magic_quotes_gpc()) {	 // and !get_magic_quotes_runtime()
	foreach ($_GET as $key => $val) if (!is_array($_GET[$key])) $_GET[$key] = addslashes($_GET[$key]);
	foreach ($_POST as $key => $val) if (!is_array($_POST[$key])) $_POST[$key] = addslashes($_POST[$key]);
}
/*
// Delete Statements from URL, which could manipulate an SQL-WHERE-Clause
foreach ($_GET as $key => $val) if (!is_array($_GET[$key])) {
  $_GET[$key] = eregi_replace(' and ', '', $_GET[$key]);
  $_GET[$key] = eregi_replace(' and\(', '', $_GET[$key]);
  $_GET[$key] = eregi_replace(' or ', '', $_GET[$key]);
  $_GET[$key] = eregi_replace(' or\(', '', $_GET[$key]);
}
*/
$vars = array_merge((array)$_GET, (array)$_POST);


// Save Path
#$script_filename = substr($_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"], "/") + 1, strlen($_SERVER["SCRIPT_NAME"]));
#$script_filename = substr($_SERVER["PATH_TRANSLATED"], strrpos($_SERVER["PATH_TRANSLATED"], "/") + 1, strlen($_SERVER["PATH_TRANSLATED"]));
$script_filename = substr($_SERVER["REQUEST_URI"], strrpos($_SERVER["REQUEST_URI"], "/") + 1, strlen($_SERVER["REQUEST_URI"]));
$script_filename = substr($script_filename, 0, strpos($script_filename, "?"));


// Vollbild per GET Parameter ein/ausschalten
if ($_GET["fullscreen"] == "yes") 	$_SESSION["lansuite"]["fullscreen"] = true;
elseif ($_GET["fullscreen"] == "no") 	$_SESSION["lansuite"]["fullscreen"] = false;

// Read config-file
$config	= parse_ini_file("inc/base/config.php", 1);

// Read definition file 
include_once("inc/base/define.php");

$lang = array();

if (!$config) {
	echo HTML_FONT_ERROR. "Öffnen oder Lesen der Konfigurations-Datei nicht möglich. Lansuite wird beendet." .HTML_NEWLINE . "
	Überprüfen Sie die Datei <b>config.php</b> im Verzeichnis inc/base/" .HTML_FONT_END;
	exit(); 
}

// Wenn configured = 0: Setup aufrufen
$IsAboutToInstall = 0;
if ($config['environment']['configured'] == 0) {
	$_GET['mod'] = 'install';
	$_GET['action'] = 'wizard';
	$IsAboutToInstall = 1;
}

//// Load Base-Lang-File
// 1) Include "de"
if (file_exists("inc/language/language_de.php")) include_once("inc/language/language_de.php");
if (file_exists("modules/mastersearch/language/mastersearch_lang_de.php")) include_once("modules/mastersearch/language/mastersearch_lang_de.php");

// Include base classes
if (extension_loaded("mysqli")) include_once("inc/classes/class_db_mysqli.php");
else include_once("inc/classes/class_db_mysql.php");
include_once("inc/classes/class_func.php");
include_once("inc/classes/class_auth.php");
include_once("inc/classes/class_xml.php");
if ($_GET['design'] != 'base') include_once("inc/classes/class_sitetool.php");
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

// Initialize base classes
$func		= new func;			// Base Functions (anything that doesnt belong elsewere)
$gd		= new gd;			// GD Functions (for graphical outputs)

if ($_GET['design'] != 'base') $sitetool	= new sitetool("");		// Sitetool (for compressing the content send to the browser)

$dsp		= new display();		// Display Functions (to load the lansuite-templates)
$mail		= new mail();			// Mail Functions (for sending mails to lansuite-users)
$xml		= new xml;			// XML Functions (to maintain XML-Ex-/Imports)
$install	= new Install();		// Install Functions (Some basic Setup-Routines)
$db			= new db;			// DB Functions (to work with the databse)
$sec		= new sec;			// Security Functions (to lock pages)
$cron2	= new cron2();	// Load Cronjob
$seat2 = new seat2();   // Load Seat-Controll Class


// Wenn Install: Connect ohne Abbruch bei Fehler, sonst mit Abbruch
if ($IsAboutToInstall) $db->success = $db->connect(1);
else $db->success = $db->connect(0);


$found_adm = 0;
if ($db->success) {
	$res = $db->query("SELECT userid FROM {$config["database"]["prefix"]}user WHERE type = 3 LIMIT 1");
	if ($db->num_rows($res) > 0) $found_adm = 1;
	$db->free_result($res);

	// Reset DB-Success in Setup if no Adm.-Account was found, because a connection could work, but prefix is wrong
	if (!$found_adm and (($_GET["action"] == "wizard" and $_GET["step"] <= 3) or ($_GET["action"] == "ls_conf"))) $db->success = 0;

	// Load SQL-Tables used by each page
	$install->SetTableNames();

	// Config-Tabelle aulesen
	$cfg = $func->read_db_config();

	$sec->check_blacklist();

  // Fetch all names of active modules
  $ActiveModules = array();
  $res = $db->query("SELECT name FROM {$config["tables"]["modules"]} WHERE active = 1");
  while($row = $db->fetch_array($res)) $ActiveModules[] = $row['name'];
  $db->free_result($res);
  $ActiveModules[] = 'helplet';
  $ActiveModules[] = 'popups';
}

// Set language
if ($_POST['language']) $_SESSION['language'] = $_POST['language'];
elseif ($_GET['language']) $_SESSION['language'] = $_GET['language'];

if ($_SESSION['language']) $language = $_SESSION['language'];
elseif ($cfg["sys_language"]) $language = $cfg["sys_language"];
else $language = "de";

// Load Barcode System
$barcode	= new barcode_system();	// Barcode System

//// Load Base-Lang-File
// 2) Overwrite with $language
if ($language != "de" and file_exists("inc/language/language_$language.php")) include_once("inc/language/language_$language.php");
if ($language != "de" and file_exists("modules/mastersearch/language/mastersearch_lang_$language.php")) include_once("modules/mastersearch/language/mastersearch_lang_$language.php");
if ($language != "de" and file_exists("modules/boxes/language/boxes_lang_$language.php")) include_once("modules/boxes/language/boxes_lang_$language.php");

// Initialize party
$party = new party();

// Set Missingfields to false
$missing_fields = 0;
if ($found_adm) {
	// Startup authentication
	$authentication = new auth();
	$auth = $authentication->GetAuthData();

	// Check, if all required user data fields, are known and force user to add them, if not.
	if ($auth['login'] and $auth['userid'] and $_GET["mod"] != 'install') include_once('modules/usrmgr/missing_fields.php');

} else {
	$auth["type"] = 3;
	$auth["login"] = 1;
}

// Show Blocked Site
if($cfg['sys_blocksite'] == 1 and $auth['type'] < 2) $siteblock = true;

// Set Default-Design, if non is set
if (!$auth["design"]) $auth["design"] = "simple";
if (!file_exists("design/{$auth["design"]}/templates/index_login.htm")) $auth["design"] = "simple";
$_SESSION["auth"]["design"] = $auth["design"];

if ($db->success) {
	$stats = new stats(); // Statistic Functions (for generating server- and usage-statistics)

	// Include base-files
	include_once("modules/sponsor/banner.php");
}

// Boxes
if (!$IsAboutToInstall and !$_GET['contentonly'] and $_GET['design'] != 'base') include_once("modules/boxes/class_boxes.php");

// Info Seite blockiert
if ($cfg['sys_blocksite'] == 1) $func->error($cfg['sys_blocksite_text'], "index.php?mod=install");

// Include Module $_GET["mod"]
if (!$missing_fields and !$siteblock) include_once("index_module.inc.php");

// Output HTML
if ($_GET['contentonly'] or $_GET['design'] == 'base') $index = $templ['index']['info']['content'];
else {
  if ($_SESSION['lansuite']['fullscreen'] and file_exists('design/'. $auth['design'] .'/templates/index_fullscreen.htm')) $index = $func->gettemplate('index_fullscreen');
  else $index = $func->gettemplate('index_login');
}

if ($_GET['design'] != 'base') $sitetool->out_optimizer();
#else echo $index;

// Aktualisierung der Statistik wird erst am Schluss durchgeführt, damit Seitengrösse und Berechnungsdauer eingetragen werden können.
if ($db->success) {
  if ($_GET['design'] != 'base') $stats->update($sitetool->out_work(), $sitetool->get_send_size());

  // Check Cronjobs
  $cron2->CheckJobs();
  $db->disconnect();
}
?>