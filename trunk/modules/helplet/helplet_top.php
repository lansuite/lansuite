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
*	Filename: 		helplet_top.php
*	Module: 		Helplet
*	Main editor: 		johannes@one-network.org
*	Last change: 		15.12.2002 11:26
*	Description: 		Heplplet top frame
*	Remarks: 		
*
**************************************************************************/

//
// Output HTML
//
eval("\$index .= \"". $func->gettemplate("helplet_show_top")."\";");
$func->templ_output($index);
?>
