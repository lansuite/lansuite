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
}
?>