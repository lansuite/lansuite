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

$status = explode(' ', $db->stat());
$hostname = explode(' ', $db->get_host_info());
$uptime = explode(".", round($status[1] / 60 / 60, 2));

$dsp->NewContent(t('Datenbank Statistik'), t('Auf dieser Seite erhalten Sie Informationen &uuml;ber die Datenbank und aktuelle Leistungsdaten.'));

$dsp->AddDoubleRow(t('Servername'), $hostname[0]);
$dsp->AddDoubleRow(t('Verbindungstyp'), $hostname[2]);
$dsp->AddDoubleRow(t('L&auml;uft seit'), $uptime[0] . " " . t('Stunde(n)') . " " . round($uptime[1]*0.6) . " " . t('Minute(n)'));
$dsp->AddDoubleRow(t('Abfragen Insg.'), $status[7]);
$dsp->AddDoubleRow(t('Abfragen / Sekunde'), round($status[7] / $status[1], 3)); // $status[27]
$dsp->AddDoubleRow(t('Offene Tabellen'), $status[22]);
$dsp->AddDoubleRow(t('Threads'), $status[4]);

$dsp->AddBackButton("index.php?mod=stats", "stats/db");
$dsp->AddContent();

?>