<?php

$dsp->NewContent(t('Anmeldestatistik'), t('Hier sehen Sie die aktuelle Statistik zur laufenden LAN'));

// Ermittle die Anzahl der derzeit angemeldeten Usern
$get_cur = $db->query_first("SELECT count(userid) as n FROM {$config["tables"]["user"]} AS user LEFT JOIN {$config["tables"]["party_user"]} AS party ON user.userid = party.user_id WHERE party_id='{$party->party_id}' AND ($querytype)");
$cur = $get_cur["n"];

// Wieviele davon haben bezahlt
$get_cur = $db->query_first("SELECT count(userid) as n FROM {$config["tables"]["user"]} AS user LEFT JOIN {$config["tables"]["party_user"]} AS party ON user.userid = party.user_id WHERE ($querytype) AND (party.paid > 0) AND party_id='{$party->party_id}'");
$paid = $get_cur["n"];

// Ermittel die derzeitige Zeitdifferenz zwischen Startdatum und Heute
$party_date = $db->query_first("SELECT DATEDIFF(startdate, NOW()) AS timetoleft FROM {$config["tables"]["partys"]} WHERE party_id = '{$party->party_id}'");

$dsp->AddDoubleRow(t('Ben&ouml;tigte Anmeldung pro Tag'), round((($_SESSION['party_info']['max_guest']-$cur)/$party_date['timetoleft']),2));
$dsp->AddDoubleRow(t('Ben&ouml;tigte Bezahlungen pro Tag'), round((($_SESSION['party_info']['max_guest']-$paid)/$party_date['timetoleft']),2));

// Ermittel die derzeitige Zeitdifferenz zwischen Startdatum und Heute
$party_date = $db->query_first("SELECT DATEDIFF(startdate, NOW()) AS timetoleft FROM {$config["tables"]["partys"]} WHERE party_id = '{$party->party_id}'");

// Ausgabe der Anmeldeliste
$res = $db->query("SELECT p.name, DATEDIFF(p.startdate, p.sstartdate) AS anmeldetage, COUNT(u.user_id) AS angemeldet FROM {$config["tables"]["party_user"]} AS u
			LEFT JOIN {$config["tables"]["partys"]} AS p ON u.party_id = p.party_id
			WHERE DATEDIFF(p.startdate, u.signondate) >= {$party_date['timetoleft']}
  			GROUP BY p.party_id
			");
$dsp->AddFieldsetStart(t('Vergangene Anmeldezahlen'));
$dsp->AddSingleRow($party_date['timetoleft']." Tag(e) vor Eventbeginn");
while ($row = $db->fetch_array($res)) {
  $dsp->AddDoubleRow($row['name'], $row['angemeldet']. " (".$row['anmeldetage']. " Tage zur Anmeldung verf&uuml;gbar)" );
}
$dsp->AddFieldsetEnd();
$db->free_result($res);
$dsp->AddContent();
?>