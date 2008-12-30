<?php

$smarty->assign('caption', t('Die nächsten Partys'));
$content = "";

$query = $db->qry("SELECT p.partyid, p.name, UNIX_TIMESTAMP(p.start) as start FROM %prefix%partylist AS p
  WHERE p.end >= NOW()
  ORDER BY p.start ASC
  LIMIT 0,%int%
  ", $cfg['home_item_count']);

if ($db->num_rows($query) > 0) while($row = $db->fetch_array($query)) {
  $templ['home']['show']['row']['control']['link'] = "index.php?mod=partylist&partyid={$row['partyid']}";

  $templ['home']['show']['row']['info']['text']		= $func->CutString($row['name'], 25) .' ['. $func->unixstamp2date($row['start'], 'date') .']';
  $content	.= $dsp->FetchModTpl('home', 'show_row');
} else $content = "<i>". t('Keine Einträge vorhanden') ."</i>";

?>
