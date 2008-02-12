<?php

/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-----------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.0
*	Filename: 		query.php
*	Module: 		Msgsys
*	Main editor: 		johannes@one-network.org
*	Last change: 		04.01.2003 14:52
*	Description:
*	Remarks:
*
**************************************************************************/

if($_SESSION[auth][login] == "1")
{
	//
	// Get username
	//
	$row = $db->query_first("
	SELECT	username
	FROM	{$config['tables']['user']}
	WHERE	userid = '{$vars['queryid']}'");

	$templ['messenger']['query']['info']['username'] = $row['username'];

	//
	// Output HTML
	//
	eval("\$index .= \"". $func->gettemplate("messenger_query_index")."\";");
	$func->templ_output($index);
}
else
{
	//
	// ERROR
	//
	$func->error("NO_LOGIN","");
	echo $templ_index_content;
}
?>