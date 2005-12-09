<?php

if ($_POST["fid"] != "") $_GET["fid"] = $_POST["fid"];
$mastersearch = new MasterSearch($vars, "index.php?mod=board&action=forum&fid={$_GET["fid"]}", "index.php?mod=board&action=thread&fid={$_GET["fid"]}&tid=", " AND t.fid = {$_GET["fid"]}");
$mastersearch->LoadConfig("board_threads", $lang['board']['ms_post_search'], $lang['board']['ms_post_result']);
$mastersearch->PrintForm();
$mastersearch->Search();
$mastersearch->PrintResult();

$row = $db->query_first("SELECT need_type FROM {$config["tables"]["board_forums"]} WHERE fid={$_GET["fid"]}");
if ($row['need_type'] == 1 and $auth['login'] == 0){
	$new_thread = $lang['board']['only_loggedin_post'];
} else $new_thread = $dsp->FetchButton("index.php?mod=board&action=post&fid={$vars["fid"]}", "new_thread");


$dsp->AddSingleRow($new_thread ." ". $dsp->FetchButton("index.php?mod=board", "back"));
$dsp->AddSingleRow($mastersearch->GetReturn());
$dsp->AddSingleRow($new_thread ." ". $dsp->FetchButton("index.php?mod=board", "back"));
$dsp->AddContent();

?>
