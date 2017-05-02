<?php
$smarty->assign('caption', t('Neue Server'));
$content = "";

if (!$cfg['server_sortmethod']) {
    $cfg['server_sortmethod'] = 'changedate';
}
$query = $db->qry("SELECT serverid, caption, type, UNIX_TIMESTAMP(changedate) AS changedate FROM %prefix%server WHERE (party_id = %int% OR party_id = 0 or party_id = NULL) ORDER BY %string% DESC LIMIT 0, %plain%", $party->party_id, $cfg['server_sortmethod'], $cfg['home_item_cnt_server']);

if ($db->num_rows($query) > 0) {
    while ($row = $db->fetch_array($query)) {
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
