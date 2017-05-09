<?php

$smarty->assign('caption', t('Die nächsten Partys'));
$content = "";

$query = $db->qry("SELECT p.partyid, p.name, UNIX_TIMESTAMP(p.start) as start FROM %prefix%partylist AS p
  WHERE p.end >= NOW()
  ORDER BY p.start ASC
  LIMIT 0,%int%
  ", $cfg['home_item_cnt_partylist']);

if ($db->num_rows($query) > 0) {
    while ($row = $db->fetch_array($query)) {
        $smarty->assign('link', "index.php?mod=partylist&partyid={$row['partyid']}");
        $smarty->assign('text', $func->CutString($row['name'], 25));
        $smarty->assign('text2', ' ['. $func->unixstamp2date($row['start'], 'date') .']');
        $content .= $smarty->fetch('modules/home/templates/show_row.htm');
    }
} else {
    $content = "<i>". t('Keine Einträge vorhanden') ."</i>";
}
