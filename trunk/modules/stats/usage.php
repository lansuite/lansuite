<?php

$dsp->NewContent($lang["stats"]["user_caption"], $lang["stats"]["user_subcaption"]);
$dsp->AddDoubleRow("<b>Time</b>", "<b>Visits (Hits)</b>");

if ($_GET["time"] == "") $_GET["time"] = "year";
if ($_GET["start"] == "") $_GET["start"] = 1;

switch ($_GET["time"]) {
	case "year":
		$multiplier = 24 * 30.4375 * 12;
		$end = 24 * 30.4375 * 12 * 100;
		$format = "year";
		$link = "<a href=\"index.php?mod=stats&action=usage&time=month&start=%TIME%\">%TIME_FORMATED%</a>";
	break;
	case "month":
		$multiplier = 24 * 30.4375;
		$end = 24 * 30.4375 * 12;
		$format = "month";
		$link = "<a href=\"index.php?mod=stats&action=usage&time=day&start=%TIME%\">%TIME_FORMATED%</a>";
	break;
	case "day":
		$multiplier = 24;
		$end = 24 * 30.4375;
		$format = "daydate";
		$link = "<a href=\"index.php?mod=stats&action=usage&time=houre&start=%TIME%\">%TIME_FORMATED%</a>";
	break;
	default:
		$multiplier = 1;
		$end = 24;
		$format = "datetime";
		$link = "%TIME_FORMATED%";
	break;
}

$res = $db->query("SELECT SUM(hits) AS hits, SUM(visits) AS visits, MIN(time) AS time FROM {$config["tables"]["stats_usage"]}
	WHERE time > {$_GET["start"]} AND time < {$_GET["start"]} + $end
	GROUP BY floor(time / ($multiplier))
	ORDER BY time
	");
while ($row = $db->fetch_array($res)) {
	$dsp->AddDoubleRow(str_replace("%TIME%", $row["time"] - 1, str_replace("%TIME_FORMATED%", $func->unixstamp2date($row["time"] * 60 * 60, $format), $link)),
		"{$row["visits"]} ({$row["hits"]})");
}
$db->free_result($res);

$buttons = "";
switch ($_GET["time"]) {
	case "houre":
		$buttons .= $dsp->FetchButton("index.php?mod=stats&action=usage&time=day&start=". floor(floor($_GET["start"] / (24 * 30.4375)) * (24 * 30.4375)), "day") . " ";
	case "day":
		$buttons .= $dsp->FetchButton("index.php?mod=stats&action=usage&time=month&start=". floor(floor($_GET["start"] / (24 * 30.4375 * 12)) * (24 * 30.4375 * 12)), "month") . " ";
	case "month":
		$buttons .= $dsp->FetchButton("index.php?mod=stats&action=usage&time=year&start=1", "year") . " ";
	break;
}
if ($buttons) $dsp->AddDoubleRow("", $buttons);
$dsp->AddBackButton("index.php?mod=stats", "stats/usage");

$dsp->AddContent();
?>