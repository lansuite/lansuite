<?php
$templ['box']['rows'] = "";
$box->DotRow(t('Zuletzt angemeldet').':');

$qry = $db->qry('SELECT userid, username FROM %prefix%user WHERE type > 0 ORDER BY userid DESC LIMIT 0,5');
while ($row = $db->fetch_array($qry)) {
	if (strlen($row["username"]) > 12) $row["username"] = substr($row["username"], 0, 10) . "...";
	$box->EngangedRow("<b>{$row["username"]}</b> ". $dsp->FetchUserIcon($row["userid"]));
}
$db->free_result($qry);
?>
