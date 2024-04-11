<?php

$smarty->assign('caption', t('Verleih'));
$content = '';
  
// Additional Admin-Stats
if ($auth['type'] >= \LS_AUTH_TYPE_ADMIN) {
    $row8 = $database->queryWithOnlyFirstRow("SELECT COUNT(*) AS n FROM %prefix%rentuser WHERE back_orgaid = '' AND out_orgaid != ''");

    // total equip
    $row9 = $database->queryWithOnlyFirstRow("SELECT SUM(quantity) AS n FROM %prefix%rentstuff");
    $content .= t('Verleih') .": ".$row8["n"]." / ".$row9["n"] . HTML_NEWLINE;
}
