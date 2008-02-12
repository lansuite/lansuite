<?php

$mastersearch = new MasterSearch($vars, "index.php?mod=board&action=bookmark", "index.php?mod=board&action=thread&fid={$_GET["fid"]}&tid=", " AND b.userid = '{$auth["userid"]}' AND t.caption != ''");
$mastersearch->LoadConfig("board_bookmarks", $lang['board']['ms_bm_search'], $lang['board']['ms_bm_result']);
$mastersearch->PrintForm();
$mastersearch->Search();
$mastersearch->PrintResult();

$templ['index']['info']['content'] .= $mastersearch->GetReturn();

?>
