<?php
/*************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		1.0
*	Filename: 		modindex_catering.php
*	Module: 		catering
*	Main editor: 		fritzsche@emazynx.de
*	Last change: 		24.05.2003 16:05
*	Description: 		Switches action
*	Remarks: 		none
*
******************************************************************************/

switch($_GET["action"])  { 
	
	default:
		include ("modules/catering/show.php");
		break; 
		
	case "showgrp":
		include ("modules/catering/showfood.php");
		break; 		

	case "addtocart":
		if ($auth['type'] > 0) { 
			include ("modules/catering/showfood.php");
		} else 
			$func->error("ACCESS_DENIED","");
		break; 					
		
	// FROM HERE ALL ACTIONS ONLY ACCESSIBLE BY ADMINS
	case "prioup":
		if ($auth['type'] > 1) { 
			include ("modules/catering/showfood.php");
		} else 
			$func->error("ACCESS_DENIED","");
		break; 

	case "priodown":
		if ($auth['type'] > 1) { 
			include ("modules/catering/showfood.php");
		} else 
			$func->error("ACCESS_DENIED","");
		break; 		
		
	case "newfood":
		if ($auth['type'] > 1) { 
			include ("modules/catering/addfood.php");
		} else 
			$func->error("ACCESS_DENIED","");
 		break; 

	case "newwiz":
		if ($auth['type'] > 1) { 
			include ("modules/catering/addwiz.php");
		} else 
			$func->error("ACCESS_DENIED","");
 		break; 
		
	case "deletefood":
		if ($auth['type'] > 1) { 
			include ("modules/catering/deletefood.php");
		} else 
			$func->error("ACCESS_DENIED","");
		break; 
	
	case "changefood": 
		if ($auth['type'] > 1) { 
			include ("modules/catering/changefood.php");
		} else 
			$func->error("ACCESS_DENIED","");
		break; 
		
	case "lock":
		if ($auth['type'] > 1) { 
			include ("modules/catering/showfood.php");
		} else 
			$func->error("ACCESS_DENIED","");
		break; 		

	case "unlock":
		if ($auth['type'] > 1) { 
			include ("modules/catering/showfood.php");
		} else 
			$func->error("ACCESS_DENIED","");
		break; 		
		
	case "foreignorder":
		if ($auth['type'] > 1) { 
			include ("modules/catering/foreignorder.php");
		} else 
			$func->error("ACCESS_DENIED","");
		break; 	
	
	case "theke":
		if ($auth['type'] > 1) { 
			include ("modules/catering/theke.php");
		} else 
			$func->error("ACCESS_DENIED","");
		break; 		
		
	case "thekenbestellung":
		if ($auth['type'] > 1) { 
			include ("modules/catering/theke.php");
		} else 
			$func->error("ACCESS_DENIED","");
		break; 				


	case "moneycounter":
		if ($auth['type'] > 1) { 
			include ("modules/catering/moneycounter.php");
		} else 
			$func->error("ACCESS_DENIED","");
		break; 				

		
	// *******************************
	// Accounting actions
	// *******************************
	case "accounting":
		if ($auth['type'] > 1) { 
			include ("modules/catering/accounting.php");
		} else 
			$func->error("ACCESS_DENIED","");
		break; 
		
	case "accforuser":
		if ($auth['type'] > 1) { 
			include ("modules/catering/accounting.php");
		} else 
			$func->error("ACCESS_DENIED","");
		break; 
		
	case "savemovement":
		if ($auth['type'] > 1) { 
			include ("modules/catering/accounting.php");
		} else 
			$func->error("ACCESS_DENIED","");
		break; 
		
	// ******************************
	// Balance actions
	// ******************************
	case "showaccountmovements":
		if ($auth['type'] > 0) { 
			include ("modules/catering/balance.php");
		} else 
			$func->error("ACCESS_DENIED","");
		break; 
		
	// *****************************
	// Personal order actions
	// *****************************
	case "showmyorders":
		if ($auth['type'] > 0) { 
			include ("modules/catering/orders.php");
		} else 
			$func->error("ACCESS_DENIED","");
		break; 
	
	case "deleteopenorder":
		if ($auth['type'] > 0) { 
			include ("modules/catering/orders.php");
		} else 
			$func->error("ACCESS_DENIED","");
		break; 
		
		
	// *****************************
	// Admin orderlist actions
	// *****************************
	case "ordersummary":
		if ($auth['type'] > 1) { 
			include ("modules/catering/orderlist.php");
		} else 
			$func->error("ACCESS_DENIED","");
		break; 
	
	case "openorders":
		if ($auth['type'] > 1) { 
			include ("modules/catering/orderlist.php");
		} else 
			$func->error("ACCESS_DENIED","");
		break; 

	case "orderlistdetails":
		if ($auth['type'] > 1) { 
			include ("modules/catering/orderlist.php");
		} else 
			$func->error("ACCESS_DENIED","");
		break; 		
		
	case "orderlistdelete":
		if ($auth['type'] > 1) { 
			include ("modules/catering/orderlist.php");
		} else 
			$func->error("ACCESS_DENIED","");
		break; 			
			
	// change selected open orders to ordered orders
	case "triggerorders":
		if ($auth['type'] > 1) { 
			include ("modules/catering/orderlist.php");
		} else 
			$func->error("ACCESS_DENIED","");
		break; 		
		
	// change selected ordered orders to delivered orders
	case "delivery":
		if ($auth['type'] > 1) { 
			include ("modules/catering/orderlist.php");
		} else 
			$func->error("ACCESS_DENIED","");
		break; 				

	case "notpayedlist":
		if ($auth['type'] > 1) {
			include ("modules/catering/notpayedlist.php");
		} else $func->error("ACCESS_DENIED","");
    break;
} 
	
?>
