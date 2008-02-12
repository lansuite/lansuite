<?
$dsp->NewContent("Lansin-TV (tm) - Stats", "");

$ges_uploads = $db->query_first("SELECT id FROM {$config['tables']['lansintv']} ORDER by id DESC LIMIT 1;");

if (!$ges_uploads["id"]) {
	$dsp->AddSingleRow("Es wurden noch keine Clips hochgeladen.");
} else {
        $dsp->AddSingleRow("Insgesamt wurden bissher: {$ges_uploads["id"]} Clips hochgeladen");
}

$dsp->AddHRuleRow();
$dsp->AddSingleRow("<br><i>Statistik f&uuml; Uploads:</i>");
$get_data = $db->query("SELECT ltvu.uploads, u.username
		FROM {$config['tables']['lansintv_user']} AS ltvu
		LEFT JOIN {$config['tables']['user']} AS u ON u.userid = ltvu.userid
		WHERE ltvu.banned = 0 AND ltvu.uploads >= 1
		ORDER BY ltvu.uploads DESC
		LIMIT 20");
while($row = $db->fetch_array($get_data)) {
	$dsp->AddDoubleRow($row["username"], "Uploads: " . $row["uploads"]);
}



$dsp->AddHRuleRow();
$dsp->AddSingleRow("<br><br><i>Statistik f&uuml; Votes:</i>");
$get_data = $db->query("SELECT ltvu.votes, u.username
		FROM {$config['tables']['lansintv_user']} AS ltvu
		LEFT JOIN {$config['tables']['user']} AS u ON u.userid = ltvu.userid
		WHERE ltvu.banned = 0 AND ltvu.votes >= 1
		ORDER BY ltvu.votes DESC
		LIMIT 20");
while($row = $db->fetch_array($get_data)) {
	$dsp->AddDoubleRow($row["username"], "Votes: " . $row["votes"]);
}


$dsp->AddBackButton("?mod=lansintv&action=stats", "lansintv/stats");
$dsp->AddContent();
?>
