<?php
$smarty->assign('caption', t('Server für ') . ' ' . $_SESSION['party_info']['name']);
$content = "";

$serverSortmethod = $cfg['server_sortmethod'] ?? 'changedate';
$serverItemCount = $cfg['home_item_cnt_server'] ?? 5;
$partyId = $party->party_id ?? 0;

$serverRows = $database->queryWithFullResult("
  SELECT
    serverid,
    caption,
    type,
    UNIX_TIMESTAMP(changedate) AS changedate
  FROM %prefix%server
  WHERE
    party_id = ?
  ORDER BY " . $serverSortmethod . " DESC
  LIMIT 0, " . $serverItemCount, [$partyId]);

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
