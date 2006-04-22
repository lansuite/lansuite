<?php

$dsp->NewContent($lang["stats"]["user_caption"], $lang["stats"]["user_subcaption"]);

$visits = $db->query_first("SELECT COUNT(visits) AS insg FROM {$config["tables"]["stats_usage"]}");
$dsp->AddDoubleRow($lang["stats"]["user_visits_insg"], $visits["insg"]);

$hits = $db->query_first("SELECT SUM(hits) AS insg FROM {$config["tables"]["stats_usage"]}");
$dsp->AddDoubleRow($lang["stats"]["user_hits_insg"], $hits["insg"]);
$dsp->AddDoubleRow($lang["stats"]["user_hits_avg"], round($hits["insg"] / $visits["insg"], 2));

$visit_timeout = time() - 60*60;
$online = $db->query_first("SELECT COUNT(*) AS insg FROM {$config["tables"]["stats_auth"]} WHERE (lasthit > $visit_timeout)");
$user_online = $db->query("SELECT user.username
		FROM {$config["tables"]["stats_auth"]} AS auth
		LEFT JOIN {$config["tables"]["user"]} AS user ON user.userid = auth.userid
		WHERE (auth.lasthit > $visit_timeout)
		ORDER BY auth.lasthit
		");
$user_list = "";
while ($user = $db->fetch_array($user_online)) {
	$user_list .= $user["username"] . ", ";
}
$user_list = substr($user_list, 0, strlen($user_list) - 2);
$dsp->AddDoubleRow($lang["stats"]["user_user_online"], $online["insg"] . " ($user_list)");

$total_time = $db->query_first("SELECT time, size FROM {$config["tables"]["stats"]}");
$dsp->AddDoubleRow($lang["stats"]["user_time"], $total_time['time'] . " " . $lang["stats"]["sec"]);
$dsp->AddDoubleRow($lang["stats"]["user_size"], $total_time['size'] . " kB");


$db->free_result($res);
#$dsp->AddBackButton("index.php?mod=stats", "stats/user");
$dsp->AddContent();

?>
