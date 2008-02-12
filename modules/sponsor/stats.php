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

$dsp->NewContent($lang["sponsor"]["stats_caption"], $lang["sponsor"]["stats_sub_caption"]);
$sponsoren = $db->query("SELECT name, url, pic_path, text, views, hits FROM {$config['tables']['sponsor']}");
while ($sponsor = $db->fetch_array($sponsoren)){
	$dsp->AddSingleRow("<a href=\"{$sponsor["url"]}\" traget=\"_blank\">{$sponsor["name"]}</a>");
	$dsp->AddDoubleRow($lang["sponsor"]["stats_views"], $sponsor["views"] ."x");
	$dsp->AddDoubleRow($lang["sponsor"]["stats_hits"], $sponsor["hits"] ."x");
	($sponsor["views"] > 0)? $dsp->AddDoubleRow($lang["sponsor"]["stats_rate"], round($sponsor["hits"] / $sponsor["views"], 4) * 100 ."%")
	: $dsp->AddDoubleRow($lang["sponsor"]["stats_rate"], "---");
}
$db->free_result($sponsoren);
$dsp->AddBackButton("index.php?mod=sponsor", "sponsor/show");
$dsp->AddContent();
?>
