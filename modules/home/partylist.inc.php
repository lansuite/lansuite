<?php

$templ['home']['show']['item']['info']['caption'] = t('Die nächsten Partys');
$templ['home']['show']['item']['control']['row'] = "";

$query = $db->query("SELECT p.partyid, p.name, UNIX_TIMESTAMP(p.start) as start FROM {$config["tables"]["partylist"]} AS p
  WHERE p.end >= NOW()
  ORDER BY p.start ASC
  LIMIT 0,{$cfg['home_item_count']}
  ");

if ($db->num_rows($query) > 0) while($row = $db->fetch_array($query)) {
  $templ['home']['show']['row']['control']['link'] = "index.php?mod=partylist&partyid={$row['partyid']}";
  if (strlen($row['name']) > 40) $row['name'] = substr($row['name'], 0, 25).'...';
  $templ['home']['show']['row']['info']['text']		= $row['name'] .' ['. $func->unixstamp2date($row['start'], 'date') .']';
  $templ['home']['show']['item']['control']['row']	.= $dsp->FetchModTpl('home', 'show_row');
} else {
	$templ['home']['show']['row']['text']['info']['text'] = "<i>". t('Keine Einträge vorhanden') ."</i>";
	$templ['home']['show']['item']['control']['row'] .= $dsp->FetchModTpl('home', 'show_row_text');
}

?>