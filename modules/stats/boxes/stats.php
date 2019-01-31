<?php
/**
 * Show the statsbox
 */
 
// Number of visits
$total = $db->qry_first("SELECT SUM(visits) AS visits, SUM(hits) AS hits FROM %prefix%stats_usage");

// Ermittle die Anzahl der registrierten Usern
$get_cur = $db->qry_first('SELECT count(userid) as n FROM %prefix%user AS user WHERE user.type > 0');
$reg = $get_cur["n"];
$box->DotRow(t('Benutzer').': '. $reg);

// Avg online, this hour
$avg = $db->qry_first("
  SELECT
    visits,
    hits
  FROM %prefix%stats_usage
  WHERE
    DATE_FORMAT(time, '%Y-%m-%d %H:00:00') = DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 HOUR), '%Y-%m-%d %H:00:00')");
$box->DotRow(t('Besucher').':');
$box->EngangedRow('<span class="infolink">'.number_format($total['visits'], 0, '', '.').'<span class="infobox">'.$total['visits'].' '.t('Besucher insgesamt').'</span></span>&nbsp;<span class="infolink">('.($avg['visits'] ? $avg['visits'] : '0').')<span class="infobox">'.($avg['visits'] ? $avg['visits'] : '0').' '.t('Besucher in der letzten Stunde').'</span></span>');
$box->DotRow(t('Aufrufe').':');
$box->EngangedRow('<span class="infolink">'.number_format($total['hits'], 0, '', '.').'<span class="infobox">'.$total['hits'].' '.t('Seitenzugriffe insgesamt').'</span></span>&nbsp;<span class="infolink">('.($avg['hits'] ? $avg['hits'] : '0').')<span class="infobox">'.($avg['hits'] ? $avg['hits'] : '0').' '.t('Seitenzugriffe in der letzten Stunde').'</span></span>');

$box->DotRow(t('Online') .': '. count($authentication->online_users), 'index.php?mod=guestlist&action=onlineuser');
foreach ($authentication->online_users as $userid) {
    $row = $db->qry_first("SELECT username FROM %prefix%user WHERE userid = %int%", $userid);
    $box->EngangedRow($dsp->FetchUserIcon($userid, $row["username"]));
}

$box->DotRow(t('Untätig') .': '. count($authentication->away_users), 'index.php?mod=guestlist&action=onlineuser');
foreach ($authentication->away_users as $userid) {
    $row = $db->qry_first("SELECT username FROM %prefix%user WHERE userid = %int%", $userid);
    $box->EngangedRow($dsp->FetchUserIcon($userid, $row["username"]));
}
