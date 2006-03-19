<?php

include_once("modules/cron/class_cronjob.php");

$cronjob = new cronjob();

switch ($_GET['action']){
	
	default:
		$cronjob->menu_joblist();
		
	break;
	
	
	case "config":
		$cronjob->menu_config($_GET['job']);
	break;
		
}

?>