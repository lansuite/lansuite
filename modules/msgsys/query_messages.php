<?php
/*************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	-----------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.0
*	Filename: 		query_messages.php
*	Module: 		Msgsys
*	Main editor: 		johannes@one-network.org, raphael@one-network.org
*	Last change: 		12.03.2003 18:26
*	Description: 		 
*	Remarks: 		
*
**************************************************************************/


$refreshtime = $config["size"]["msgrefreshtime"];

// 
// Set all msgs to status 0
//
$query = $db->query("
UPDATE	{$config[tables][messages]} 
SET	new 	 = '0' 
WHERE	senderid = '$_GET[queryid]' AND receiverid = '{$_SESSION[auth][userid]}'
");

// 
// Select msgs
//

$query = $db->query("
		SELECT	text, timestamp, senderid
		FROM	{$config[tables][messages]}	
		WHERE	(senderid = '{$_SESSION[auth][userid]}'	AND receiverid = '$_GET[queryid]')
		OR	(senderid = '$_GET[queryid]'		AND receiverid = '{$_SESSION[auth][userid]}')
		ORDER BY timestamp
		");

$row2 = $db->query_first("
		SELECT	username
		FROM	{$config[tables][user]}	
		WHERE	userid = '$_GET[queryid]'
		");

while($row = $db->fetch_array($query))
{
	$senderid	= $row["senderid"];
	$timestamp	= $row["timestamp"];
	$text		= $row["text"];
			
	$text = $func->text2html($text);
	$date = $func->unixstamp2date($timestamp,"time");
	
	if($senderid == $_SESSION[auth][userid])
	{ 
		$class	= "tbl_blue"; 
		$templ['messenger']['query']['messages']['info']['msgs'] .= "<div class=\"$class\"><b>".$_SESSION[auth][username]." ($date):</b></div> $text".HTML_NEWLINE . HTML_NEWLINE;
	} 
	else 
	{
		$class	= "tbl_red"; 
		$templ['messenger']['query']['messages']['info']['msgs'] .= "<div class=\"$class\"><b>".$row2[username]." ($date):</b></div> $text".HTML_NEWLINE . HTML_NEWLINE;
	}
	
	

} // while

//
// Set anchor
//
$templ['messenger']['query']['messages']['info']['msgs'] .= "<a name=\"end\"></a>";

// 
// Output HTML
//
eval("\$index .= \"". $func->gettemplate("messenger_query_messages")."\";");
$func->templ_output($index);
?>
