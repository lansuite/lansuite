<?php
/*****************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	----------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		1.0
*	Filename: 		delete.php
*	Module: 		infocat
*	Original by: 		fritzsche@emazynx.de
*	Changes by: 		magic_erwin@gmx.de
*	Last change: 		02.07.2004 01:35
*	Description: 		deletes an entry in info menu
*	Remarks: 		None
*			
******************************************************************************/
  
$category = $_GET["category"];
$step = $_GET["step"];

switch($step) {
	
	default:

		$foodres = $db->query("SELECT * FROM {$config["tables"]["infocat"]} WHERE infocatID=\"".$_GET["infoID"]."\" AND category='".$category."'");
		$row = $db->fetch_array($foodres);
		$func->question("Eintrag <b>".$row["title"]."</b> wirklich l&ouml;schen?<br><br>".$anno."","?mod=infocat&action=delete& category=".$category."&infoID=".$_GET["infoID"]."&step=2","?mod=infocat");			
		break;
	
	case 2:
		// do database query
		$foodres = $db->query("SELECT picture, infogroup FROM {$config["tables"]["infocat"]} WHERE infocatID='".$_GET["infoID"]."' AND category='".$category."'");
		$row = $db->fetch_array($foodres);
		// delete picture, if present
		if ($row["picture"]!="") 
			@unlink($_SERVER["DOCUMENT_ROOT"]."/ext_inc/infocat/".$row["picture"]);
			

		$add_it = $db->query("DELETE FROM {$config["tables"]["infocat"]} WHERE infocatID='".$_GET["infoID"]."' AND category='".$category."'");
		
		if($add_it == 1) 
			$func->confirmation($lang["infocat"]["catchange"][$category],"");
		break; 
		
}
?>