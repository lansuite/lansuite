<?php
/*****************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	----------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		1.0
*	Filename: 		addfood.php
*	Module: 		Catering
*	Main editor: 		fritzsche@emazynx.de
*	Last change: 		25.05.2003 00:49
*	Description: 		Adds an entry to food menu
*	Remarks: 		None
*			
******************************************************************************/
  

$step = $_GET["step"];
if ($_GET["parentid"]!="") $pid = $_GET["parentid"];
else $pid = $_POST["parentid"];

switch($step) {
	
	case 2:
	//  ERRORS
	
	$_POST["catering_price"] = str_replace(",",".",$_POST["catering_price"]);
	$_POST["catering_ek"] = str_replace(",",".",$_POST["catering_ek"]);
	
	$i = strlen($_POST["catering_text"]);
	
	if($i > 5000) {
		$error_catering_text = $func->generate_error_template("catering_form","text","Der Textkörper darf nicht mehr als 5000 Zeichen enthalten");
		eval($error_catering_text);
		$step = 1;
	}
	
	if($_POST["catering_title"] == "") {
		$error_catering_caption = $func->generate_error_template("catering_form","title","Bitte geben Sie eine Überschrift ein");
		eval($error_catering_caption);
		$step = 1;
	}
	
/*	if($_POST["catering_text"] == "" && $pid=="") {
		$error_catering_text = $func->generate_error_template("catering_form","text","Bitte geben Sie einen Text ein.");
		eval($error_catering_text);
		$step = 1;
	}*/

	if(is_numeric($_POST["catering_price"]) == FALSE) {
		$error_catering_text = $func->generate_error_template("catering_form","price","Bitte geben Sie eine Preis ein!");
		eval($error_catering_text);
		$step = 1;
	}
	
	if(is_numeric($_POST["catering_ek"]) == FALSE) {
		$error_catering_text = $func->generate_error_template("catering_form","ek","Bitte geben Sie einen Einkaufspreis ein!");
		eval($error_catering_text);
		$step = 1;
	}	
	
	if($_POST["catering_ek"] <= 0) {
		$error_catering_text = $func->generate_error_template("catering_form","ek","Der Einkaufspreis sollte größer 0 sein!");
		eval($error_catering_text);
		$step = 1;
	}			
	
	if($_POST["catering_price"] <= 0) {
		$error_catering_text = $func->generate_error_template("catering_form","price","Der Preis sollte größer 0 sein!");
		eval($error_catering_text);
		$step = 1;
	}	
		
	if(is_numeric($_POST["catering_prio"]) == FALSE) {
		$error_catering_text = $func->generate_error_template("catering_form","prio","Die Priorität muss numerisch sein!");
		eval($error_catering_text);
		$step = 1;
	}	
	
	break;

} // CLOSE SWITCH STEP

switch($step) {
	
	default:
	
		$templ['catering']['form']['info']['page_title']		= "Speisen/Getränke hinzufügen";
		$templ['catering']['form']['info']['page_description']	= "Mit Hilfe des Dialoges können Sie Speisen und Getränke hinzufügen.";
	
		$templ['catering']['form']['control']['form_action'] = "index.php?mod=catering&action=newfood&step=2";
		
		// Group selection list
		$ogrp = $_POST['catering_gruppe'];
		$_POST['catering_gruppe']="";
		$res = $db->query("SELECT * FROM {$config["tables"]["catering_foodgroups"]} ORDER BY name");
		$_POST['catering_gruppe'] .= "<option value=\"0\">keine</option>\n";
		while ($row=$db->fetch_array($res)) {
			if ($row["ID"] == $ogrp) $selo="selected"; else $selo="";
			$_POST['catering_gruppe'] .= "<option value=\"".$row["ID"]."\" $selo>".$row["name"]."</option>\n";
		}
		
		// Supplier selection list
		$osuppl = $_POST['catering_supplier'];
		$_POST['catering_supplier']="";
		$res = $db->query("SELECT * FROM {$config["tables"]["catering_supplier"]} ORDER BY contact");
		$_POST['catering_supplier'] .= "<option value=\"0\">keiner</option>\n";
		while ($row=$db->fetch_array($res)) {
			if ($row["ID"] == $ogrp) $selo="selected"; else $selo="";
			$_POST['catering_supplier'] .= "<option value=\"".$row["ID"]."\" $selo>".$row["contact"]."</option>\n";
		}
	
		if ($pid!="") {
			$templ['catering']['form']['control']['catering_picfile'] = "disabled";
			$templ['catering']['form']['control']['catering_text'] = "disabled";
			$templ['catering']['form']['control']['catering_pid'] = "<input type=\"hidden\" name=\"parentid\" value=\"".$pid."\">";
		}		
		
		eval("\$templ['index']['info']['content'] .= \"". $func->gettemplate("catering_form")."\";");
	
				
	break;
	
	case 2:
		// first, process file upload
		$newfilename = "";
		$supplID = 0;
		if ($_FILES['catering_picfile']['name']!="") {
			$newfilename = md5 (uniqid (rand())) . $_FILES['catering_picfile']['name'];
			@move_uploaded_file($_FILES['catering_picfile']['tmp_name'], $_SERVER["DOCUMENT_ROOT"]."/ext_inc/catering/".$newfilename);
		}

		// insert new supplier
		if ($_POST["catering_new_supplier"]!="") {
			$db->query("INSERT INTO {$config["tables"]["catering_supplier"]} SET contact=\"".$_POST["catering_new_supplier"]."\"");
//			$res = $db->query("SELECT max(ID) FROM {$config["tables"]["catering_supplier"]}");
//			$row = $db->fetch_array($res);
//			$supplID = row[0];
			$supplID = mysql_insert_id();
			echo "SupplID: " . $supplID;
		} else {
			$supplID = $_POST["catering_supplier"];
		}
		
		// insert new food group
		if ($_POST["catering_new_group"]!="") {
			if($_POST["is_wiz"]=="wizzard"){
				$as_wiz = 1;
			} else {
				$as_wiz = 0;
			}
			$db->query("INSERT INTO {$config["tables"]["catering_foodgroups"]} SET 
					name=\"".$_POST["catering_new_group"]."\",
					supplID=\"".$supplID."\",
					wizzard=\"".$as_wiz."\"
				");
//			$res = $db->query("SELECT max(ID) FROM {$config["tables"]["catering_foodgroups"]}");
//			$row = $db->fetch_array($res);
//			$grpID = $row[0];
			$grpID = mysql_insert_id();
		} else {
			$grpID = $_POST["catering_gruppe"];
		}
		
		// do database query
		if ($pid=="") $pid = 0;
		$add_it = $db->query("INSERT INTO {$config["tables"]["catering_foods"]} SET
					title = '{$_POST["catering_title"]}',
					description = '{$_POST["catering_text"]}',
					price = '{$_POST["catering_price"]}',
					ek = '{$_POST["catering_ek"]}',
					prio = '{$_POST["catering_prio"]}',
					picfile = '$newfilename',
					grpID = '$grpID',
					supplID = '$supplID',
					supplHint = \"{$_POST["catering_supplhint"]}\",
					parentID = \"".$pid."\"
			     	    "); 

		if($add_it == 1) 
			$func->confirmation("Die Speisekarte wurde ergänzt","");
		break; 
		
} // CLOSE SWITCH STEP
?>
