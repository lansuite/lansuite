<?php

$dsp->NewContent(t('Party-Karte'), t('Partys, die Lansuite verwenden'));

$where_pid = '';
if ($party->party_id) {
    $where_pid = "AND (p.party_id = {$party->party_id})";
}

$res = $db->qry("SELECT *, UNIX_TIMESTAMP(start) AS start, UNIX_TIMESTAMP(end) AS end FROM %prefix%partylist AS p WHERE start > NOW()");

$addresses = '';
if ($res->num_rows > 0) {
    while ($row = $db->fetch_array($res)) {
        $text = "<b>{$row['name']}</b><br />- {$row['motto']} -<br>". $func->unixstamp2date($row['start'], 'datetime') .' - '. $func->unixstamp2date($row['end'], 'datetime') ."<br>{$row['street']} {$row['hnr']}, {$row['plz']} {$row['city']}";
        $addresses .= "showAddress('Germany', '". addslashes($row['city']) ."', '". addslashes($row['plz']) ."', '". addslashes($row['street']) ."', '". addslashes($row['hnr']) ."', '". addslashes($text) ."');\r\n";
    }
    $smarty->assign('addresses', $addresses);
    if (!empty($cfg['google_analytics_id'])) {
        $smarty->assign('apikey', $cfg['google_analytics_id']);
        $dsp->AddSingleRow($smarty->fetch('modules/guestlist/templates/googlemaps.htm'));
    } else {
        $func->error(t('Die Google Analytics ID wurde nicht konfiguriert, ist aber notwendig zur Benutzung von Google Maps-Diensten.'));
    }
} else {
     $func->error(t('Es wurden keine Party-Einträge gefunden'));
}
