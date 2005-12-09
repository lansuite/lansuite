<?php 
// BOXES CONTROLFILE
include("modules/boxes/class_boxes.php");
$box = new boxes();

// Navigationsmenü
include("modules/boxes/menu.php");

// Suche
$module = $db->query_first("SELECT active FROM {$config["tables"]["modules"]} WHERE name = 'search'");
if ($module["active"]) include("modules/boxes/search.php");

// Info-Box
if ((!$cfg["sys_internet"]) && ($auth['login'])) include("modules/boxes/infobox.php");

// Zuletzt angemeldete Benutzer
if ($cfg["sys_internet"] and $cfg["signon_last_user_box"]) include("modules/boxes/last_user.php");

//  Messenger
if (((!$cfg["sys_internet"]) || ($cfg["msgsys_alwayson"])) && ($auth['login'])) {
	$module = $db->query_first("SELECT active FROM {$config["tables"]["modules"]} WHERE name = 'msgsys'");
	if ($module["active"]) include("modules/boxes/messenger.php");
}

//  Sponsoren
if ($cfg["sponsor_show_box"]) {
	$module = $db->query_first("SELECT active FROM {$config["tables"]["modules"]} WHERE name = 'sponsor'");
	if ($module["active"]) include("modules/boxes/sponsor.php");
}

// Kontostand
if ((!$cfg["sys_internet"]) && ($auth['login'])) {
	$module = $db->query_first("SELECT active FROM {$config["tables"]["modules"]} WHERE name = 'cashmgr'");
	if ($module["active"]) include("modules/boxes/cashmgr.php");
}

// Benutzerdaten-, bzw. Login-Box
if ($auth['login']) include("modules/boxes/userdata.php");
else include("modules/boxes/login.php");

// Anmeldestatus
if ($cfg["sys_internet"]) include("modules/boxes/signonstatus.php");

// Stats
$module = $db->query_first("SELECT active FROM {$config["tables"]["modules"]} WHERE name = 'stats'");
if ($module["active"]) include("modules/boxes/stats.php");


// Define a Box to the left or right site 
$templ['index']['control']['boxes_letfside']	.= $boxes['menu'];
$templ['index']['control']['boxes_letfside']	.= $boxes['search'];

$templ['index']['control']['boxes_rightside']	.= $boxes['login'];
$templ['index']['control']['boxes_rightside']	.= $boxes['userdata'];
$templ['index']['control']['boxes_rightside']	.= $boxes['infobox'];
$templ['index']['control']['boxes_rightside']	.= $boxes['signonstatus'];	
$templ['index']['control']['boxes_rightside']	.= $boxes['sponsor'];	
$templ['index']['control']['boxes_rightside']	.= $boxes['last_user'];	
$templ['index']['control']['boxes_rightside']	.= $boxes['messenger'];
$templ['index']['control']['boxes_rightside']	.= $boxes['cashmgr'];
$templ['index']['control']['boxes_rightside']	.= $boxes['stats'];

?>
