<?php
$mastersearch = new MasterSearch($vars, "index.php?mod=board&action=search", "index.php?mod=board&action=thread&tid=", "");
$mastersearch->LoadConfig("thread", $lang['board']['search_thread'], $lang['board']['search_result']);
$mastersearch->PrintForm();
$mastersearch->Search();
$mastersearch->PrintResult();

$templ['index']['info']['content'] .= $mastersearch->GetReturn();
?>
