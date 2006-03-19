<?php // 18.10.2002 15:02 - Benjamin@lansuite.de

$mastersearch = new MasterSearch($vars, "index.php?mod=server&action=show", "index.php?mod=server&action=show_details&serverid=", "");
$mastersearch->LoadConfig("server", $lang["server"]["ms_search"], $lang["server"]["ms_result"]);
$mastersearch->PrintForm();
$mastersearch->Search();
$mastersearch->PrintResult();

$templ['index']['info']['content'] .= $mastersearch->GetReturn();
?>
