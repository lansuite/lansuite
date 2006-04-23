<?php
/*************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	-----------------------------------------------
*
*	(c) 2001-2003 by One-Network.Org
*
*	Lansuite Version:	2.0
*	File Version:		2.0
*	Filename: 		base.php
*	Module: 		Framework
*	Main editor: 		
*	Last change: 		22.06.2003 17:35
*	Description: 		This is the lansuite base file for popups.
*				Each javascript windows based on this file.
*				This file makes all basic functions
*				available - like the index.php but without
*				the index HTML-framework
*	Remarks:
*
**************************************************************************/

// Error Reporting auf "Alles außer Hinweise" setzen
error_reporting(E_ALL ^ E_NOTICE);

//
// Start session-management
//
session_start();

// Laden der Variablen
if (!is_array($_POST)) $_POST = $HTTP_POST_VARS;
if (!is_array($_GET)) $_GET = $HTTP_GET_VARS;
$vars = array_merge((array)$_POST, (array)$_GET);

// Save Path
$script_filename = substr($_SERVER["REQUEST_URI"], strrpos($_SERVER["REQUEST_URI"], "/") + 1, strlen($_SERVER["REQUEST_URI"]));
$script_filename = substr($script_filename, 0, strpos($script_filename, "?"));

// Read config-file
$config	= parse_ini_file("inc/base/config.php", 1);

// Read definition file 
include_once("inc/base/define.php");


$lang = array();

if (!$config) {
	echo HTML_FONT_ERROR. "&Ouml;ffnen oder Lesen der Konfigurations-Datei nicht m&ouml;glich. Lansuite wird beendet." .HTML_NEWLINE . "
	&Uuml;berpr&uuml;fen Sie die Datei <b>config.php</b> im Verzeichnis inc/base/" .HTML_FONT_END;
	exit(); 
}

// Wenn configured = 0: Setup aufrufen
if ($config['environment']['configured'] == 0) {
	$_GET["action"] = "wizard";
	$_GET["mod"] = "install";
	$script_filename = "install.php";
}

// Include base classes
include_once("inc/classes/class_db_mysql.php");
include_once("inc/classes/class_func.php");
include_once("inc/classes/class_auth.php");
include_once("inc/classes/class_display.php");
include_once("inc/classes/class_gd.php");
include_once("inc/classes/class_party.php");
include_once("inc/classes/class_barcode.php");

include_once("modules/install/class_install.php");
if (file_exists("modules/mastersearch/class_mastersearch.php")) include_once("modules/mastersearch/class_mastersearch.php" );
include_once("modules/mail/class_mail.php" );
include_once("modules/seating/class_seat.php");

// Initialize base classes
$func		= new func;			// Base Functions (anything that doesnt belong elsewere)
$gd		= new gd;			// GD Functions (for graphical outputs)

//$sitetool	= new sitetool("");		// Sitetool (for compressing the content send to the browser)

$dsp		= new display();		// Display Functions (to load the lansuite-templates)
$mail		= new mail();			// Mail Functions (for sending mails to lansuite-users)
//$xml		= new xml;			// XML Functions (to maintain XML-Ex-/Imports)
$install	= new Install();		// Install Functions (Some basic Setup-Routines)
$db		= new db;			// DB Functions (to work with the databse)
//$stats		= new stats();			// Statistic Functions (for generating server- and usage-statistics)
$seat2 = new seat2();


$db->connect();

// Load SQL-Tables used by each page
$install->SetTableNames();

// Config-Tabelle aulesen
$cfg = $func->read_db_config();

// Set language
($cfg["sys_language"]) ? $language = $cfg["sys_language"]
	: $language = "de";

// Load Barcodesystem
$barcode 	= new barcode_system();

//// Load Base-Lang-File
include_once("inc/language/language_de.php");
if (file_exists("modules/mastersearch/language/mastersearch_lang_de.php")) include_once("modules/mastersearch/language/mastersearch_lang_de.php");
// 2) Overwrite with $language
if ($language != "de" and file_exists("inc/language/language_$language.php")) include_once("inc/language/language_$language.php");
if ($language != "de" and file_exists("modules/mastersearch/language/mastersearch_lang_$language.php")) include_once("modules/mastersearch/language/mastersearch_lang_$language.php");

$authentication = new auth();
$auth = $authentication->GetAuthData(false);

// Party schreiben 
$party 		= new party();	// initialize Parys
if ($_GET["mod"] != "install"){
	$party->write_party_infos();
}

//
// Define general index vars
//
$templ['index']['info']['current_date']		= $func->unixstamp2date(time(),'daydatetime');
$templ['index']['info']['lanparty_name']	= $config['lanparty']['name'];
$templ['index']['info']['version']		= $config['lansuite']['version'];

//
// Modules
//
include("base_module.inc.php");


?>
