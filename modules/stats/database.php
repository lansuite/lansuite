<?php

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
