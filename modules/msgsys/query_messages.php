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


$templ['messenger']['query']['messages']['refreshtime'] = $config["size"]["msgrefreshtime"];

// 
// Set all msgs to status 0
//
$query = $db->qry("
UPDATE %prefix%messages 
SET new   = '0' 
WHERE senderid = %int% AND receiverid = %int%
", $_GET[queryid], $_SESSION[auth][userid]);

// 
// Select msgs
//

$query = $db->qry("
  SELECT text, timestamp, senderid
  FROM %prefix%messages 
  WHERE (senderid = %int% AND receiverid = %int%)
  OR (senderid = %int%  AND receiverid = %int%)
  ORDER BY timestamp
  ", $_SESSION[auth][userid], $_GET[queryid], $_GET[queryid], $_SESSION[auth][userid]);

$row2 = $db->qry_first("
  SELECT username
  FROM %prefix%user 
  WHERE userid = %int%
  ", $_GET[queryid]);

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
$index .= $dsp->FetchTpl("design/templates/messenger_query_messages.htm");
$func->templ_output($index);
?>
