<?php

$smarty->assign('caption', t('Troubletickets'));
$content = '';
  
// Additional Admin-Stats
if ($auth["type"] >= 2) {
    $row6 = $db->qry_first("SELECT COUNT(*) AS n FROM %prefix%troubleticket WHERE target_userid = '0'");
    $row7 = $db->qry_first("SELECT COUNT(*) AS n FROM %prefix%troubleticket");
    $content .= t('Troubletickets') .": ".$row6["n"]." / ".$row7["n"] . HTML_NEWLINE;
}
