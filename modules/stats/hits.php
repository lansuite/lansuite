<?php

$dsp->NewContent(t('Benutzerstatistiken'), t('Hier sehen sie wie viele Benutzer ihre Seite besuchen'));

$visits = $db->qry_first("SELECT SUM(visits) AS insg FROM %prefix%stats_usage");
$dsp->AddDoubleRow(t('Besucher (Visits)'), $visits["insg"]);

$hits = $db->qry_first("SELECT SUM(hits) AS insg FROM %prefix%stats_usage");
$dsp->AddDoubleRow(t('Seitenaufrufe (Hits)'), $hits["insg"]);
$dsp->AddDoubleRow(t('Seiten pro Besucher'), round($hits["insg"] / $visits["insg"], 2));

$visit_timeout = time() - 60*60;
$online = $db->qry_first("SELECT SUM(visits) AS insg FROM %prefix%stats_auth WHERE (lasthit > %int%)", $visit_timeout);
$user_online = $db->qry("SELECT user.username
  FROM %prefix%stats_auth AS auth
  LEFT JOIN %prefix%user AS user ON user.userid = auth.userid
  WHERE (auth.lasthit > %int%)
  ORDER BY auth.lasthit
  ", $visit_timeout);
$user_list = "";
while ($user = $db->fetch_array($user_online)) {
    $user_list .= $user["username"] . ", ";
}
$user_list = substr($user_list, 0, strlen($user_list) - 2);
$dsp->AddDoubleRow(t('Benutzer eingeloggt (letzte Stunde)'), $online["insg"] . " ($user_list)");

$total_time = $db->qry_first("SELECT time, size FROM %prefix%stats");
$dsp->AddDoubleRow(t('Bis jetzt ben&ouml;tigte Zeit f&uuml;r Skript'), $total_time['time'] . " " . t('Sekunde(n)'));
$dsp->AddDoubleRow(t('Bis jetzt &uuml;bertragene Daten'), $total_time['size'] . " kB");

$dsp->AddContent();
