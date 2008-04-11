<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.1
*	Filename: 			show.php
*	Module: 			Sponsorpresentation
*	Main editor: 		jochen@one-network.org
*	Last change: 		29.05.2004
*	Description: 		show a list of sponsors
*	Remarks: 			
*
**************************************************************************/

$sponsorid 	= $vars["sponsorid"];
$step = $vars["step"];

switch ($step){
	default:
    include_once('modules/sponsor/search.inc.php');
	break;

	case 2:
		$sponsor = $db->query_first("SELECT name FROM {$config['tables']['sponsor']} WHERE sponsorid=$sponsorid");
		$func->question(str_replace("%NAME%", $sponsor['name'], $lang["sponsor"]["del_confirm"]), "index.php?mod=sponsor&amp;action=delete&amp;step=3&amp;sponsorid=$sponsorid", "index.php?mod=sponsor&amp;action=delete");
	break;

	case 3:
		$sponsor = $db->query_first("SELECT name FROM {$config['tables']['sponsor']} WHERE sponsorid=$sponsorid");
		$db->query("DELETE FROM {$config['tables']['sponsor']} WHERE sponsorid=$sponsorid");
		$func->confirmation(str_replace("%NAME%", $sponsor["name"], $lang["sponsor"]["del_success"]), "index.php?mod=sponsor&amp;action=delete");
	break;
}

?>
