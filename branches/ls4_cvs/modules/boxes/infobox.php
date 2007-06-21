<?php
$templ['box']['rows'] = "";

if($auth['login'] == 1) {

	// Checkout infos
	$halfanhour = date("U") - 30*60;

	$query = $db->query("SELECT	userid, text, priority, date
		FROM {$config["tables"]["infobox"]}
		WHERE userid = '{$_SESSION["auth"]["userid"]}' AND date > $halfanhour
		ORDER BY priority DESC, date DESC
		LIMIT 0,3
		");

	while ($row=$db->fetch_array()) {
		if ($row["priority"] == "1") 	$class = "row_value";
		elseif ($row["priority"] == "2") $class = "row_value_highlighted";
		elseif ($row["priority"] == "3") $class = "row_value_important";

		$box->EngangedRow("<i>" . $func->unixstamp2date($row["date"], "datetime") . "</i>", "", "", $class);
		$box->EngangedRow("<i>" . $row["text"], "", "", $class);
	}

	if($db->num_rows() < "1") {
		$box->EngangedRow("<i>". t('Keine Einträge in den '). HTML_NEWLINE .t('letzten 30 Minuten'). "</i>", "", "");
	}
}
?>