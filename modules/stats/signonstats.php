<?php

// With or without admins?
if ($cfg['guestlist_showorga'] == 0) {
    $querytype = 'user.type = 1';
} else {
    $querytype = 'user.type >= 1';
}

$dsp->NewContent(t('Anmeldestatistik'), t('Hier siehst du die aktuelle Statistik zur laufenden LAN'));

// Ermittle die Anzahl der derzeit angemeldeten Usern
$get_cur = $db->qry_first("SELECT count(userid) as n FROM %prefix%user AS user LEFT JOIN %prefix%party_user AS party ON user.userid = party.user_id WHERE party_id=%int% AND (%plain%)", $party->party_id, $querytype);
$cur = $get_cur["n"];

// Wieviele davon haben bezahlt
$get_cur = $db->qry_first("SELECT count(userid) as n FROM %prefix%user AS user LEFT JOIN %prefix%party_user AS party ON user.userid = party.user_id WHERE (%plain%) AND (party.paid > 0) AND party_id=%int%", $querytype, $party->party_id);
$paid = $get_cur["n"];

// Ermittel die derzeitige Zeitdifferenz zwischen Startdatum und Heute
$party_date = $db->qry_first("SELECT DATEDIFF(startdate, NOW()) AS timetoleft FROM %prefix%partys WHERE party_id = %int%", $party->party_id);

if (!$party_date['timetoleft']) {
    $dsp->AddSingleRow(t('Derzeit ist keine Party geplant'));
} else {
    $dsp->AddDoubleRow(t('Ben&ouml;tigte Anmeldung pro Tag'), round((($_SESSION['party_info']['max_guest']-$cur)/$party_date['timetoleft']), 2));
    $dsp->AddDoubleRow(t('Ben&ouml;tigte Bezahlungen pro Tag'), round((($_SESSION['party_info']['max_guest']-$paid)/$party_date['timetoleft']), 2));
}

// Ermittel die derzeitige Zeitdifferenz zwischen Startdatum und Heute
$party_date = $db->qry_first("SELECT DATEDIFF(startdate, NOW()) AS timetoleft FROM %prefix%partys WHERE party_id = %int%", $party->party_id);

// Ausgabe der Anmeldeliste
$res = $db->qry("SELECT p.name, DATEDIFF(p.startdate, p.sstartdate) AS anmeldetage, COUNT(u.user_id) AS angemeldet FROM %prefix%party_user AS u
   LEFT JOIN %prefix%partys AS p ON u.party_id = p.party_id
   WHERE DATEDIFF(p.startdate, u.signondate) >= %int%
     GROUP BY p.party_id
   ", $party_date['timetoleft']);
$dsp->AddFieldsetStart(t('Vergangene Anmeldezahlen'));
$dsp->AddSingleRow($party_date['timetoleft']." Tag(e) vor Eventbeginn");
while ($row = $db->fetch_array($res)) {
    $dsp->AddDoubleRow($row['name'], $row['angemeldet']. " (".$row['anmeldetage']. " Tage zur Anmeldung verf&uuml;gbar)");
}
$dsp->AddFieldsetEnd();
$db->free_result($res);

// Ausgabe der Bezahlliste
$res = $db->qry("SELECT p.name, DATEDIFF(p.startdate, p.sstartdate) AS anmeldetage, COUNT(u.user_id) AS angemeldet FROM %prefix%party_user AS u
   LEFT JOIN %prefix%partys AS p ON u.party_id = p.party_id
   WHERE DATEDIFF(p.startdate, u.signondate) >= %int% AND u.paid > 0
     GROUP BY p.party_id
   ", $party_date['timetoleft']);
$dsp->AddFieldsetStart(t('Vergangene Bezahlungen'));
$dsp->AddSingleRow($party_date['timetoleft']." Tag(e) vor Eventbeginn");
while ($row = $db->fetch_array($res)) {
    $dsp->AddDoubleRow($row['name'], $row['angemeldet']);
}
$dsp->AddFieldsetEnd();
$db->free_result($res);
$dsp->AddContent();
