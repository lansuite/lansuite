<?php
/*****************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	----------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		1.0
*	Filename: 		change.php
*	Module: 		infocat
*	Original by: 		fritzsche@emazynx.de
*	Changes by: 		magic_erwin@gmx.de
*	Last change: 		02.07.2004 01:35
*	Description: 		Changes an entry in info menu
*	Remarks: 		None
*			
******************************************************************************/
  
$category = $_GET["category"];
$step = $_GET["step"];
if ($_GET["parentid"]!="") $pid = $_GET["parentid"];
else $pid = $_POST["parentid"];

switch($step) {
	
	case 2:
	//  ERRORS
	
	$_GET["infoID"] = $_POST["infocat_id"];
	
	$i = strlen($_POST["infocat_text1"]);
	
	if($i > 5000) {
		$error_infocat_text1 = $func->generate_error_template("infocat_form","text1","Der Textk&ouml;rper darf nicht mehr als 5000 Zeichen enthalten");
		eval($error_infocat_text1);
		$step = 1;
	}
	
	if($_POST["infocat_title"] == "") {
		$error_infocat_title = $func->generate_error_template("infocat_form","title","Bitte geben Sie eine &Uuml;berschrift ein");
		eval($error_infocat_title);
		$step = 1;
	}
	
/*	if($_POST["infocat_text1"] == "" && $pid=="") {
		$error_infocat_text1 = $func->generate_error_template("infocat_form","text1","Bitte geben Sie einen Text ein.");
		eval($error_infocat_text1);
		$step = 1;
	}*/


/*	if(is_numeric($_POST["infocat_prio"]) == FALSE) {
		$error_infocat_prio = $func->generate_error_template("infocat_form","prio","Die Priorit&auml;t muss numerisch sein!");
		eval($error_infocat_prio);
		$step = 1;
	}*/
	
	
	break;

} // CLOSE SWITCH STEP

switch($step) {
	
	default:
		$foodres = $db->query("SELECT * FROM {$config["tables"]["infocat"]} WHERE  infocatID=\"".$_GET["infoID"]."\" AND category='".$category."'");
		$row = $db->fetch_array($foodres);
		$templ['infocat']['form']['info']['page_title']		= $lang["infocat"]["page_title_change"][$category];
		$templ['infocat']['form']['info']['page_description']	= $lang["infocat"]["page_description_change"][$category];
	
		if ($_POST['infocat_title'] == "") $_POST['infocat_title'] = $row["title"];
		if ($_POST['infocat_descr'] == "") $_POST['infocat_descr'] = $row["descr"];
		if ($_POST['infocat_text1'] == "") $_POST['infocat_text1'] = $row["text1"];
		if ($_POST['infocat_prio'] == "") $_POST['infocat_prio'] = $row["prio"];
		$_POST['infocat_oldpicture'] = $row["picture"];
		$_POST['infocat_oldinfogroup'] = $row["infogroup"];
		
		if ($pid!="") {
			$templ['infocat']['form']['control']['infocat_picture'] = "disabled";
			$templ['infocat']['form']['control']['infocat_text1'] = "disabled";
			$templ['infocat']['form']['control']['infocat_pid'] = "<input type=\"hidden\" name=\"parentid\" value=\"".$pid."\">";
		}
		
		// group selection list
		if ($_POST["infocat_gruppe"]=="") $ogrp = $row["infogroup"];
		else $ogrp = $_POST['infocat_gruppe'];
		$_POST['infocat_gruppe']="";
		$res2 = $db->query("SELECT DISTINCT `infogroup` FROM {$config["tables"]["infocat"]} WHERE category='".$category."' ORDER BY `infogroup` ASC");
		$_POST['infocat_gruppe'] .= "<option value=\"0\">keine</option>\n";
		while ($row2=$db->fetch_array($res2)) {
			if ($row2["infogroup"] == $ogrp) $selo="selected"; else $selo="";
			$_POST['infocat_gruppe'] .= "<option value=\"".$row2["infogroup"]."\" $selo>".$row2["infogroup"]."</option>\n";
		}		
		
		
		$templ['infocat']['form']['control']['form_action'] = "?mod=infocat&action=change& category=".$category."&infoID=".$_GET["id"]."&step=2";
		
	
		eval("\$templ['index']['info']['content'] .= \"". $func->gettemplate("infocat_form")."\";");
	
				
	break;
	
	case 2:
		// first, process file upload
		$newfilename = "";
		if ($_FILES['infocat_picture']['name']!="") {
			$newfilename = md5 (uniqid (rand())) . $_FILES['infocat_picture']['name'];
			@move_uploaded_file($_FILES['infocat_picture']['tmp_name'], $_SERVER["DOCUMENT_ROOT"]."/ext_inc/infocat/".$newfilename);
			// check if there was already a picture file ... yes? delete it!
			if ($_POST["infocat_oldpicture"]!="") {
				@unlink($_SERVER["DOCUMENT_ROOT"]."/ext_inc/infocat/".$_POST["infocat_oldpicture"]);
			}		
		}
		
		// insert new food group
		if ($_POST["infocat_new_group"]!="") {
			$grpcat = $_POST["infocat_new_group"];
		} else {
			$grpcat = $_POST["infocat_gruppe"];
		}		

		// do database query
		if ($newfilename!="") {
			$add_it = $db->query("UPDATE {$config["tables"]["infocat"]} SET
						title = '{$_POST["infocat_title"]}',
						descr = '{$_POST["infocat_descr"]}',
						infogroup = '$grpcat',
						picture = '$newfilename',
						text1 = '{$_POST["infocat_text1"]}'
					     WHERE infocatID=\"{$_POST["infocat_id"]}\"
					      AND category='".$category."'
				     	    "); 
		} else {
			$add_it = $db->query("UPDATE {$config["tables"]["infocat"]} SET
						title = '{$_POST["infocat_title"]}',
						descr = '{$_POST["infocat_descr"]}',
						infogroup = '$grpcat',
						text1 = '{$_POST["infocat_text1"]}'
				  	     WHERE infocatID=\"{$_POST["infocat_id"]}\"
				  	      AND category='".$category."'
				     	    "); 

		}

		if($add_it == 1) 
			$func->confirmation($lang["infocat"]["catchange"][$category],"");
		break; 
		
} // CLOSE SWITCH STEP
?>