<?php
/*************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		1.0
*	Filename: 		delete.php
*	Module: 		news
*	Main editor: 		Michael@one-network.org
*	Last change: 		05.02.2003 15:59
*	Description: 		Deletes news
*	Remarks: 		no bugs reported, should be ready for release
*
******************************************************************************/

switch($vars["step"]) {
	default:
		$mastersearch = new MasterSearch( $vars, "index.php?mod=news&action=delete", "index.php?mod=news&action=delete&step=2&newsid=", "");
		$mastersearch->LoadConfig( "news", $lang["news"]["del_ms_caption"], $lang["news"]["del_ms_subcaption"]);
		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();
		$templ['index']['info']['content'] .= $mastersearch->GetReturn();
	break;

	case 2:
		// CHECK IF NEWSID IS VALID
		$get_data = $db->query_first("SELECT caption FROM {$config["tables"]["news"]} WHERE newsid = '{$vars["newsid"]}'");
		$caption = $get_data["caption"];
		$newsid = $vars["newsid"];
	
		if ($caption != "") {
			$func->question(str_replace("%NAME%", $caption, $lang["news"]["del_confirm"]), "index.php?mod=news&action=delete&step=3&newsid=$newsid", "index.php?mod=news");
		} else $func->error($lang["news"]["change_err_notexist"], "index.php?mod=news&action=delete");
	break;

	case 3:
		// CHECK IF NEWSID IS VALID
		$get_data = $db->query_first("SELECT caption FROM {$config["tables"]["news"]} WHERE newsid = '{$vars["newsid"]}'");
		$caption = $get_data["caption"];
		$newsid = $vars["newsid"];

		if($caption != "") {
			$del_it = $db->query("DELETE from {$config["tables"]["news"]} WHERE newsid = '$newsid'");
			if ($del_it) {
				$func->confirmation($lang["news"]["del_success"], "index.php?mod=news&action=show");
				$func->log_event(str_replace("%NAME%", $get_data["caption"], $lang["news"]["del_log"]), 1, "News");
			}
		} else $func->error($lang["news"]["change_err_notexist"], "index.php?mod=news&action=delete");
	break;
}
?>
