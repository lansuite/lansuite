<?php

$smarty->assign('caption', t('Neue Bugs und Feature Wünsche'));
$content = "";

$query = $db->qry("SELECT b.*, UNIX_TIMESTAMP(b.changedate) AS changedate, COUNT(c.relatedto_id) AS comments FROM %prefix%bugtracker AS b
  LEFT JOIN %prefix%comments AS c ON (c.relatedto_id = b.bugid AND c.relatedto_item = 'BugEintrag')
  WHERE b.state <= 3
  GROUP BY b.bugid
  ORDER BY b.changedate DESC
  LIMIT 0, %int%
  ", $cfg['home_item_count']);

if ($db->num_rows($query) > 0) while($row = $db->fetch_array($query)) {
  $templ['home']['show']['row']['control']['link'] = "index.php?mod=bugtracker&bugid={$row['bugid']}";

  $templ['home']['show']['row']['info']['text']		= $func->CutString($row['caption'], 40) .' ['. $row['comments'] .']';
  if ($func->CheckNewPosts($row['changedate'], 'bugtracker', $row['bugid'])) $content	.= $dsp->FetchModTpl('home', 'show_row_new');
  else $content	.= $dsp->FetchModTpl('home', 'show_row');
} else $content = "<i>". t('Keine Einträge vorhanden') ."</i>";
?>