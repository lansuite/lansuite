<?php
switch($_GET["step"]) {
	default:
		$mastersearch = new MasterSearch($vars, "index.php?mod=server&action=delete", "index.php?mod=server&action=delete&step=2&serverid=", "");
		$mastersearch->LoadConfig("server", $lang["server"]["ms_search"], $lang["server"]["ms_result"]);
		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();

		$templ['index']['info']['content'] .= $mastersearch->GetReturn();
    break;
    
    case 2:
		$server = $db->query_first("SELECT caption FROM {$config[tables][server]} WHERE serverid = '{$_GET["serverid"]}'");
		
		$servername = $server["caption"];

		if($server) $func->question(str_replace("%CAPTION%", "<b>$servername</b>", $lang["server"]["del_confirm"]), "index.php?mod=server&action=delete&step=3&serverid={$_GET["serverid"]}", "index.php?mod=server&action=delete");
		else $func->error($lang["server"]["del_notexist"], "index.php?mod=server&action=delete");
	break;
    
    
    case 3:
		$server = $db->query_first("SELECT caption, owner FROM {$config[tables][server]}
		WHERE serverid = '{$_GET["serverid"]}'");

		if ($server) {
			if (($server["owner"] != $auth["userid"]) || ($auth["type"] <= 0)) $func->information($lang["server"]["change_norights"], "index.php?mod=server&action=delete");

			else {
				$delete = $db->query("DELETE FROM {$config[tables][server]} WHERE serverid = '{$_GET["serverid"]}'");
			
				$servername = $server["caption"];
				if ($delete) $func->confirmation(str_replace("%CAPTION%", "<b>$servername</b>", $lang["server"]["del_success"]), "index.php?mod=server&action=delete");
			}
		} else $func->error($lang["server"]["del_notexist"], "index.php?mod=server&action=delete");
    break;
}
?>
