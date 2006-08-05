<?php
$templ['box']['rows'] = '';

// Number of visits
$total = $db->query_first("SELECT SUM(visits) AS visits, SUM(hits) AS hits FROM {$config['tables']['stats_usage']}");
$box->DotRow($lang['boxes']['stats_visits'] .': '. $total['visits']);

// Avgerage online, this hour
$avg = $db->query_first("SELECT SUM(visits) AS visits, SUM(hits) AS hits FROM {$config["tables"]["stats_usage"]}
  WHERE time = ". (floor(time() / (60 * 60)) - 1)
	);
$box->DotRow($lang['boxes']['avg_last_h'] .': '. round($avg['visits'] / 6, 1));

// Number of hits
$box->DotRow($lang['boxes']['stats_hits'] .': '. $total['hits']);
$box->DotRow($lang['boxes']['avg_last_h'] .': '. round($avg['hits'] / 6, 1));
$box->EmptyRow();

// Get list of users currently online
$user_online = $db->query("SELECT SQL_CALC_FOUND_ROWS user.username, user.userid
	FROM {$config['tables']['stats_auth']} AS auth
	LEFT JOIN {$config['tables']['user']} AS user ON user.userid = auth.userid
	WHERE (auth.lasthit > ". (time() - 60 * 10) .") AND auth.login = '1' AND user.userid > 0
	GROUP BY user.userid
	ORDER BY auth.lasthit
	LIMIT 5
	");
$online = $db->query_first('SELECT FOUND_ROWS() AS count');
$box->DotRow($lang['boxes']['stats_user_online'] .': '. $online['count']);
while ($user = $db->fetch_array($user_online)) $box->EngangedRow($user["username"] .' '. $dsp->FetchUserIcon($user["userid"]));
$db->free_result($user_online);

$boxes['stats'] .= $box->CreateBox("stats", $lang['boxes']['stats']);
?>