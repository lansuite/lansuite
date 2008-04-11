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
*	Filename: 			overview
*	Module: 			Games
*	Main editor: 		denny@one-network.org
*	Description: 		Overview overall available Games
*	Remarks: 		
*
**************************************************************************/

$dsp->NewContent($lang["games"]["overview_caption"], $lang["games"]["overview_subcaption"]);
$dsp->AddSingleRow($dsp->FetchModTpl("games", "overview"));
$dsp->AddContent();
?>