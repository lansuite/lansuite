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

$selection_data[0]	=	t('Keine zuweisung');
$selection_data[1]	=	t('Alter');
$selection_data[2]	=	t('Weiblich');
$selection_data[3]	=	t('Männlich');
$selection_data[4]	=	t('Ortschaft');

switch ($_GET['step']){
	case 3:
		if(isset($_GET['group_id'])) $_POST['group_id'] = $_GET['group_id'];
		if($_POST['group_name'] == ""){
			$error_usrmgr['group'] = t('Geben Sie einen Gruppennamen ein');
			$_GET['step'] = 2;
		}
		
		if($_POST['selection'] == 1){
			if(!(preg_match("/^[0-9]+-[0-9]+$/i",trim($_POST['select_opts'])) || preg_match("/^-[0-9]+$/i",trim($_POST['select_opts'])) || preg_match("/^[0-9]+\+$/i",trim($_POST['select_opts'])))){
				$error_usrmgr['select_opts'] = t('Alter wurde falsch angegeben bitte in der Form 14+ , -18 oder 15-17 angeben.');
				$_GET['step'] = 2;
			}
		}
		if($_POST['selection'] == 4 && trim($_POST['select_opts']) == ""){
			$error_usrmgr['select_opts'] = t('Bitte eine Stadt angeben');
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
		$dsp->NewContent(t('Gruppenverwaltung'),t('Erstellen Sie Benutzergruppen um unterschiedliche Preise zu verlangen.'));
		$dsp->AddSingleRow("<a href='?mod=usrmgr&action=group&step=9'>".t('Benutzer einer Gruppe zuweisen')."</a>");

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
					
		$dsp->AddTextFieldRow("group_name",t('Gruppenname'),$_POST['group_name'],$error_usrmgr['group']);
		$dsp->AddTextFieldRow("description",t('Benutzergruppenbeschreibung'),$_POST['description'],$error_usrmgr['group_desc']);
		// Dropdown für auswahl der Automatischen einstufung
#		$selection_array = array();
#		foreach ($selection_data as $key => $value){
#			($key == $_POST['selection']) ? $selected = "selected" : $selected = "";
#			array_push($selection_array,"<option $selected value='$key'>" . $value . "</option>");
#		}
#		$dsp->AddDropDownFieldRow("selection",t('Automatische Zuweisung'),$selection_array,$error_usrmgr['selection']);
#		$dsp->AddTextFieldRow("select_opts",t('Zuweisungsbegriff (für Alter z.b. 18+, 16-18, -18)'),$_POST['select_opts'],$error_usrmgr['select_opts']);
		
		$dsp->AddFormSubmitRow("add","usrmgr/group");
		
		if($_GET['var'] != "update"){
			$count = $db->query_first("SELECT count(group_id) as n FROM {$config['tables']['party_usergroups']} WHERE selection != 0");
			if($count['n'] > 1){
				$dsp->AddHRuleRow();
				$dsp->AddDoubleRow("","<a href='?mod=usrmgr&action=group&step=15'>".t('Automatisch Zuordnung sortieren')."</a>");
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
			$func->confirmation(t('Benutzergruppe wurde hinzugefügt'),'?mod=usrmgr&action=group&step=2');
		}elseif ($_GET['var'] == "update"){
			$party->update_user_group($_GET['group_id'],$_POST['group_name'],$_POST['description'],$_POST['selection'],$_POST['select_opts']);
			$func->confirmation(t('Benutzergruppe wurde erfolgreich editiert.'),'?mod=usrmgr&action=group&step=2');
		}else{
			$func->error(t('Die Benutzergruppe konnte nicht angelegt werden.'),'?mod=usrmgr&action=group&step=2');
		}
		
	break;
	
	case 9:
		$dsp->NewContent(t('Gruppe auswählen'),t('Gruppe auswählen'));
		$dsp->SetForm("index.php?mod=usrmgr&action=group&step=10");		
		$party->get_user_group_dropdown();
		$dsp->AddFormSubmitRow("next");
		$dsp->AddContent();
	break;
	
	case 10:
		if(isset($_POST['group_id'])) $_GET['group_id'] = $_POST['group_id'];
    $current_url = "index.php?mod=usrmgr&action=group&step=10&group_id={$_GET['group_id']}";
    $target_url = "index.php?mod=usrmgr&action=group&step=11&group_id={$_GET['group_id']}&userid=";
    include_once('modules/usrmgr/search_basic_userselect.inc.php');
	break;

	case 11:
		if ($_POST['checkbox']) {
			$text = "";
			$userids = "";
			foreach ($_POST['checkbox'] AS $userid) {
				$user_data = $db->query_first("SELECT user.username, g.group_name FROM {$config["tables"]["user"]} AS user LEFT JOIN {$config["tables"]["party_usergroups"]} AS g ON user.group_id = g.group_id WHERE userid = '$userid'");
				if($user_data["group_name"] != ""){
					$text .=  "<b>{$user_data["username"]}</b> " . t('ist in der Gruppe') . " <b>" . $user_data["group_name"] . "</b>" . HTML_NEWLINE;
				}else{
					$text .=  "<b>{$user_data["username"]}</b> " . t('ist in keiner Gruppe') . HTML_NEWLINE;
				}
				$userids .= "$userid,";
			}
			$row = $db->query_first("SELECT group_name FROM {$config["tables"]["party_usergroups"]} WHERE group_id={$_GET['group_id']}");
			$text .= HTML_NEWLINE . t('Wollen Sie diese Benutzer der Gruppe %1 zuweisen?',"\"<b>" .$row['group_name'] . "</b>\"");
			$userids = substr($userids, 0, strlen($userids) - 1);
			$func->question($text, "index.php?mod=usrmgr&action=group&step=12&userids=$userids&group_id={$_GET['group_id']}", "index.php?mod=usrmgr&action=group&step=10&group_id={$_GET['group_id']}");

		} elseif ($_GET["userid"]) {
			$user_data = $db->query_first("SELECT user.username, g.group_name FROM {$config["tables"]["user"]} AS user LEFT JOIN {$config["tables"]["party_usergroups"]} AS g ON user.group_id = g.group_id WHERE userid = '{$_GET["userid"]}'");
					
			if ($user_data["username"]) {
				$func->question(t('Wollen Sie den Benutzer %1 der Gruppe %2 zuweisen?', $user_data["username"],$user_data["group_name"]),"index.php?mod=usrmgr&action=group&step=12&userid={$_GET["userid"]}&group_id={$_GET['group_id']}", "index.php?mod=usrmgr&action=group&step=10&group_id={$_GET['group_id']}");
			}else{
				$func->error(t('Dieser Benutzer existiert nicht'), "index.php?mod=usrmgr&action=group&step=10");	
			}
		} else $func->error(t('Dieser Benutzer existiert nicht'), "index.php?mod=usrmgr&action=group&step=10");	
	
	break;
	
	case 12:
		if ($_GET["userids"]) {
			$userids = split(",", $_GET["userids"]);
			foreach ($userids as $userid) {
				$db->query("UPDATE {$config["tables"]["user"]} SET group_id = '{$_GET['group_id']}' WHERE userid = {$_GET["userid"]} LIMIT 1");
			}
		} else $db->query("UPDATE {$config["tables"]["user"]} SET group_id = '{$_GET['group_id']}' WHERE userid = {$_GET["userid"]} LIMIT 1");

		$func->confirmation(t('Die Gruppenzuweisung wurde erfolgreich durchgeführt'), "index.php?mod=usrmgr&action=group&group_id={$_GET['group_id']}");
	break;
	
	
	// Sort Groups
	case 15:
		$dsp->NewContent(t('Gruppen sortieren'), t('Hier können Sie die Gruppen sortieren in welcher Reihenfolge sie Angewendet werden sollen. Die oberste hat die höchste Priorität'));

		$groups = $db->query("SELECT * FROM {$config["tables"]["party_usergroups"]} WHERE selection != 0 ORDER BY pos");
		$z = 0;
		
		$usrmgr_selection[0] = t('Keine zuweisung');
		$usrmgr_selection[1] = t('Alter');
		$usrmgr_selection[2] = t('Weiblich');
		$usrmgr_selection[3] = t('Männlich');
		$usrmgr_selection[4] = t('Ortschaft');
		
		while ($group = $db->fetch_array($groups)){
			$z++;
			$db->query("UPDATE {$config["tables"]["party_usergroups"]} SET pos = $z WHERE group_id = {$group["group_id"]}");

			$link = "";
			if ($z > 1)  $link .= "[<a href=\"index.php?mod=usrmgr&action=group&step=16&pos=$z\">^</a>] ";
			if ($z < $db->num_rows($groups)) $link .= "[<a href=\"index.php?mod=usrmgr&action=group&step=17&pos=$z\">v</a>]";
			$link .= " " . $usrmgr_selection[$group['selection']] . " " . $group['select_opts'];
			
			$dsp->AddDoubleRow("$z) ". $group["group_name"], $link);
			
		}
		$db->free_result($groups);

		$dsp->AddBackButton("index.php?mod=usrmgr&action=group"); 
		$dsp->AddContent();
	break;
	
	// Delete Group
	case 20:
		$row = $db->query_first("SELECT * FROM {$config['tables']['party_usergroups']} WHERE group_id={$_POST['group_id']}");
		$func->question(t('Wollen sie die Gruppe %1 wirklich löschen?',$row['group_name']),"index.php?mod=usrmgr&action=group&step=21&group_id={$_POST['group_id']}","index.php?mod=usrmgr&action=group");
	break;
	
	case 21:
		$dsp->NewContent(t('Gruppe zuweisen'),t('Welche Gruppe möchten Sie den Benutzern die in der gelöschten Gruppe sind zuweisen?'));
		$dsp->SetForm("index.php?mod=usrmgr&action=group&step=22&group_id={$_GET['group_id']}");
		$party->get_user_group_dropdown("NULL",1);
		$dsp->AddFormSubmitRow("next");
		$dsp->AddContent();
	break;
	
	case 22:
		$party->delete_usergroups($_GET['group_id'],$_POST['group_id']);
		$func->confirmation(t('Gruppe erfolgreich gelöscht.'),"index.php?mod=usrmgr&action=group");
	break;
	
	// Multi-User-Assign
  case 30;
  	foreach ($_POST['action'] as $key => $val) {
      $db->query("UPDATE {$config["tables"]["user"]} SET group_id = '{$_GET['group_id']}' WHERE userid = ". (int)$key);
    }
		$func->confirmation(t('Die Gruppenzuweisung wurde erfolgreich durchgeführt'), "index.php?mod=usrmgr");
  break;
}

?>