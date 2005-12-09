<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.1
*	Filename: 			show_back.php
*	Module: 			Verleih/Rent
*	Main editor: 		denny@one-network.org
*	Description: 		show all stuff thats back
*	Remarks: 		
*
**************************************************************************/

$step 	 = $vars["step"];
$item_id = $vars["itemid"];
$user_id = $vars["userid"];

switch($step) {

	default:
		$mastersearch = new MasterSearch( $vars, "index.php?mod=rent&action=show_back", "index.php?mod=rent&action=show_back&link=", " AND (ru.back_orgaid != '')");
		$mastersearch->LoadConfig( "rentback", $lang['rent']['show_back_search_eq'], $lang['rent']['show_back_search_result'] );
		$mastersearch->PrintForm(); // such-formular
		$mastersearch->Search(); // init suche
		$mastersearch->PrintResult(); // anzeige
		$templ['index']['info']['content'] .= $mastersearch->GetReturn();

	break;
	
}// switch

?>
