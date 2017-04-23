<?php
$smarty->assign('caption', t('Verleih'));
$content = '';
  
// Additional Admin-Stats
if ($auth["type"] >= 2) {
    # send out
    $row8 = $db->qry_first("SELECT count(*) as n FROM %prefix%rentuser WHERE back_orgaid = '' AND out_orgaid != ''");

  # got back
  #$row9 = $db->qry_first("SELECT count(*) as n FROM %prefix%rentuser WHERE back_orgaid > '0' AND out_orgaid > '0'");

  #total equip
    $row9 = $db->qry_first("SELECT sum(quantity) as n FROM %prefix%rentstuff");
    $content .= t('Verleih') .": ".$row8["n"]." / ".$row9["n"] . HTML_NEWLINE;
}
