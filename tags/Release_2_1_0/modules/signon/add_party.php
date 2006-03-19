<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		1.0
*	Filename: 			add_party.php
*	Module: 			signon
*	Main editor: 		Genesis (marco@chuchi.tv)
*	Last change: 		03.03.2005
*	Description: 		Party register
*	Remarks:
*
**************************************************************************/

$step 		= $_GET['step'];

// Error Switch
switch($step){
	
	case 2:
		// Prüfen der eingaben
		if($_POST['name'] == ""){
			$signon_error['partyname'] = $lang['signon']['error_name'];
			$step = 1;
		};
		
	
		if($_POST['ort'] == ""){
			$signon_error['ort'] = $lang['signon']['error_ort'];
			$step = 1;
		};
		
		if($_GET['var'] == "new"){
			
			if($_POST['price_text'] == ""){
				$signon_error['price_text'] = $lang['signon']['error_price_text'];
				$step = 1;
			};
			
			if($_POST['price'] == ""){
				$signon_error['price'] = $lang['signon']['error_price'];
				$step = 1;
			};		
				
			if($_POST['max_guest'] == ""){
				$signon_error['max_guest'] = $lang['signon']['max_guest_error'];
				$step = 1;
			};
		}
		
		
	break;
	
}

switch ($step){
	// Formular für neue Party oder das ändern
	default:
		if($_GET['var'] == "update"){

			$dsp->NewContent($lang['signon']['edit_party_caption'],$lang['signon']['edit_party_subcaption']);
			$dsp->SetForm("index.php?mod=signon&action=add_party&step=2&partyid=" . $party->get_party_id() . "&var=update");

			if(!isset($_POST['name'])){
				$row = $db->query_first("SELECT * FROM {$config['tables']['partys']} WHERE party_id={$party->party_id}");
				$_POST  = array_merge_recursive($_POST, $row);	
			}
		}else{
			$dsp->NewContent($lang['signon']['add_party_caption'],$lang['signon']['add_party_subcaption']);
			$dsp->SetForm("index.php?mod=signon&action=add_party&step=2&var=new");
			
			$_POST['startdate'] 	= mktime($_POST["stime_value_hours"], $_POST["stime_value_minutes"], $_POST["stime_value_seconds"], $_POST["stime_value_month"], $_POST["stime_value_day"], $_POST["stime_value_year"]);
			$_POST['enddate']		= mktime($_POST["etime_value_hours"], $_POST["etime_value_minutes"], $_POST["etime_value_seconds"], $_POST["etime_value_month"], $_POST["etime_value_day"], $_POST["etime_value_year"]);
			$_POST['sstartdate']	= mktime($_POST["sstime_value_hours"], $_POST["sstime_value_minutes"], $_POST["sstime_value_seconds"], $_POST["sstime_value_month"], $_POST["sstime_value_day"], $_POST["sstime_value_year"]);
			$_POST['senddate']		= mktime($_POST["setime_value_hours"], $_POST["setime_value_minutes"], $_POST["setime_value_seconds"], $_POST["setime_value_month"], $_POST["setime_value_day"], $_POST["setime_value_year"]);
		}
		
		$dsp->AddTextFieldRow("name",$lang['signon']['partyname'],$_POST['name'],$signon_error['partyname']);
		$dsp->AddTextFieldRow("max_guest",$lang['signon']['max_guest'],$_POST['max_guest'],$signon_error['max_guest']);
		$dsp->AddTextFieldRow("plz",$lang['signon']['plz'],$_POST['plz'],$signon_error['plz']);
		$dsp->AddTextFieldRow("ort",$lang['signon']['ort'],$_POST['ort'],$signon_error['ort']);
		$dsp->AddDateTimeRow("stime",$lang['signon']['stime'],$_POST['startdate'],$signon_error['stime'], "");
		$dsp->AddDateTimeRow("etime",$lang['signon']['etime'],$_POST['enddate'],$signon_error['etime'], "");
		$dsp->AddDateTimeRow("sstime",$lang['signon']['sstime'],$_POST['sstartdate'],$signon_error['sstime'], "");
		$dsp->AddDateTimeRow("setime",$lang['signon']['setime'],$_POST['senddate'],$signon_error['setime'], "");
		// erster Preis einfügen
		if($_GET['var'] == "new"){
			$dsp->AddTextFieldRow("price_text",$lang['signon']['price_text'],$_POST['price_text'],$signon_error['price_text']);
			$dsp->AddTextFieldRow("price",$lang['signon']['price'],$_POST['price'],$signon_error['price']);
		}
		$dsp->AddFormSubmitRow("add");
		$dsp->AddBackButton("index.php?mod=signon","signon/add_party");
		$dsp->AddContent();
	break;
	
	
	case 2:
		if($_GET['var'] == "new"){
			$party->add_party();
			$party->add_price($_POST['price_text'],$_POST['price']);
			$func->confirmation($lang['signon']['add_ok'], "index.php?mod=signon&action=edit_party");
		}elseif ($_GET['var'] == "update"){
			$party->change_party();
			$func->confirmation($lang['signon']['update_ok'], "index.php?mod=signon&action=edit_party");
		}else{
			$func->error($lang['signon']['entry_error'], "index.php?mod=signon&action=edit_party");
		}
	break;	
	
	
	// Party fest einstellen
	case 3:
		if($db->query("UPDATE {$config['tables']['config']} SET cfg_value = '" . $_POST['party_id'] . "' WHERE cfg_key = 'signon_partyid'")){
			$func->confirmation($lang['signon']['change_party_id_ok'],"index.php?mod=signon&action=edit_party");
			
		}else{
		    $func->error($lang['signon']['change_party_id_fail'],"index.php?mod=signon&action=edit_party");
		}
	break;
	
}
?>
