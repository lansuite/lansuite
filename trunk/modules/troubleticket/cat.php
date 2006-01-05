<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	 2.0
*	File Version:		 2.0
*	Filename: 			cat.php
*	Module: 			Troubleticket
*	Main editor: 		marco@chuchi.tv
*	Last change:
*	Description: 		edit categories
*	Remarks:
*
**************************************************************************
*/


switch($_GET['step']){
	case 2:
		if($_POST['tticket_cat'] == 0 && $_GET['act'] == "change"){
			$error['tticket_cat'] = $lang['troubleticket']['cat_no_err'];
			$_GET['step'] = 1;
		}
		break;
	case 3:
		if(trim($_POST['name']) == "" || strlen($_POST['name']) > 30){
			$error_cat['name'] = $lang['troubleticket']['cat_err_name'];
			$_POST['tticket_cat'] = $_GET['cat_id'];
			$_GET['step'] = 2;
		}
	break;
}
switch($_GET['step']){
	
	default:
		$dsp->NewContent($lang['troubleticket']['cat']);
		
		$t_cat = $db->query("SELECT * FROM {$config["tables"]["troubleticket_cat"]}");
		if($db->num_rows($t_cat) > 0){

			$t_cat_array[] = "<option value=\"0\">{$lang['troubleticket']['no_cat']}</option>";
			
			while ($row = $db->fetch_array($t_cat)){
				$t_cat_array[] .= "<option value=\"{$row['cat_id']}\">{$row['cat_text']}</option>";
			}

			$dsp->SetForm("index.php?mod=troubleticket&action=cat&act=change&step=2");
			$dsp->AddDropDownFieldRow("tticket_cat",$lang['troubleticket']['cat'],$t_cat_array,$error['tticket_cat']);
			$dsp->AddFormSubmitRow("change");
		}else{
			$dsp->AddSingleRow($lang['troubleticket']['cat_empty']);
			
		}
	
		$dsp->AddDoubleRow("",$dsp->FetchButton("index.php?mod=troubleticket&action=cat&act=add&step=2","add"));
		$dsp->AddBackButton("index.php?mod=troubleticket");
		$dsp->AddContent();
	break;
	
	
	case 2:
		$dsp->NewContent($lang['troubleticket']['cat']);
		$user_row = $db->query("SELECT * FROM {$config["tables"]["user"]} WHERE type > 1");
	
		if(isset($_POST["tticket_cat"]) && $_POST["tticket_cat"] > 0){
			$user_row_option[] .= "<option value=\"0\">{$lang['troubleticket']['cat_nouser']}</option>";
		}else{
			$user_row_option[] .= "<option selected value=\"0\">{$lang['troubleticket']['cat_nouser']}</option>";
		}
		
		while ($user_data = $db->fetch_array($user_row)){
			if($user_data["userid"] == $_POST["tticket_cat"] && isset($_POST["tticket_cat"])){
				$user_row_option[] .= "<option selected value=\"{$user_data["userid"]}\">{$user_data["username"]}</option>";
			}else{
				$user_row_option[] .= "<option value=\"{$user_data["userid"]}\">{$user_data["username"]}</option>";
			}
		}
		
		if($_GET['act'] == "add"){
			$dsp->SetForm("index.php?mod=troubleticket&action=cat&act=add&step=3");
			$dsp->AddTextFieldRow("name",$lang['troubleticket']['cat'],"",$error_cat['name']);
			$dsp->AddDropDownFieldRow("orga",$lang['troubleticket']['cat_user'],$user_row_option,"");
			$dsp->AddFormSubmitRow("add");
		}else{
			$cat_data = $db->query_first("SELECT * FROM {$config["tables"]["troubleticket_cat"]} WHERE cat_id = {$_POST["tticket_cat"]}");
			
			$dsp->SetForm("index.php?mod=troubleticket&action=cat&act=change&step=3&cat_id={$_POST['tticket_cat']}");
			$dsp->AddTextFieldRow("name",$lang['troubleticket']['cat'],$cat_data['cat_text'],$error_cat['name']);
			$dsp->AddDropDownFieldRow("orga",$lang['troubleticket']['cat_user'],$user_row_option,"");
			$dsp->AddFormSubmitRow("change");
		}
		$dsp->AddContent();
	
	break;
	
	case 3:
		if($_GET['act'] == "add"){
			if($db->query("INSERT INTO {$config["tables"]["troubleticket_cat"]} SET
					cat_text = '{$_POST['name']}',
					orga = '{$_POST['orga']}'")){
				$func->confirmation($lang['troubleticket']['cat_ok'],"index.php?mod=troubleticket&action=cat");
			}else{
				$func->error($lang['troubleticket']['cat_err'],"index.php?mod=troubleticket&action=cat");
			}
			
		}else{
			if($db->query("UPDATE {$config["tables"]["troubleticket_cat"]} SET
					cat_text = '{$_POST['name']}',
					orga = '{$_POST['orga']}'
					WHERE cat_id = {$_GET['cat_id']}
			")){			
				$func->confirmation($lang['troubleticket']['cat_ok'],"index.php?mod=troubleticket&action=cat");
			}else{
				$func->error($lang['troubleticket']['cat_err'],"index.php?mod=troubleticket&action=cat");
			}
		}
	
	break;
}




?>