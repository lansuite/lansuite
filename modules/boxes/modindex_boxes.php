<?php 
$LSCurFile = __FILE__;

// BOXES CONTROLFILE
include("modules/boxes/class_boxes.php");
$box = new boxes();

// Navigationsmenü
include("modules/boxes/menu.php");

// Suche
if ($cfg['search_box']) include("modules/boxes/search.php");

// Info-Box
if ((!$cfg["sys_internet"]) && ($auth['login'])) include("modules/boxes/infobox.php");

// Zuletzt angemeldete Benutzer
if ($cfg["sys_internet"] and $cfg["signon_last_user_box"]) include("modules/boxes/last_user.php");

//  Messenger
if ((!$cfg["sys_internet"] or $cfg["msgsys_alwayson"]) and $auth['login'] and in_array('msgsys', $ActiveModules)) include("modules/boxes/messenger.php");

//  Sponsoren
if ($cfg["sponsor_show_box"] and in_array('sponsor', $ActiveModules)) include("modules/boxes/sponsor.php");

// Kontostand
if (!$cfg["sys_internet"] and $auth['login'] and in_array('cashmgr', $ActiveModules)) include("modules/boxes/cashmgr.php");

// Benutzerdaten-, bzw. Login-Box
if ($auth['login']) include("modules/boxes/userdata.php");
else include("modules/boxes/login.php");

// Anmeldestatus
if ($cfg["sys_internet"] and $party->count > 0) include("modules/boxes/signonstatus.php");

// Stats
if (in_array('stats', $ActiveModules)) include("modules/boxes/stats.php");

// WWCL (Die WWCL-Box soll bei jeder Veranstaltung mit mind. einem WWCL-Spiel auf allen Seiten des Turnier-Moduls erscheinen)
$t_wwcl = $db->query_first("SELECT name FROM {$config["tables"]["tournament_tournaments"]} WHERE wwcl_gameid > 0 AND party_id = '{$party->party_id}'");
if ($t_wwcl['name'] != '' and $_GET['mod'] == 'tournament2') include("modules/boxes/wwcl.php");

// Home module boxes
if ($cfg['home_recent_news_box']) {
  include('modules/home/news.inc.php');
  $templ['box']['rows'] = $templ['home']['show']['item']['control']['row'];
  $boxes['recent_news'] = $box->CreateBox('recent_news', t('Die letzten News'));
}
if ($cfg['home_recent_board_box'] and in_array('board', $ActiveModules)) {
  include('modules/home/board.inc.php');
  $templ['box']['rows'] = $templ['home']['show']['item']['control']['row'];
  $boxes['recent_board'] = $box->CreateBox('recent_board', t('Neu im Board'));
}
if ($cfg['home_recent_server_box'] and in_array('server', $ActiveModules)) {
  include('modules/home/server.inc.php');
  $templ['box']['rows'] = $templ['home']['show']['item']['control']['row'];
  $boxes['recent_server'] = $box->CreateBox('recent_server', t('Neue Server'));
}
if ($cfg['home_recent_poll_box'] and in_array('poll', $ActiveModules)) {
  include('modules/home/poll.inc.php');
  $templ['box']['rows'] = $templ['home']['show']['item']['control']['row'];
  $boxes['recent_poll'] = $box->CreateBox('recent_poll', t('Neue Umfragen'));
}

// Define a Box to the left or right site
$templ['index']['control']['boxes_letfside']	.= $boxes['menu'];
$templ['index']['control']['boxes_letfside']	.= $boxes['recent_news'];
$templ['index']['control']['boxes_letfside']	.= $boxes['recent_board'];
$templ['index']['control']['boxes_letfside']	.= $boxes['recent_server'];
$templ['index']['control']['boxes_letfside']	.= $boxes['recent_poll'];
$templ['index']['control']['boxes_letfside']	.= $boxes['wwcl'];
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
