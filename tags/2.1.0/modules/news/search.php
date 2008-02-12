<?php

	/*************************************************************************
	* 
	*	Lansuite - Webbased LAN-Party Management System
	*	-------------------------------------------------------------------
	*	Lansuite Version:	2.0
	*	File Version:		1.0
	*	Filename: 		search.php
	*	Module: 		news
	*	Main editor: 		Michael@one-network.org
	*	Last change: 		05.02.2003 16:12
	*	Description: 		Searches news
	*	Remarks: 		no bugs reportet, should be ready for release
	*
	******************************************************************************/

	$mastersearch = new MasterSearch( $vars, "index.php?mod=news&action=search", "index.php?mod=news&action=comment&newsid=", "");
	$mastersearch->LoadConfig("news", $lang["news"]["ms_caption"], $lang["news"]["ms_subcaption"]);
	$mastersearch->PrintForm();
	$mastersearch->Search();
	$mastersearch->PrintResult();
	
	$templ['index']['info']['content'] .= $mastersearch->GetReturn();

?>
