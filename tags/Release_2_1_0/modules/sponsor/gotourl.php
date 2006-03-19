<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.1
*	Filename: 			show.php
*	Module: 			Sponsorpresentation
*	Main editor: 		jochen@one-network.org
*	Last change: 		29.05.2004
*	Description: 		show a list of sponsors
*	Remarks: 			
*
**************************************************************************/

$sponsoren = $db->query("UPDATE {$config['tables']['sponsor']} SET hits = hits + 1 WHERE url='{$_GET["url"]}'");

$dsp->NewContent($lang["sponsor"]["caption"], $lang["sponsor"]["sub_caption"]);
$dsp->AddDoubleRow($lang["sponsor"]["redirect"], "<a href=\"{$_GET["url"]}\">{$_GET["url"]}</a>" . HTML_NEWLINE . "
<script language = \"JavaScript\">window.location.href = \"{$_GET["url"]}\";</script>");
$dsp->AddBackButton("index.php?mod=sponsor", "sponsor/show");
$dsp->AddContent();
?>
