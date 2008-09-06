<?php

$dsp->NewContent(t('Suchmaschinen'), t('Hier sehen Sie, &uuml;ber welche Suchbegriffe Besucher &uuml;ber Suchmaschinenen auf Ihrer Seite gelandet sind'));

// Generate header menu
$se_name = array ();
$se_name[] = t('Alle');
$query = $db->query("SELECT se FROM {$config["tables"]["stats_se"]} GROUP BY se ORDER BY se");
$i = 1;
while ($row = $db->fetch_array($query)) {
	$se_name[$i] = $row["se"];
	$i++;
}
$db->free_result($res);
$dsp->AddHeaderMenu($se_name, "index.php?mod=stats&action=search_engines", $_GET["headermenuitem"]);

// Show only entries of the selected search engine
$_GET["headermenuitem"]--;
if ($_GET["headermenuitem"] == 0) $where = ""; // Show all
elseif ($se_name[$_GET["headermenuitem"]] != "") $where = "WHERE se = '{$se_name[$_GET["headermenuitem"]]}'"; // Show selected
else $where = ""; // Wrong selection

$query = $db->query("SELECT * FROM {$config["tables"]["stats_se"]} $where ORDER BY hits DESC, term ASC");
while ($row = $db->fetch_array($query)) {
  if (strlen($row['term']) > 30) $row['term'] = '<div class="infolink" style="display:inline">'. substr($row['term'], 0, 28) .'...<span class="infobox">'. $row['term'] .'</span>';
  $dsp->AddDoubleRow($row["term"], $row["hits"] .' Hits bei '. $row["se"] .' ('. $func->unixstamp2date($row["first"], "datetime") .' - '. $func->unixstamp2date($row["last"], "datetime") .')');
}
$db->free_result($res);

$dsp->AddSingleRow($table);

$dsp->AddBackButton("index.php?mod=stats", "stats/se");
$dsp->AddContent();
?>