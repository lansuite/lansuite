<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.1
*	Filename: 			show.php
*	Module: 			Tournamentsystem
*	Main editor: 		jochen@one-network.org
*	Last change: 		20.04.2004
*	Description: 		show a list of existing tournamnets
*	Remarks: 			
*
**************************************************************************/

	$mastersearch = new MasterSearch( $vars, "index.php?mod=tournament2", "index.php?mod=tournament2&action=details&tournamentid=", "" );
	$mastersearch->LoadConfig("tournament", $lang["tourney"]["ms_search"], $lang["tourney"]["ms_result"]);
	$mastersearch->PrintForm();
	$mastersearch->Search();
	$mastersearch->PrintResult();
	
	$templ['index']['info']['content'] .= $mastersearch->GetReturn();
?>
