<?php
$templ['box']['rows'] = '';

// Number of visits
$total = $db->query_first("SELECT SUM(visits) AS visits, SUM(hits) AS hits FROM {$config['tables']['stats_usage']}");

// Ermittle die Anzahl der registrierten Usern
$get_cur = $db->query_first("SELECT count(userid) as n FROM {$config["tables"]["user"]} AS user WHERE user.type > 0");
$reg = $get_cur["n"];
  $box->DotRow(t('Benutzer').': '. $reg);


// Avgerage online, this hour
$avg = $db->query_first("SELECT SUM(visits) AS visits, SUM(hits) AS hits FROM {$config["tables"]["stats_usage"]}
  WHERE DATE_FORMAT(time, '%Y-%m-%d %H:00:00') = DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 HOUR), '%Y-%m-%d %H:00:00')
	");
$box->DotRow(t('Besucher').':');
$box->EngangedRow('<span onmouseover="return overlib(\''. t('Besucher insgesamt') .'\');" onmouseout="return nd();">'. $total['visits'] .'</span> <span onmouseover="return overlib(\''. t('Besucher in der letzten Stunde') .'\');" onmouseout="return nd();">('. $avg['visits'] .')</span>');
$box->DotRow(t('Aufrufe').':');
$box->EngangedRow('<span onmouseover="return overlib(\''. t('Seitenzugriffe insgesamt') .'\');" onmouseout="return nd();">'. $total['hits'] .'</span> <span onmouseover="return overlib(\''. t('Seitenzugriffe in der letzten Stunde') .'\');" onmouseout="return nd();">('. $avg['hits'] .')</span>');

// Get list of users currently online
$user_online = $db->query("SELECT SQL_CALC_FOUND_ROWS user.username, user.userid
	FROM {$config['tables']['stats_auth']} AS auth
	LEFT JOIN {$config['tables']['user']} AS user ON user.userid = auth.userid
	WHERE (auth.lasthit > ". (time() - 60 * 10) .") AND auth.login = '1' AND user.userid > 0 AND user.type > 0
	GROUP BY user.userid
	ORDER BY auth.lasthit
	LIMIT 10
	");
$online = $db->query_first('SELECT FOUND_ROWS() AS count');
$box->DotRow(t('Eingeloggt') .': '. $online['count']);
while ($user = $db->fetch_array($user_online)) $box->EngangedRow($user["username"] .' '. $dsp->FetchUserIcon($user["userid"]));
$db->free_result($user_online);

?>
