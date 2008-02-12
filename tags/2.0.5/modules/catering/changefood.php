<?php
/*****************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	----------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		1.0
*	Filename: 		changefood.php
*	Module: 		Catering
*	Main editor: 		fritzsche@emazynx.de
*	Last change: 		25.05.2003 00:49
*	Description: 		Changes an entry in food menu
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
	$_GET["foodid"] = $_POST["catering_id"];
	
	$i = strlen($_POST["catering_text"]);
	
	if($i > 5000) {
		$error_catering_text = $func->generate_error_template("catering_form","text","Der Textk&ouml;rper darf nicht mehr als 5000 Zeichen enthalten");
		eval($error_catering_text);
		$step = 1;
	}
	
	if($_POST["catering_title"] == "") {
		$error_catering_caption = $func->generate_error_template("catering_form","title","Bitte geben Sie eine &Uuml;berschrift ein");
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
		$error_catering_text = $func->generate_error_template("catering_form","ek","Der Einkaufspreis sollte gr&ouml;�er 0 sein!");
		eval($error_catering_text);
		$step = 1;
	}			
	
	if(is_numeric($_POST["catering_prio"]) == FALSE) {
		$error_catering_text = $func->generate_error_template("catering_form","prio","Die Priorit&auml;t muss numerisch sein!");
		eval($error_catering_text);
		$step = 1;
	}
	
	
	break;

} // CLOSE SWITCH STEP

switch($step) {
	
	default:
		$foodres = $db->query("SELECT * FROM {$config["tables"]["catering_foods"]} WHERE ID=\"".$_GET["foodid"]."\"");
		$row = $db->fetch_array($foodres);
		$templ['catering']['form']['info']['page_title']		= "Speisen/Getr&auml;nke &auml;ndern";
		$templ['catering']['form']['info']['page_description']	= "Mit Hilfe des Dialoges k&ouml;nnen Sie Speisen und Getr&auml;nke &auml;ndern.";
	
		if ($_POST['catering_title'] == "") $_POST['catering_title'] = $row["title"];
		if ($_POST['catering_price'] == "") $_POST['catering_price'] = $row["price"];
		if ($_POST['catering_ek'] == "") $_POST['catering_ek'] = $row["ek"];
		if ($_POST['catering_text'] == "") $_POST['catering_text'] = $row["description"];
		if ($_POST['catering_prio'] == "") $_POST['catering_prio'] = $row["prio"];
		$_POST['catering_oldpicfile'] = $row["picfile"];
		$_POST['catering_oldgrpID'] = $row["grpID"];
		$_POST['catering_oldsupplID'] = $row["supplID"];
		$_POST['catering_supplhint'] = $row["supplHint"];
		
		if ($pid!="") {
			$templ['catering']['form']['control']['catering_picfile'] = "disabled";
			$templ['catering']['form']['control']['catering_text'] = "disabled";
			$templ['catering']['form']['control']['catering_pid'] = "<input type=\"hidden\" name=\"parentid\" value=\"".$pid."\">";
		}
		
		// group selection list
		if ($_POST["catering_gruppe"]=="") $ogrp = $row["grpID"];
		else $ogrp = $_POST['catering_gruppe'];
		$_POST['catering_gruppe']="";
		$res2 = $db->query("SELECT * FROM {$config["tables"]["catering_foodgroups"]} ORDER BY name");
		$_POST['catering_gruppe'] .= "<option value=\"0\">keine</option>\n";
		while ($row2=$db->fetch_array($res2)) {
			if ($row2["ID"] == $ogrp) $selo="selected"; else $selo="";
			$_POST['catering_gruppe'] .= "<option value=\"".$row2["ID"]."\" $selo>".$row2["name"]."</option>\n";
		}		
		
		// supplier selection list
		if ($_POST["catering_supplier"]=="") $osuppl = $row["supplID"];
		else $osuppl = $_POST['catering_supplier'];
		$_POST['catering_supplier']="";
		$res3 = $db->query("SELECT * FROM {$config["tables"]["catering_supplier"]} ORDER BY contact");
		$_POST['catering_supplier'] .= "<option value=\"0\">keiner</option>\n";
		while ($row3=$db->fetch_array($res3)) {
			if ($row3["ID"] == $osuppl) $selo="selected"; else $selo="";
			$_POST['catering_supplier'] .= "<option value=\"".$row3["ID"]."\" $selo>".$row3["contact"]."</option>\n";
		}		
		
		$templ['catering']['form']['control']['form_action'] = "index.php?mod=catering&action=changefood&foodid=".$_GET["id"]."&step=2";
		
	
		eval("\$templ['index']['info']['content'] .= \"". $func->gettemplate("catering_form")."\";");
	
				
	break;
	
	case 2:
		// first, process file upload
		$newfilename = "";
		if ($_FILES['catering_picfile']['name']!="") {
			$newfilename = md5 (uniqid (rand())) . $_FILES['catering_picfile']['name'];
			// test if you can move and write uploaded file.
			if (!@move_uploaded_file($_FILES['catering_picfile']['tmp_name'], $_SERVER["DOCUMENT_ROOT"]."/ext_inc/catering/".$newfilename)) $move_upload_file_return=HTML_NEWLINE . HTML_NEWLINE . "Das Bild konnte jedoch nicht hochgeladen werden! " . HTML_NEWLINE . "Bitte die Rechte im " . $_SERVER["DOCUMENT_ROOT"]."/ext_inc/catering/ Ordner &uuml;berpr&uuml;fen.";
			// check if there was already a picture file ... yes? delete it!
			if ($_POST["catering_oldpicfile"]!="") {
				@unlink($_SERVER["DOCUMENT_ROOT"]."/ext_inc/catering/".$_POST["catering_oldpicfile"]);
			}		
		}
		
		// check for group change, delete old groups if neccessary
		if ($_POST["catering_new_group"]!="" || $_POST["catering_oldgrpID"]!=$_POST["catering_gruppe"]) {
			$res = $db->query("SELECT COUNT(ID) as cntID FROM {$config["tables"]["catering_foods"]} WHERE grpID=\"".$_POST["catering_oldgrpID"]."\"");
			$row = $db->fetch_array($res);
			if ($row["cntID"]==1) {
				$db->query("DELETE FROM {$config["tables"]["catering_foodgroups"]} WHERE ID=\"".$_POST["catering_oldgrpID"]."\"");
			}
		}
		
		// insert new food group
		if ($_POST["catering_new_group"]!="") {
			$db->query("INSERT INTO {$config["tables"]["catering_foodgroups"]} SET name=\"".$_POST["catering_new_group"]."\"");
			$res = $db->query("SELECT last_insert_id()");
			$row = $db->fetch_array($res);
			$grpID = $row[0];
		} else {
			$grpID = $_POST["catering_gruppe"];
		}
		
		// check for supplier change, delete old supplier if neccessary
		if ($_POST["catering_new_supplier"]!="" || $_POST["catering_oldsupplID"]!=$_POST["catering_supplier"]) {
			$res = $db->query("SELECT COUNT(ID) as cntID FROM {$config["tables"]["catering_foods"]} WHERE supplID=\"".$_POST["catering_oldsupplID"]."\"");
			$row = $db->fetch_array($res);
			if ($row["cntID"]==1) {
				$db->query("DELETE FROM {$config["tables"]["catering_supplier"]} WHERE ID=\"".$_POST["catering_oldsupplID"]."\"");
			}
		}
		
		// insert new supplier
		if ($_POST["catering_new_supplier"]!="") {
			$db->query("INSERT INTO {$config["tables"]["catering_supplier"]} SET contact=\"".$_POST["catering_new_supplier"]."\"");
			$res = $db->query("SELECT last_insert_id()");
			$row = $db->fetch_array($res);
			$supplID = $row[0];
		} else {
			$supplID = $_POST["catering_supplier"];
		}		
		
		// do database query
		if ($newfilename!="") {
			$add_it = $db->query("UPDATE {$config["tables"]["catering_foods"]} SET
						title = \"{$_POST["catering_title"]}\",
						description = \"{$_POST["catering_text"]}\",
						price = \"{$_POST["catering_price"]}\",
						ek = \"{$_POST["catering_ek"]}\",			
						prio = \"{$_POST["catering_prio"]}\",
						picfile = \"{$newfilename}\",
						grpID = \"{$grpID}\",
						supplID = \"{$supplID}\",
						supplHint = \"{$_POST["catering_supplhint"]}\"
					     WHERE ID=\"{$_POST["catering_id"]}\"
				     	    "); 
		} else {
			$add_it = $db->query("UPDATE {$config["tables"]["catering_foods"]} SET
						title = \"{$_POST["catering_title"]}\",
						description = \"{$_POST["catering_text"]}\",
						price = \"{$_POST["catering_price"]}\",
						ek = \"{$_POST["catering_ek"]}\",	
						prio = \"{$_POST["catering_prio"]}\",
						grpID = \"{$grpID}\",
						supplID = \"{$supplID}\",
						supplHint = \"{$_POST["catering_supplhint"]}\"			
				  	     WHERE ID=\"{$_POST["catering_id"]}\"
				     	    "); 

		}

		if($add_it == 1) 
			$func->confirmation("Die Speisekarte wurde ge&auml;ndert. $move_upload_file_return","");
		break; 
		
} // CLOSE SWITCH STEP
?>
