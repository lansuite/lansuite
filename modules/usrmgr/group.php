<?php

/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0.3
*	File Version:		1.0
*	Filename: 			group.php
*	Module: 			usrmgr
*	Main editor: 		Genesis marco@chuchi.tv
*	Last change: 		25.02.05
*	Description: 		Editieren der Preise für die Partys
*	Remarks:
*
**************************************************************************/

$selection_data[0]	=	$lang['usrmgr']['selection'][0];
$selection_data[1]	=	$lang['usrmgr']['selection'][1];
$selection_data[2]	=	$lang['usrmgr']['selection'][2];
$selection_data[3]	=	$lang['usrmgr']['selection'][3];
$selection_data[4]	=	$lang['usrmgr']['selection'][4];

switch ($_GET['step']){
	case 3:
		if(isset($_GET['group_id'])) $_POST['group_id'] = $_GET['group_id'];
		if($_POST['group_name'] == ""){
			$error_usrmgr['group'] = $lang['usrmgr']['error_group'];
			$_GET['step'] = 2;
		}
		
		if($_POST['selection'] == 1){
			if(!(preg_match("/^[0-9]+-[0-9]+$/i",trim($_POST['select_opts'])) || preg_match("/^-[0-9]+$/i",trim($_POST['select_opts'])) || preg_match("/^[0-9]+\+$/i",trim($_POST['select_opts'])))){
				$error_usrmgr['select_opts'] = $lang['usrmgr']['error_select_age'];
				$_GET['step'] = 2;
			}
		}
		if($_POST['selection'] == 4 && trim($_POST['select_opts']) == ""){
			$error_usrmgr['select_opts'] = $lang['usrmgr']['error_select_city'];
			$_GET['step'] = 2;
		}
	break;
	
		// Move Up
	case 16:
		$db->query("UPDATE {$config["tables"]["party_usergroups"]} SET pos = 0 WHERE pos = ". ($_GET["pos"] - 1) ."");
		$db->query("UPDATE {$config["tables"]["party_usergroups"]} SET pos = pos - 1 WHERE pos = {$_GET["pos"]}");
		$db->query("UPDATE {$config["tables"]["party_usergroups"]} SET pos = {$_GET["pos"]} WHERE pos = 0");
		$_GET['step'] = 15;
	break;

	// Move Down
	case 17:
		$db->query("UPDATE {$config["tables"]["party_usergroups"]} SET pos = 0 WHERE pos = ". ($_GET["pos"] + 1) ."");
		$db->query("UPDATE {$config["tables"]["party_usergroups"]} SET pos = pos + 1 WHERE pos = {$_GET["pos"]}");
		$db->query("UPDATE {$config["tables"]["party_usergroups"]} SET pos = {$_GET["pos"]} WHERE pos = 0");
		$_GET['step'] = 15;
	break;
	
	case 22:
		if($_GET['group_id'] == $_POST['group_id']) $_GET['step'] = 21;
	break;
}


switch ($_GET['step']){
	
	default :
		$dsp->NewContent($lang['usrmgr']['edit_user_group_caption'],$lang['usrmgr']['edit_user_group_subcaption']);
		$dsp->AddSingleRow("<a href='?mod=usrmgr&action=group&step=9'>{$lang['usrmgr']['users_to_group']}</a>");

		if($_GET['var'] == "update"){
			$dsp->AddDoubleRow('',$dsp->FetchButton("index.php?mod=usrmgr&action=group&step=2&var=new","add"));
			$dsp->SetForm("index.php?mod=usrmgr&action=group&step=3&var=update&group_id={$_POST['group_id']}");
			if(!isset($_POST['group_name'])){
				$row = $db->query_first("SELECT * FROM {$config['tables']['party_usergroups']} WHERE group_id={$_POST['group_id']}");
				$_POST = array_merge_recursive($_POST,$row);	
			}
		}else{
			$dsp->SetForm("index.php?mod=usrmgr&action=group&step=3&var=new");
			
		}
					
		$dsp->AddTextFieldRow("group_name",$lang['usrmgr']['group'],$_POST['group_name'],$error_usrmgr['group']);
		$dsp->AddTextFieldRow("description",$lang['usrmgr']['group_desc'],$_POST['description'],$error_usrmgr['group_desc']);
		// Dropdown für auswahl der Automatischen einstufung
		$selection_array = array();
		foreach ($selection_data as $key => $value){
			($key == $_POST['selection']) ? $selected = "selected" : $selected = "";
			array_push($selection_array,"<option $selected value='$key'>" . $value . "</option>");
		}
		$dsp->AddDropDownFieldRow("selection",$lang['usrmgr']['selection_choise'],$selection_array,$error_usrmgr['selection']);
		$dsp->AddTextFieldRow("select_opts",$lang['usrmgr']['select_opts'],$_POST['select_opts'],$error_usrmgr['select_opts']);
		
		$dsp->AddFormSubmitRow("add","usrmgr/group");
		
		if($_GET['var'] != "update"){
			$count = $db->query_first("SELECT count(group_id) as n FROM {$config['tables']['party_usergroups']} WHERE selection != 0");
			if($count['n'] > 1){
				$dsp->AddHRuleRow();
				$dsp->AddDoubleRow("","<a href='?mod=usrmgr&action=group&step=15'>{$lang['usrmgr']['group_sort_list']}</a>");
			}
			$dsp->AddHRuleRow();
			$dsp->SetForm("index.php?mod=usrmgr&action=group&step=2&var=update");
			if($party->get_user_group_dropdown()){
				$dsp->AddFormSubmitRow("edit");
			}				
			if($dsp->form_open) $dsp->CloseForm();
			$dsp->AddHRuleRow();
			$dsp->SetForm("index.php?mod=usrmgr&action=group&step=20");
			if($party->get_user_group_dropdown()){
				$dsp->AddFormSubmitRow("delete");
			}
			if($dsp->form_open) $dsp->CloseForm();
			
		}
		
		$dsp->AddContent();
	break;
	
	case 3:
		if($_GET['var'] == "new"){
			$party->add_user_group($_POST['group_name'],$_POST['description'],$_POST['selection'],$_POST['select_opts']);
			$func->confirmation($lang['usrmgr']['add_group_ok'],'?mod=usrmgr&action=group&step=2');
		}elseif ($_GET['var'] == "update"){
			$party->update_user_group($_GET['group_id'],$_POST['group_name'],$_POST['description'],$_POST['selection'],$_POST['select_opts']);
			$func->confirmation($lang['usrmgr']['edit_group_ok'],'?mod=usrmgr&action=group&step=2');
		}else{
			$func->error($lang['usrmgr']['entry_error'],'?mod=usrmgr&action=group&step=2');
		}
		
	break;
	
	case 9:
		$dsp->NewContent($lang['usrmgr']['select_group_caption'],$lang['usrmgr']['select_group_caption']);
		$dsp->SetForm("index.php?mod=usrmgr&action=group&step=10");		
		$party->get_user_group_dropdown();
		$dsp->AddFormSubmitRow("next");
		$dsp->AddContent();
	break;
	
	case 10:
		if(isset($_POST['group_id'])) $_GET['group_id'] = $_POST['group_id'];
    include_once('modules/usrmgr/search.inc.php');
/*
		$mastersearch = new MasterSearch($vars, "index.php?mod=usrmgr&action=group&step=10&group_id={$_GET['group_id']}", "index.php?mod=usrmgr&action=group&step=11&group_id={$_GET['group_id']}&userid=", "GROUP BY email");
		$mastersearch->LoadConfig("users", $lang['usrmgr']['ms_search'], $lang['usrmgr']['ms_result']);
		$mastersearch->config['result_fields'][0]['checkbox']   = "checkbox";
		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();
		
		$templ['index']['info']['content'] .= $mastersearch->GetReturn();
*/
	break;

	case 11:
		if ($_POST['checkbox']) {
			$text = "";
			$userids = "";
			foreach ($_POST['checkbox'] AS $userid) {
				$user_data = $db->query_first("SELECT user.username, g.group_name FROM {$config["tables"]["user"]} AS user LEFT JOIN {$config["tables"]["party_usergroups"]} AS g ON user.group_id = g.group_id WHERE userid = '$userid'");
				if($user_data["group_name"] != ""){
					$text .=  "<b>{$user_data["username"]}</b> " . $lang['usrmgr']['is_in_group'] . " <b>" . $user_data["group_name"] . "</b>" . HTML_NEWLINE;
				}else{
					$text .=  "<b>{$user_data["username"]}</b> " . $lang['usrmgr']['is_in_nogroup'] . HTML_NEWLINE;
				}
				$userids .= "$userid,";
			}
			$row = $db->query_first("SELECT group_name FROM {$config["tables"]["party_usergroups"]} WHERE group_id={$_GET['group_id']}");
			$text .= HTML_NEWLINE . str_replace("%GROUP%","\"<b>" .$row['group_name'] . "</b>\"",$lang['usrmgr']['change_users_in_group']);
			$userids = substr($userids, 0, strlen($userids) - 1);
			$func->question($text, "index.php?mod=usrmgr&action=group&step=12&userids=$userids&group_id={$_GET['group_id']}", "index.php?mod=usrmgr&action=group&step=10&group_id={$_GET['group_id']}");

		} elseif ($_GET["userid"]) {
			$user_data = $db->query_first("SELECT user.username, g.group_name FROM {$config["tables"]["user"]} AS user LEFT JOIN {$config["tables"]["party_usergroups"]} AS g ON user.group_id = g.group_id WHERE userid = '$userid'");
					
			if ($user_data["username"]) {
				$func->question(str_replace("%USER%", $user_data["username"],str_replace("%GROUP%", $user_data["group_name"],$lang['usrmgr']['change_user_in_group'])),"index.php?mod=usrmgr&action=group&step=12&userid={$_GET["userid"]}&group_id={$_GET['group_id']}", "index.php?mod=usrmgr&action=group&step=10&group_id={$_GET['group_id']}");
			}else{
				$func->error($lang["usrmgr"]["chpaid_error_nouser"], "index.php?mod=usrmgr&action=group&step=10");	
			}
		} else $func->error($lang["usrmgr"]["chpaid_error_nouser"], "index.php?mod=usrmgr&action=group&step=10");	
	
	break;
	
	case 12:
		if ($_GET["userids"]) {
			$userids = split(",", $_GET["userids"]);
			foreach ($userids as $userid) {
				$db->query("UPDATE {$config["tables"]["user"]} SET group_id = '{$_GET['group_id']}' WHERE userid = $userid LIMIT 1");
			}
		} else $db->query("UPDATE {$config["tables"]["user"]} SET group_id = '{$_GET['group_id']}' WHERE userid = $userid LIMIT 1");

		$func->confirmation($lang['usrmgr']['change_usergroup_ok'], "index.php?mod=usrmgr&action=group&group_id={$_GET['group_id']}");
	break;
	
	
	// Sort Groups
	case 15:
		$dsp->NewContent($lang['usrmgr']['sort_group_caption'], $lang['usrmgr']['sort_group_subcaption']);

		$groups = $db->query("SELECT * FROM {$config["tables"]["party_usergroups"]} WHERE selection != 0 ORDER BY pos");
		$z = 0;
		while ($group = $db->fetch_array($groups)){
			$z++;
			$db->query("UPDATE {$config["tables"]["party_usergroups"]} SET pos = $z WHERE group_id = {$group["group_id"]}");

			$link = "";
			if ($z > 1)  $link .= "[<a href=\"index.php?mod=usrmgr&action=group&step=16&pos=$z\">^</a>] ";
			if ($z < $db->num_rows($groups)) $link .= "[<a href=\"index.php?mod=usrmgr&action=group&step=17&pos=$z\">v</a>]";
			$link .= " " . $lang['usrmgr']['selection'][$group['selection']] . " " . $group['select_opts'];
			
			$dsp->AddDoubleRow("$z) ". $group["group_name"], $link);
			
		}
		$db->free_result($groups);

		$dsp->AddBackButton("index.php?mod=usrmgr&action=group"); 
		$dsp->AddContent();
	break;
	
	// Delete Group
	case 20:
		$row = $db->query_first("SELECT * FROM {$config['tables']['party_usergroups']} WHERE group_id={$_POST['group_id']}");
		$func->question(str_replace("%GROUP%",$row['group_name'],$lang['usrmgr']['delete_group']),"index.php?mod=usrmgr&action=group&step=21&group_id={$_POST['group_id']}","index.php?mod=usrmgr&action=group");
	break;
	
	case 21:
		$dsp->NewContent($lang['usrmgr']['delete_group_capt'],$lang['usrmgr']['delete_group_subcapt']);
		$dsp->SetForm("index.php?mod=usrmgr&action=group&step=22&group_id={$_GET['group_id']}");
		$party->get_user_group_dropdown("NULL",1);
		$dsp->AddFormSubmitRow("next");
		$dsp->AddContent();
	break;
	
	case 22:
		$party->delete_usergroups($_GET['group_id'],$_POST['group_id']);
		$func->confirmation($lang['usrmgr']['delete_group_ok'],"index.php?mod=usrmgr&action=group");
	break;
		
}




?>
