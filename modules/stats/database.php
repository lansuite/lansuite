<?php
/*************************************************************************
*
*   Lansuite - Webbased LAN-Party Management System
*   -------------------------------------------------------------------
*   Lansuite Version:    2.0
*   File Version:        1.0
*   Filename:       database.php
*   Module:             Statistics
*   Main editor:        christian@one-network.org
*   Last change:        2002-12-15
*   Description:        Shows informations about database.
*   Remarks:
*
**************************************************************************/

$hostname = explode(' ', $db->get_host_info());

$dsp->NewContent(t('Datenbank Statistik'), t('Auf dieser Seite erhälst du Informationen &uuml;ber die Datenbank und aktuelle Leistungsdaten.'));
$dsp->AddDoubleRow(t('Servername'), $hostname[0]);
$dsp->AddDoubleRow(t('Verbindungstyp'), $hostname[2]);

$res = $db->qry('SHOW status');
while ($row = $db->fetch_array($res)) {
    $dsp->AddDoubleRow($row[0], $row[1]);
}
$db->free_result($res);

$dsp->AddBackButton("index.php?mod=stats", "stats/db");
