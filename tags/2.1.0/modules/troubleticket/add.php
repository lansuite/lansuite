<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	 2.0
*	File Version:		 2.0
*	Filename: 			add.php
*	Module: 			Troubleticket
*	Main editor: 		denny@esa-box.de
*	Last change:
*	Description: 		Adds a troubleticket by user to the system
*	Remarks:
*
**************************************************************************
*
* Database "Status" and "Priotity" structure
*
* Status
*  0 - unassigned (status 0 entrys are never listed!)
*  1 - neu, noch nicht verifiziert (nur wenn user gepostet hat)
*  2 - verifiziert, noch nicht bearbeitet
*  3 - in Arbeit
*  4 - abgeschlossen
*  5 - won't fix
*
* Priorität
*
*  0 - unassigned
*  1 - low
*  2 - normal
*  3 - high
*  4 - critical
*
*/


switch($_GET["step"]) {
	case 2:
		if (strlen($_POST["tticket_text"]) > 5000) {
			$func->information($lang['troubleticket']['err_max_size'], "index.php?mod=troubleticket&action=add");
			$_GET["step"] = 1;
		}

		if ($_POST["tticket_desc"] == "") {
			$func->information($lang['troubleticket']['err_no_head'], "index.php?mod=troubleticket&action=add");
			$_GET["step"] = 1;
		}
		
		if(isset($_POST['tticket_cat']) && $_POST['tticket_cat'] == 0){
			$error['tticket_cat'] = $lang['troubleticket']['err_no_cat'];
			$_GET['step'] = 1;
		}
	break;
}


switch ($_GET["step"]) {
	default:
		$dsp->NewContent($lang['troubleticket']['headline'],$lang['troubleticket']['subline']);
		$dsp->SetForm("index.php?mod=troubleticket&action=add&step=2");

		$dsp->AddTextFieldRow("tticket_desc",$lang['troubleticket']['description'], $_POST['tticket_desc'], $error["tticket_desc"]);

		$t_cat = $db->query("SELECT *FROM {$config["tables"]["troubleticket_cat"]}");
		
		if($db->num_rows($t_cat) > 0){

			$t_cat_array[] = "<option value=\"0\">{$lang['troubleticket']['no_cat']}</option>";
			
			while ($row = $db->fetch_array($t_cat)){
				$t_cat_array[] .= "<option value=\"{$row['cat_id']}\">{$row['cat_text']}</option>";
			}
			
			$dsp->AddDropDownFieldRow("tticket_cat",$lang['troubleticket']['cat'],$t_cat_array,$error['tticket_cat']);
		}
			
		$options = array("10" => $lang['troubleticket']['state_0'],
			"20" => $lang['troubleticket']['state_1'],
			"30" => $lang['troubleticket']['state_2'],
			"40" => $lang['troubleticket']['state_3']
			);
		$t_array = array();
		if ($_POST["tticket_priority"] == "") $_POST["tticket_priority"] = "20";
		reset ($options);
		while (list ($key, $val) = each ($options)) {
			($_POST["tticket_priority"] == $key) ? $selected = "selected" : $selected = "";
			array_push ($t_array, "<option $selected value=\"$key\">$val</option>");
		}
		$dsp->AddDropDownFieldRow("tticket_priority",$lang['troubleticket']['priority'], $t_array, $error["tticket_priority"], 1);

		if ($auth["type"] > 1) {
			$dsp->AddRadioRow("orgaonly",$lang['troubleticket']['visible_4all'], "0", $error["orgaonly"], 0, 1);
			$dsp->AddRadioRow("orgaonly",$lang['troubleticket']['visible_4orga'], "1", "", 0, 0);
		}

		$dsp->AddTextAreaPlusRow("tticket_text", "Text", $_POST["tticket_text"], $error["tticket_text"], "", "", 1);

		$dsp->AddFormSubmitRow("add");
		$dsp->AddBackButton("index.php?mod=troubleticket", "troubleticket/add");
		$dsp->AddContent();
	break;

	case 2:
		$czeit = time();

		if ($auth["type"] <= 1) {
			$ticketstatus = '1';
			$vzeit = '';
		} else {
			$ticketstatus = '2';
			$vzeit = $czeit;
		}

		if (!$_POST["tticket_text"]) $_POST["tticket_text"] = $lang['troubleticket']['no_contend'];

		$target_userid = 0;
		if (!isset($_POST["tticket_cat"]) || $_POST["tticket_cat"] == 0){
			$_POST["tticket_cat"] = 0;
		}else{
			$cat_data = $db->query_first("SELECT * FROM {$config["tables"]["troubleticket_cat"]} WHERE cat_id = {$_POST["tticket_cat"]}");
			if($cat_data['orga'] > 0){	
				$target_userid	= $cat_data['orga'];
			}
		}
		
				
		$db->query("INSERT INTO {$config["tables"]["troubleticket"]} SET
				created = '$czeit',
				verified = '$vzeit',
				process = '',
				finished = '',
				status = '$ticketstatus',
				processstatus = '0',
				priority = '{$_POST["tticket_priority"]}',
				origin_userid = '{$auth["userid"]}',
				target_userid = '$target_userid',
				orgaonly = '{$_POST["orgaonly"]}',
				caption = '{$_POST["tticket_desc"]}',
				text = '{$_POST["tticket_text"]}',
				orgacomment = '',
				publiccomment = '$pubcomment',
				cat = '{$_POST["tticket_cat"]}'
				");

		
		if($cat_data['orga'] > 0 && isset($_POST["tticket_cat"]) && $_POST["tticket_cat"] > 0){
			$func->setainfo(str_replace("%TTCaption%",$_POST["tticket_desc"],$lang['troubleticket']['user_assign']),$cat_data['orga'],1,"troubleticket",$db->insert_id());
		}
		
		$func->confirmation($lang['troubleticket']['add_confirm'], "index.php?mod=troubleticket&action=add");
	break;
}


?>

