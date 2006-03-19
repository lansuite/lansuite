<?php

$dsp->NewContent($lang["stats"]["se_caption"], $lang["stats"]["se_subcaption"]);

// Generate header menu
$se_name = array ();
$se_name[] = $lang["stats"]["se_all"];
$query = $db->query("SELECT se FROM {$config["tables"]["stats_se"]} GROUP BY se ORDER BY se");
while ($row = $db->fetch_array($query)) {
	$se_name[] = $row["se"];
}
$db->free_result($res);
$dsp->AddHeaderMenu($se_name, "index.php?mod=stats&action=search_engines", $_GET["headermenuitem"]);

// Show only entries of the selected search engine
$_GET["headermenuitem"]--;
if ($_GET["headermenuitem"] == 0) $where = ""; // Show all
elseif ($se_name[$_GET["headermenuitem"]] != "") $where = "WHERE se = '{$se_name[$_GET["headermenuitem"]]}'"; // Show selected
else $where = ""; // Wrong selection

$query = $db->query("SELECT * FROM {$config["tables"]["stats_se"]} $where ORDER BY hits DESC, term ASC");
$table = "<table><tr><th>{$lang["stats"]["se_term"]}</th><th>{$lang["stats"]["se_se"]}</th><th>{$lang["stats"]["se_hits"]}</th><th>{$lang["stats"]["se_first"]}</th><th>{$lang["stats"]["se_last"]}</th></tr>";
while ($row = $db->fetch_array($query)) {
	$table .= "<tr><td>{$row["term"]}</td><td>{$row["se"]}</td><td>{$row["hits"]}</td><td>". $func->unixstamp2date($row["first"], "datetime") ."</td><td>". $func->unixstamp2date($row["last"], "datetime") ."</td></tr>";
}
$table .= "</table>";
$db->free_result($res);

$dsp->AddSingleRow($table);

$dsp->AddBackButton("index.php?mod=stats", "stats/se");
$dsp->AddContent();
?>