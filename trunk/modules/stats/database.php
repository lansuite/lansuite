<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	 2.0
*	File Version:		 1.0
*	Filename: 		database.php
*	Module: 			Statistics
*	Main editor: 		christian@one-network.org
*	Last change: 		2002-12-15
*	Description: 		Shows informations about database.
*	Remarks: 		
*
**************************************************************************/

$status = explode(' ', mysql_stat());
$hostname = explode(' ', mysql_get_host_info());
$uptime = explode(".", round($status[1] / 60 / 60, 2));

$dsp->NewContent($lang["stats"]["db_caption"], $lang["stats"]["db_subcaption"]);

$dsp->AddDoubleRow($lang["stats"]["db_servername"], $hostname[0]);
$dsp->AddDoubleRow($lang["stats"]["db_contype"], $hostname[2]);
$dsp->AddDoubleRow($lang["stats"]["db_uptime"], $uptime[0] . " " . $lang["stats"]["hour"] . " " . round($uptime[1]*0.6) . " " . $lang["stats"]["min"]);
$dsp->AddDoubleRow($lang["stats"]["db_querys"], $status[7]);
$dsp->AddDoubleRow($lang["stats"]["db_querys_ps"], round($status[7] / $status[1], 3)); // $status[27]
$dsp->AddDoubleRow($lang["stats"]["db_open_tables"], $status[22]);
$dsp->AddDoubleRow($lang["stats"]["db_threads"], $status[4]);

$dsp->AddBackButton("index.php?mod=stats", "stats/db");
$dsp->AddContent();

?>
