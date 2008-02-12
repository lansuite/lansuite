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
		$mastersearch = new MasterSearch($vars, "index.php?mod=sponsor&action=delete", "index.php?mod=sponsor&action=delete&step=2&sponsorid=", "");
		$mastersearch->LoadConfig("sponsor", "", $lang["sponsor"]["del_ms"]);
		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();
		$templ['index']['info']['content'] .= $mastersearch->GetReturn();
	break;

	case 2:
		$sponsor = $db->query_first("SELECT name FROM {$config['tables']['sponsor']} WHERE sponsorid=$sponsorid");
		$func->question(str_replace("%NAME%", $sponsor['name'], $lang["sponsor"]["del_confirm"]), "index.php?mod=sponsor&action=delete&step=3&sponsorid=$sponsorid", "index.php?mod=sponsor&action=delete");
	break;

	case 3:
		$sponsor = $db->query_first("SELECT name FROM {$config['tables']['sponsor']} WHERE sponsorid=$sponsorid");
		$db->query("DELETE FROM {$config['tables']['sponsor']} WHERE sponsorid=$sponsorid");
		$func->confirmation(str_replace("%NAME%", $sponsor["name"], $lang["sponsor"]["del_success"]), "index.php?mod=sponsor&action=delete");
	break;
}

?>
