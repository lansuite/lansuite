<?php
if (($auth["type"] >= 2) or (($auth["userid"] == $_GET["userid"]) && $cfg['user_self_details_change'])) {

$mastersearch = new MasterSearch($vars, "index.php?mod=beamer&action=history", "index.php?mod=beamer&action=delblacklist&userid=", "");
$mastersearch->LoadConfig("beamer_history", "History: Suche", "History: Ergebnis");
$mastersearch->PrintForm();
$mastersearch->Search();
$mastersearch->PrintResult();
$templ['index']['info']['content'] .= $mastersearch->GetReturn();

} else $func->error("ACCESS_DENIED", "");
?>