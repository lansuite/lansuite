<?php

$templ['home']['show']['item']['info']['caption'] = t('Neue Bugs und Feature Wünsche');
$templ['home']['show']['item']['control']['row'] = "";

$query = $db->query("SELECT * FROM {$config["tables"]["bugtracker"]} WHERE state <= 3 ORDER BY date LIMIT 0,8");
if ($db->num_rows($query) > 0) while($row = $db->fetch_array($query)) {
  $templ['home']['show']['row']['control']['link'] = "index.php?mod=bugtracker&bugid={$row['bugid']}";
  if (strlen($row['caption']) > 40) $row['caption'] = substr($row['caption'], 0, 37).'...';
  $templ['home']['show']['row']['info']['text']		= $row['caption'];
  $templ['home']['show']['item']['control']['row']	.= $dsp->FetchModTpl('home', 'show_row');
} else {
	$templ['home']['show']['row']['text']['info']['text'] = "<i>". t('Keine Einträge vorhanden') ."</i>";
	$templ['home']['show']['item']['control']['row'] .= $dsp->FetchModTpl('home', 'show_row_text');
}

?>
