<?php
$templ['box']['rows'] = "";
$box->DotRow($lang['boxes']['last_user']);

//$qry = $db->query("SELECT u.userid, u.username, MAX(s.logtime) AS t FROM {$config["tables"]["stats_auth"]} AS s LEFT JOIN {$config["tables"]["user"]} AS u ON u.userid=s.userid WHERE s.userid != 0 GROUP BY s.userid ORDER BY t DESC LIMIT 0,5");

$qry = $db->query("SELECT userid, username FROM {$config["tables"]["user"]} WHERE type > 0 ORDER BY userid DESC LIMIT 0,5");
while ($row = $db->fetch_array($qry)) {
	if (strlen($row["username"]) > 12) $row["username"] = substr($row["username"], 0, 10) . "...";
	$box->EngangedRow("<b>{$row["username"]}</b> ". $dsp->FetchUserIcon($row["userid"]));
}
$db->free_result($qry);

$boxes['last_user'] .= $box->CreateBox("last_user",$lang['boxes']['userdata_last_login2']);
?>