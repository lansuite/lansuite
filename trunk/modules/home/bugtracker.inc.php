<?php

function CheckBugNew($last_change, $last_read) {
  global $db, $config, $auth, $line;

  // Delete old entries
  $db->qry('DELETE FROM %prefix%bugtracker_lastread WHERE DATEDIFF(NOW(), date) > 7');

  // Older, than one week
  if ($last_change < (time() - 60 * 60 * 24 * 7)) return 0;

  // No entry -> Thread completely new
  elseif (!$last_read) return 1;

  // Entry exists
  else {

    // The posts date is newer than the mark -> New
    if ($last_read < $last_change) return 1;

    // The posts date is older than the mark -> Old
    else return 0;
  }
}

$templ['home']['show']['item']['info']['caption'] = t('Neue Bugs und Feature Wünsche');
$templ['home']['show']['item']['control']['row'] = "";

$query = $db->query("SELECT b.*, UNIX_TIMESTAMP(b.changedate) AS changedate, COUNT(c.relatedto_id) AS comments, UNIX_TIMESTAMP(r.date) as LastRead FROM {$config["tables"]["bugtracker"]} AS b
  LEFT JOIN {$config["tables"]["comments"]} AS c ON (c.relatedto_id = b.bugid AND c.relatedto_item = 'BugEintrag')
  LEFT JOIN {$config["tables"]["bugtracker_lastread"]} AS r ON b.bugid = r.bugid AND r.userid = ". (int)$auth['userid'] ."
  WHERE b.state <= 3
  GROUP BY b.bugid
  ORDER BY b.changedate DESC
  LIMIT 0,{$cfg['home_item_count']}
  ");


if ($db->num_rows($query) > 0) while($row = $db->fetch_array($query)) {
  $templ['home']['show']['row']['control']['link'] = "index.php?mod=bugtracker&bugid={$row['bugid']}";

  $templ['home']['show']['row']['info']['text']		= $func->CutString($row['caption'], 40) .' ['. $row['comments'] .']';
  if (CheckBugNew($row['changedate'], $row['LastRead'])) $templ['home']['show']['item']['control']['row']	.= $dsp->FetchModTpl('home', 'show_row_new');
  else $templ['home']['show']['item']['control']['row']	.= $dsp->FetchModTpl('home', 'show_row');
} else $templ['home']['show']['item']['control']['row'] = "<i>". t('Keine Einträge vorhanden') ."</i>";

?>
