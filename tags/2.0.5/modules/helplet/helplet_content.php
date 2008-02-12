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
*	Filename: 		helplet_content.php
*	Module: 		Helplet
*	Main editor: 		johannes@one-network.org
*	Last change: 		15.12.2002 11:24
*	Description: 		Heplplet content frame
*	Remarks: 		
*
**************************************************************************/

//
// Output HTML
//
eval("\$index .= \"". $func->gettemplate("helplet_show_content")."\";");
$func->templ_output($index);

//
// Output helplet content
//
@include("doc/online/$language/" . $_GET['helpletid'] . ".htm");
?>
