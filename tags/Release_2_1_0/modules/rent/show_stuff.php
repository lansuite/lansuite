<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0.3
*	File Version:		2.2
*	Filename: 			show_stuff.php
*	Module: 			Verleih/Rent
*	Main editor: 		denny@one-network.org
*   Sub editor:         marco@chuchi.tv
*	Description: 		show all stuff thats rentable
*	Remarks: 		
*
**************************************************************************/

$step 	 = $vars["step"];
$item_id = $vars["itemid"];
$user_id = $vars["userid"];

switch($step) {

	default:

		$mastersearch = new MasterSearch( $vars, "index.php?mod=rent&action=show_stuff", "index.php?mod=rent&action=show_stuff&step=2&itemid=", "");
		$mastersearch->LoadConfig( "rentstuff", $lang['rent']['show_stuff_search_eq'], $lang['rent']['show_stuff_search_eq_res'] );
		$mastersearch->Search();
//		$mastersearch->PrintForm();
		$mastersearch->PrintResult();
		$templ['index']['info']['content'] .= $mastersearch->GetReturn();
	
	break;

	
	case 2:		// abfrage ob eintrag verliehen werden soll

		$checkempty = $db->query_first("SELECT quantity FROM {$config["tables"]["rentstuff"]} WHERE stuffid = '$item_id'");
		$quantity = $checkempty["quantity"];
		if ($quantity > 0) {
			$func->question($lang['rent']['show_stuff_question'],"index.php?mod=rent&action=show_stuff&step=3&itemid=$item_id","index.php?mod=rent&action=show_stuff");
		}
		else
		{
			$func->error($lang['rent']['show_stuff_not_rent'],"index.php?mod=rent&action=show_stuff");
		}


	break;


	case 3:		// user auswählen
	
//	 	$vars['search_type'] = "admin";
	 

		$sql = " AND u.type >= '1' GROUP BY email";

		$mastersearch = new MasterSearch( $vars, "index.php?mod=rent&action=show_stuff&step=3","index.php?mod=rent&action=show_stuff&step=4&itemid=$item_id&userid=", $sql );
		$mastersearch->LoadConfig( "users", $lang['rent']['show_stuff_choise_user'], $lang['rent']['show_stuff_search_result']);
		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();
		$templ['index']['info']['content'] .= $mastersearch->GetReturn();
	break;
	
	
	
	case 4:		// set database
	
		$db->query("UPDATE {$config["tables"]["rentstuff"]} SET quantity = quantity-1, rented = rented+1 WHERE stuffid = '$item_id'");

		$add_it = $db->query("INSERT INTO {$config["tables"]["rentuser"]} SET
								stuffid = '{$item_id}',
								userid = '{$user_id}',
								out_orgaid = '{$_SESSION["auth"]["userid"]}',
								back_orgaid = '0'
								");

		if($add_it == 1) { $func->confirmation($lang['rent']['show_stuff_rent_ok'],"index.php?mod=rent&action=show_stuff");
		}
		else
		 $func->error("NO_REFRESH","");
	
	break;
	
}// switch

?>
