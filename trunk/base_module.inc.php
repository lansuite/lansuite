<?php

$language = "de";

switch($_GET['mod'])
{

/*
* You are in Modul-Switch ! (switch($mod){)
*
* Add "case" only!
* 
*/ 



// DONT CHANGE FOLLOW !

	/////////////////////////////////////// Messenger ////////////////////////////////////////////
	case "query":
#		include_once("modules/msgsys/mod_settings/modul_tables.inc.php");
		include('modules/msgsys/query.php');
	break;
	case "query_messages":
#		include_once("modules/msgsys/mod_settings/modul_tables.inc.php");
		include('modules/msgsys/query_messages.php');
	break;
	case "query_console":
#		include_once("modules/msgsys/mod_settings/modul_tables.inc.php");
		include('modules/msgsys/query_console.php');
	break;
	/////////////////////////////////////// Messenger ////////////////////////////////////////////

	//////////////////////////////////////// Helplet /////////////////////////////////////////////
	case "helplet":
		include('modules/helplet/helplet.php');
	break;
	case "helplet_top":
		include('modules/helplet/helplet_top.php');
	break;
	case "helplet_content":
		include('modules/helplet/helplet_content.php');
	break;
	//////////////////////////////////////// Helplet /////////////////////////////////////////////

	/////////////////////////////////////// Tournament2 ///////////////////////////////////////////
	case "tdetails":
#		include_once("modules/tournament2/mod_settings/modul_tables.inc.php");
		include_once("modules/tournament2/language/tournament2_lang_".$language.".php");
		include('modules/tournament2/team_details.php');
	break;
	case "tree_img":
#		include_once("modules/tournament2/mod_settings/modul_tables.inc.php");
		include_once("modules/tournament2/language/tournament2_lang_".$language.".php");
		include('modules/tournament2/tree_img.php');
	break;
	case "tree_frame":
#		include_once("modules/tournament2/mod_settings/modul_tables.inc.php");
		include_once("modules/tournament2/language/tournament2_lang_".$language.".php");
		include('modules/tournament2/tree_frame.php');
	break;
	/////////////////////////////////////// Tournament2 ///////////////////////////////////////////

	//////////////////////////////////////// Seating /////////////////////////////////////////////
	case "seating":
#		include_once("modules/seating/mod_settings/modul_tables.inc.php");
		include_once("modules/seating/language/seating_lang_".$language.".php");
		include('modules/seating/popup.php');
	break;
	case "ipprint":
		if ($_SESSION["auth"]["type"] > 1) {
#			include_once("modules/seating/mod_settings/modul_tables.inc.php");
			include('modules/misc/ipprint_window.php');
		}
	break;
	//////////////////////////////////////// Seating /////////////////////////////////////////////
	
	/////////////////////////////////////// Picgallery ///////////////////////////////////////////
	case "pic_download":
#		include_once("modules/picgallery/mod_settings/modul_tables.inc.php");
		include('modules/picgallery/download.php');
	break;
	/////////////////////////////////////// Picgallery ///////////////////////////////////////////

	///////////////////////////////////////    NOC     ///////////////////////////////////////////
	case "noc_statistics_graph":
#		include_once("modules/noc/mod_settings/modul_tables.inc.php");
		include('modules/noc/statistics.php');
	break;
	case "noc_port_picture":
#		include_once("modules/noc/mod_settings/modul_tables.inc.php");
		include('modules/noc/port_picture.php');
	break;
	///////////////////////////////////////    NOC     ///////////////////////////////////////////
	
	///////////////////////////////////////    STATS     ///////////////////////////////////////////
	case "stats_graph":
		include_once("modules/stats/language/stats_lang_de.php");
#		include_once("modules/stats/mod_settings/modul_tables.inc.php");
#		include_once("modules/noc/mod_settings/modul_tables.inc.php");		
		include('modules/stats/statistic_graph.php');
	break;
	///////////////////////////////////////    STATS     ///////////////////////////////////////////

	
	///////////////////////////////////////    PAYPAL     ///////////////////////////////////////////
	case "paypal":
		include_once("modules/paypal/language/paypal_lang_$language.php");
		include('modules/paypal/paying.php');
	break;
	///////////////////////////////////////    PAYPAL     ///////////////////////////////////////////

	///////////////////////////////////////    SIGNON  ///////////////////////////////////////////
	case "usermap_img":
#		include_once("modules/guestlist/mod_settings/modul_tables.inc.php");
		include('modules/guestlist/usermap_img.php');
	break;
	///////////////////////////////////////    SIGNON  ///////////////////////////////////////////

	///////////////////////////////////////    ADMINPAGE  ///////////////////////////////////////////
	case "export_data":
		if ($_SESSION["auth"]["type"] > 2) include('modules/install/export.php');
	break;
	
	case "modules":
		if ($_SESSION["auth"]["type"] > 2) include('modules/install/modules.php');
	break;

	///////////////////////////////////////    ADMINPAGE  ///////////////////////////////////////////

	///////////////////////////////////////    USRMGR  ///////////////////////////////////////////
	case "myticket_barcode":
#		include_once("modules/usrmgr/mod_settings/modul_tables.inc.php");
		include('modules/usrmgr/myticket_barcode.php');
	break;
	case "myticket":
#		include_once("modules/usrmgr/mod_settings/modul_tables.inc.php");
		include_once("modules/usrmgr/language/usrmgr_lang_".$language.".php");
		include('modules/usrmgr/myticket_popup.php');
	break;
	///////////////////////////////////////    USRMGR  ///////////////////////////////////////////

	///////////////////////////////////////    INCLUDES  ///////////////////////////////////////////
	case "bannerclick":
		include('modules/sponsor/bannerclick.php');
	break;
    case "test":
		include("modules/test.php");
	break;
	///////////////////////////////////////    INCLUDES  ///////////////////////////////////////////

		///////////////////////////////////////    PDF Export Modul     ////////////////////////////
	case "pdf":
#		include("modules/pdf/mod_settings/modul_tables.inc.php");
		if ($_SESSION["auth"]["type"] > 2 || ($auth['userid'] == $_GET['userid'] && $_GET['action'] == "guestcards")){
			if(isset($_GET['userid'])) $_POST['user'] = $_GET['userid'];
			include("modules/pdf/modindex_pdf.php");
		}
	break;
	///////////////////////////////////////    PDF Export Modul     ////////////////////////////////

		///////////////////////////////////////    FOODCENTER  ///////////////////////////////////////////
	case "foodcenter":
		if ($_SESSION["auth"]["type"] > 2){
			if($_GET['action'] == "print")
			include("modules/foodcenter/language/foodcenter_lang_de.php");
			if(file_exists("modules/foodcenter/language/foodcenter_lang_".$language.".php")) include("modules/foodcenter/language/foodcenter_lang_".$language.".php");
			include('modules/foodcenter/print.php');
		}
	break;
	///////////////////////////////////////    FOODCENTER  ///////////////////////////////////////////
}
?>
