<?php

/*************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	-----------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.0
*	Filename: 		query_console.php
*	Module: 		Msgsys
*	Main editor: 		johannes@one-network.org
*	Last change: 		04.01.2003 15:19
*	Description: 		 
*	Remarks: 		
*
**************************************************************************/

// 
// Add msg
//
if($_GET[action] == "add" && $_POST[text] != "")
{
	$time = time();

	$insert = $db->query("
	INSERT INTO	{$config[tables][messages]} 
	SET 		text='$_POST[text]', timestamp='$time', new='1', senderid='{$_SESSION[auth][userid]}', receiverid='$_GET[queryid]'
	");
}

// 
// Output HTML
//
eval("\$index .= \"". $func->gettemplate("messenger_query_console")."\";");
$func->templ_output($index);
	
?>
