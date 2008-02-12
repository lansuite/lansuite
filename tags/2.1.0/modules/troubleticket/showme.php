<?php // by denny@esa-box.de

$userid = $auth["userid"];

$mastersearch = new MasterSearch($vars, "index.php?mod=troubleticket&action=showme", "index.php?mod=troubleticket&action=show&step=2&ttid=", " AND t.target_userid = '$userid' AND t.status > '0'");
$mastersearch->LoadConfig($lang['troubleticket']['modulname'],$lang['troubleticket']['ms_search_ticket'],$lang['troubleticket']['ms_ticket_result']);
$mastersearch->PrintForm();
$mastersearch->Search();
$mastersearch->PrintResult();

$templ['index']['info']['content'] .= $mastersearch->GetReturn();
?>
