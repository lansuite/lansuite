<?php
/*************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		1.0
*	Filename: 		modindex_infocat.php
*	Module: 		infocat
*	Original by: 		fritzsche@emazynx.de
*	Changes by: 		magic_erwin@gmx.de
*	Last change: 		02.07.2004 01:35
*	Description: 		Switches action
*	Remarks: 		none
*
******************************************************************************/

//Standard category auf 0 stellen!
if ($_GET["category"]=="") $_GET["category"]="0";

	switch($_GET["action"]) {
		case show:
			include("modules/infocat/show.php");
		break;
		case add:
			if ($_SESSION["auth"]["type"] > 1) {
				include ("modules/infocat/add.php");
			} else $func->error("ACCESS_DENIED","");
		break;
	case "sortorderup":
		if ($auth['type'] > 1) { 
			include ("modules/infocat/show.php");
		} else 
			$func->error("ACCESS_DENIED","");
		break; 

	case "sortorderdown":
		if ($auth['type'] > 1) { 
			include ("modules/infocat/show.php");
		} else 
			$func->error("ACCESS_DENIED","");
		break; 		
	case "delete":
		if ($auth['type'] > 1) { 
			include ("modules/infocat/delete.php");
		} else 
			$func->error("ACCESS_DENIED","");
		break; 
	
	case "change": 
		if ($auth['type'] > 1) { 
			include ("modules/infocat/change.php");
		} else 
			$func->error("ACCESS_DENIED","");
		break; 
		default:
			include ("modules/infocat/show.php");
		break;
	}
?>