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
*	Filename: 		helplet.php
*	Module: 		Helplet
*	Main editor: 		johannes@one-network.org
*	Last change: 		15.12.2002 12:02
*	Description: 		Heplplet framework
*	Remarks: 		
*
**************************************************************************/

//
// Define variables
//
$templ['helplet']['show']['index']['control']['helpletid'] = $_GET['helpletid'];

//
// Output HTML
//
eval("\$index .= \"". $func->gettemplate("helplet_show_index")."\";");
$func->templ_output($index);
?>
