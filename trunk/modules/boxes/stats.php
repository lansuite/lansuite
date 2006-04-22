<?php
$templ['box']['rows'] = '';

// Number of visits
$visits = $db->query_first("SELECT COUNT(visits) AS insg FROM {$config['tables']['stats_usage']}");
$box->DotRow($lang['boxes']['stats_visits'] .': '. $visits['insg']);

// Number of hits
$hits = $db->query_first("SELECT SUM(hits) AS insg FROM {$config['tables']['stats_usage']}");
$box->DotRow($lang['boxes']['stats_hits'] .': '. $hits['insg']);
$box->EmptyRow();

// Numer of users currently online
$visit_timeout = time() - 60*10;
$online = $db->query_first("SELECT COUNT(*) AS insg FROM {$config['tables']['stats_auth']} WHERE (lasthit > $visit_timeout)");
$box->DotRow($lang['boxes']['stats_user_online'] .': '. $online["insg"]);

// Get list of users currently online
$user_online = $db->query("SELECT user.username, user.userid
	FROM {$config['tables']['stats_auth']} AS auth
	LEFT JOIN {$config['tables']['user']} AS user ON user.userid = auth.userid
	WHERE (auth.lasthit > $visit_timeout) AND auth.login = '1' AND user.userid > 0
	GROUP BY user.userid
	ORDER BY auth.lasthit
	LIMIT 5
	");
while ($user = $db->fetch_array($user_online)) $box->EngangedRow($user["username"] .' '. $dsp->FetchUserIcon($user["userid"]));
$db->free_result($user_online);

$boxes['stats'] .= $box->CreateBox("stats", $lang['boxes']['stats']);
?>