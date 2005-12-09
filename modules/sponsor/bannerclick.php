<?php
$get_banner = $db->query_first("SELECT url FROM {$config['tables']['sponsor']} WHERE sponsorid = '{$_GET['sponsorid']}'");

if ($get_banner) {
	$db->query("UPDATE {$config['tables']['sponsor']} SET hits = hits + 1 WHERE sponsorid = '{$_GET['sponsorid']}'");
	Header("Location: ". $get_banner["url"]);
} else die("<strong>{$lang["sponsor"]["err_no_banner_id"]}</strong>");
?>
