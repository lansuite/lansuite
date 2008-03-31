<?php

include_once("modules/cashmgr/class_accounting.php");

switch($_GET['step'])
{
default:
	$current_url = 'index.php?mod=cashmgr&action=useraccount';
	$target_url = 'index.php?mod=cashmgr&action=useraccount&step=1&userid=';
        include_once('modules/usrmgr/search_basic_userselect.inc.php');
	break;
			
case "1":
	$account = new accounting($_GET['userid']);
	$account->getAccounting();
	break;
}
?>