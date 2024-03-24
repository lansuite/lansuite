<?php
$smarty->assign('caption', t('Server für die ') . ' ' . $_SESSION['party_info']['name']);
$content = "";

if (!$cfg['server_sortmethod']) {
    $cfg['server_sortmethod'] = 'changedate';
}

$serverRows = $database->queryWithFullResult("
  SELECT
    serverid,
    caption,
    type,
    UNIX_TIMESTAMP(changedate) AS changedate
  FROM %prefix%server
  WHERE
    party_id = ?
  ORDER BY ? DESC
  LIMIT 0, ?", [$party->party_id, $cfg['server_sortmethod'], $cfg['home_item_cnt_server']]);

if (count($serverRows) > 0) {
  foreach ($serverRows as $row) {
    $smarty->assign('link', "index.php?mod=server&action=show_details&serverid={$row["serverid"]}");
    $smarty->assign('text', $func->CutString($row["caption"], 40));
    $smarty->assign('text2', " (".$row["type"].")");

    if ($func->CheckNewPosts($row['changedate'], 'server', $row['serverid'])) {
        $content .= $smarty->fetch('modules/home/templates/show_row_new.htm');
    } else {
        $content .= $smarty->fetch('modules/home/templates/show_row.htm');
    }
  }
} else {
  $content = "<i>". t('Keine Server vorhanden') ."</i>";
}
