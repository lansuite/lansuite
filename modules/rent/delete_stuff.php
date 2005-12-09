<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.1
*	Filename: 			delete_stuff.php
*	Module: 			Verleih/Rent
*	Main editor: 		denny@one-network.org
*	Description: 		delete stuff 
*	Remarks: 		
*
**************************************************************************/


$step 	 = $vars["step"];
$item_id = $vars["itemid"];
$user_id = $vars["userid"];

switch($step) {

	default:

		$mastersearch = new MasterSearch( $vars, "index.php?mod=rent&action=delete_stuff", "index.php?mod=rent&action=delete_stuff&step=2&itemid=", "");
		$mastersearch->LoadConfig( "rentdelstuff", $lang['rent']['show_out_print_form'], $lang['rent']['del_stuff_search_result'] );
//		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();
		$templ['index']['info']['content'] .= $mastersearch->GetReturn();

	
	break;

	
	case 2:
	
		$dialog[2] = $lang['rent']['del_stuff_question'];
	
		$link[0] = "index.php?mod=rent&action=delete_stuff&step=3&itemid=$item_id";
		$link[1] = "index.php?mod=rent&action=delete_stuff&step=10&itemid=$item_id";
		$link[2] = "index.php?mod=rent&action=delete_stuff";
			
		$pic[0]	= "delete";
		$pic[1]	= "edit";
		$pic[2]	= "back";
		
		$func->dialog($dialog,$link,$pic);		
		
	break;
	
	
	case 3:		// abfrage ob eintrag gelöscht werden soll

		$checkempty = $db->query_first("SELECT rented FROM {$config["tables"]["rentstuff"]} WHERE stuffid = '$item_id'");
		$rented = $checkempty["rented"];
		if ($rented > 0) {
			$func->question(str_replace("%RENTED%", $rented ,$lang['rent']['del_stuff_warning']),"index.php?mod=rent&action=delete_stuff&step=5&itemid=$item_id","index.php?mod=rent&action=delete_stuff");
		}
		else
		{
			$step = 5;  // delete it now
		}

	break;

	
	case 10: // bearbeiten

		$stuff = $db->query_first("SELECT caption, comment, quantity, rented FROM {$config["tables"]["rentstuff"]} WHERE stuffid='$item_id'");	

		$dsp->NewContent( $lang['rent']['del_stuff_edit'],$lang['rent']['del_stuff_form_edit']);
		$dsp->SetForm("index.php?mod=rent&action=delete_stuff&step=13&itemid=$item_id");

		if ($stuff["rented"]>0) {
			$dsp->AddSingleRow($lang['rent']['del_stuff_edit_warning']);
			$dsp->AddTextFieldRow("rent_caption",$lang['rent']['addstuff_eq_name'],$stuff["caption"],"","\" disabled");
		}else {
			$dsp->AddTextFieldRow("rent_caption",$lang['rent']['addstuff_eq_name'],$stuff["caption"],"");
		}

		$dsp->AddTextFieldRow("rent_comment",$lang['rent']['addstuff_shortinfo'],$stuff["comment"],"",NULL,1);
		$dsp->AddTextFieldRow("rent_quantity",$lang['rent']['addstuff_eq_quantity'],$stuff["quantity"],"");
		$dsp->AddFormSubmitRow("edit");
		$dsp->AddContent();

	break;


	case 13:	// besitzer wählen

		$caption  = rawurlencode($_POST["rent_caption"]);
		$comment  = rawurlencode($_POST["rent_comment"]);
		$quantity = rawurlencode($_POST["rent_quantity"]);
		
		$questions[0]= $lang['rent']['del_stuff_choise_owner'];
		$questions[1]= $lang['rent']['del_stuff_my_stuff'];
		$questions[2]= $lang['rent']['del_stuff_no_owner'];
		
		$link[0] = "index.php?mod=rent&action=delete_stuff&step=12&cap=$caption&com=$comment&qua=$quantity&itemid=$item_id";
		$link[1] = "index.php?mod=rent&action=delete_stuff&step=15&cap=$caption&com=$comment&qua=$quantity&userid=".$_SESSION["auth"]["userid"]."&itemid=".$item_id;
		$link[2] = "index.php?mod=rent&action=delete_stuff&step=15&cap=$caption&com=$comment&qua=$quantity&userid=&itemid=$item_id";
						
		$func->multiquestion($questions,$link,$lang['rent']['del_stuff_text_owner']);

	break;
	
	case 12:	// orga als besitzer auswählen (änderung)
		$caption  = ($_GET["cap"]);
		$comment  = ($_GET["com"]);
		$quantity = ($_GET["qua"]);

		$mastersearch = new MasterSearch($vars, "index.php?mod=rent&action=delete_stuff&step=12","index.php?mod=rent&action=delete_stuff&step=15&cap=$caption&com=$comment&qua=$quantity&itemid=$item_id&userid=", " AND (type > 1) GROUP BY user_id");
		$mastersearch->LoadConfig("users", "", "");
		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();

		$templ['index']['info']['content'] .= $mastersearch->GetReturn();
	break;
	

	case 15: // änderungen speichern
	
		$caption  = rawurldecode($_GET["cap"]);
		$comment  = rawurldecode($_GET["com"]);
		$quantity = rawurldecode($_GET["qua"]);

		if($user_id!="") $user_chg=", ownerid='$userid'"; else $user_chg="";
		
		$change_it = $db->query("UPDATE {$config["tables"]["rentstuff"]} SET caption = '$caption', comment = '$comment', quantity = '$quantity' $user_chg WHERE stuffid = '$item_id'");
		if($change_it == 1) { $func->confirmation($lang['rent']['del_stuff_edit_ok'],"index.php?mod=rent&action=delete_stuff");
		}
		else
		 $func->error("NO_REFRESH","");
	
	break;
	
	case 5:
	break;
} // switch


switch($step) {

	case 5:		// eintrag löschen

		$db->query("DELETE FROM {$config["tables"]["rentstuff"]} WHERE stuffid = '$item_id'");
		$del_it = $db->query("DELETE FROM {$config["tables"]["rentuser"]} WHERE stuffid = '$item_id'");

		if($del_it == 1) { $func->confirmation($lang['rent']['del_stuff_del_ok'],"index.php?mod=rent&action=delete_stuff");
		}
		else
		 $func->error("NO_REFRESH","");
	
	break;
	
}// switch

?>
