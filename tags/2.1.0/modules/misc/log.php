<?php

switch($_GET["step"]){
	default:
		$mastersearch = new MasterSearch($vars, "index.php?mod=misc&action=log", "index.php?mod=misc&action=log&step=2&logid=", "");
		$mastersearch->LoadConfig("log", '', '');
		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();
		$templ['index']['info']['content'] .= $mastersearch->GetReturn();	
	break;

  case 2:
    $log = $db->query_first("SELECT * FROM {$config["tables"]["log"]} WHERE logid = {$_GET['logid']}");
    $dsp->AddSingleRow($log['sort_tag']);
    $dsp->AddSingleRow($log['description']);
    $dsp->AddSingleRow($func->unixstamp2date($log['date'], 'datetime'));
    if ($log['userid']) $dsp->AddSingleRow($dsp->FetchUserIcon($log['userid']));
    $dsp->AddBackButton("index.php?mod=misc&action=log", '');
  break;
}
?>