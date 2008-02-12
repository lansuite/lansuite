<?php
/*****************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	----------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		1.0
*	Filename: 		add.php
*	Module: 		infocat
*	Original by: 		fritzsche@emazynx.de
*	Changes by: 		magic_erwin@gmx.de
*	Last change: 		02.07.2004 01:35
*	Description: 		Adds infos
*	Remarks: 		none*			
******************************************************************************/

$category = $_GET["category"];
$step = $_GET["step"];
if ($_GET["parentid"]!="") $pid = $_GET["parentid"];
else $pid = $_POST["parentid"];


switch($step) {
	
	case 2:
	//  ERRORS
	
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

/*	if($_POST["infocat_descr"] == "" && $pid=="") {
		$error_infocat_descr = $func->generate_error_template("infocat_form","descr","Bitte geben Sie einen Text ein.");
		eval($error_infocat_descr);
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

		// Group selection list
		$ogrp = $_POST['infocat_gruppe'];
		$_POST['infocat_gruppe']="";
		$res = $db->query("SELECT DISTINCT `infogroup` FROM {$config["tables"]["infocat"]} WHERE category='".$category."' ORDER BY `infogroup` ASC");
		$_POST['infocat_gruppe'] .= "<option value=\"keine\">keine</option>\n";
		while ($row=$db->fetch_array($res)) {
			if ($row["infogroup"] == $ogrp) $selo="selected"; else $selo="";
			$_POST['infocat_gruppe'] .= "<option value=\"".$row["infogroup"]."\" $selo>".$row["infogroup"]."</option>\n";
		}

	
		$templ['infocat']['form']['info']['page_title']		= $lang["infocat"]["page_title_add"][$category];
		$templ['infocat']['form']['info']['page_description']	= $lang["infocat"]["page_description_add"][$category];
	
		$templ['infocat']['form']['control']['form_action'] = "?mod=infocat&action=add& category=".$category."&step=2";
		
		if ($pid!="") {
			$templ['infocat']['form']['control']['infocat_picture'] = "disabled";
			$templ['infocat']['form']['control']['infocat_text1'] = "disabled";
			$templ['infocat']['form']['control']['infocat_pid'] = "<input type=\"hidden\" name=\"parentid\" value=\"".$pid."\">";
		}		
		
		eval("\$templ['index']['info']['content'] .= \"". $func->gettemplate("infocat_form")."\";");
	
				
	break;
	
	case 2:
	
		// first, process file upload
		$newfilename = "";
		if ($_FILES['infocat_picture']['name']!="") {
			$newfilename = md5 (uniqid (rand())) . $_FILES['infocat_picture']['name'];
			@move_uploaded_file($_FILES['infocat_picture']['tmp_name'], $_SERVER["DOCUMENT_ROOT"]."/ext_inc/infocat/".$newfilename);
		}
			
		// do database query
		if ($pid=="") $pid = 0;
		if ($_POST["infocat_new_group"]!="") {
			$grpcat = $_POST["infocat_new_group"];
		} else {
			$grpcat = $_POST["infocat_gruppe"];
		}		
		$add_it = $db->query("INSERT INTO {$config["tables"]["infocat"]} SET
					title = '{$_POST["infocat_title"]}',
					descr = '{$_POST["infocat_descr"]}',
					infogroup = '$grpcat',
					picture = '$newfilename',
					text1 = '{$_POST["infocat_text1"]}',
					category='".$category."'
			     	    "); 

		if($add_it == 1) 
			$func->confirmation($lang["infocat"]["catchange"][$category],"");
		break; 
		
} // CLOSE SWITCH STEP
?>