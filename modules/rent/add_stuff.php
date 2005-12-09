<?php
/*************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	 2.0
*	File Version:		 2.0
*	Filename: 			add_stuff.php
*	Module: 			Verleih/Rent
*	Main editor: 		denny@one-network.org
*	Last change: 		
*	Description: 		Adds Equipment	
*	Remarks: 			
*
**************************************************************************/
  

switch($_GET["step"]) {
	
	case 2:
	//  ERRORS
	
	if($_POST["rent_caption"] == "") {	
			$error_rent['caption'] = $lang['rent']['add_stuff_error'];	
			$_GET["step"] = 1;
			}

	if($_POST["rent_quantity"] == "") {		
			$error_rent['quantity'] = $lang['rent']['add_stuff_quantity_error'];	
			$_GET["step"] = 1;
	}
	else {
	
	    if(!is_numeric($_POST["rent_quantity"])) {
	    	$error_rent['quantity'] = $lang['rent']['add_stuff_no_number_error'];	
			$_GET["step"] = 1;
			}

		$i = strlen($_POST["rent_quantity"]);
		if($i > 999) {
			$error_rent['quantity'] = $lang['rent']['add_stuff_max_error'];	
			$_GET["step"] = 1;
			}
	} // else
	
	
	$i = strlen($_POST["rent_caption"]);
	if($i > 50) {
			$error_rent['caption'] = $lang['rent']['add_stuff_length_error'];	
			$_GET["step"] = 1;
			}
		
	$i = strlen($_POST["rent_comment"]);		
	if($i > 100) {
			$error_rent['comment'] = $lang['rent']['add_stuff_length_info_error'];	
			$_GET["step"] = 1;
			}
	
	break;
	
} // close switch


switch($_GET["step"]) {
	
	default:

	$dsp->NewContent( $lang['rent']['addstuff_eq_add'],$lang['rent']['addstuff_eq_add_info']);
	$dsp->SetForm("index.php?mod=rent&action=add_stuff&step=2");
	$dsp->AddTextFieldRow("rent_caption",$lang['rent']['addstuff_eq_name'],$_POST['rent_caption'],$error_rent['caption']);
	$dsp->AddTextFieldRow("rent_comment",$lang['rent']['addstuff_shortinfo'],$_POST["rent_comment"],$error_rent['comment'],NULL,1);
	$dsp->AddTextFieldRow("rent_quantity",$lang['rent']['addstuff_eq_quantity'],$_POST["rent_quantity"],$error_rent['quantity']);
	$dsp->AddFormSubmitRow("add");
	$dsp->AddContent();
		

	break;
	

	case 2:		// besitzer wählen

		$caption  = rawurlencode($_POST["rent_caption"]);
		$comment  = rawurlencode($_POST["rent_comment"]);
		$quantity = rawurlencode($_POST["rent_quantity"]);
	
		$questions[0]= $lang['rent']['del_stuff_choise_owner'];
		$questions[1]= $lang['rent']['del_stuff_my_stuff'];
		$questions[2]= $lang['rent']['del_stuff_no_owner'];
		
		$link[0] = "index.php?mod=rent&action=add_stuff&step=3&cap=$caption&com=$comment&qua=$quantity";
		$link[1] = "index.php?mod=rent&action=add_stuff&step=4&cap=$caption&com=$comment&qua=$quantity&userid=".$_SESSION["auth"]["userid"];
		$link[2] = "index.php?mod=rent&action=add_stuff&step=4&cap=$caption&com=$comment&qua=$quantity&userid=";
						
		$func->multiquestion($questions,$link,$lang['rent']['del_stuff_text_owner']);

	break;
	
	case 3:
		$caption  = ($_GET["cap"]);
		$comment  = ($_GET["com"]);
		$quantity = ($_GET["qua"]);

		$mastersearch = new MasterSearch($vars, "index.php?mod=rent&action=add_stuff&step=3","index.php?mod=rent&action=add_stuff&step=4&cap=$caption&com=$comment&qua=$quantity&userid=", " AND (type > 1) GROUP BY user_id");
		$mastersearch->LoadConfig("users", "", "");
		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();

		$templ['index']['info']['content'] .= $mastersearch->GetReturn();
	break;


	case 4:

		$caption  = rawurldecode($_GET["cap"]);
		$comment  = rawurldecode($_GET["com"]);
		$quantity = rawurldecode($_GET["qua"]);
		$user_id  = $_GET["userid"];

		$add_it = $db->query("INSERT INTO {$config["tables"]["rentstuff"]} SET
								caption = '{$caption}',
								comment = '{$comment}',
								quantity = '{$quantity}',
								ownerid = '{$user_id}'
								");

		if($add_it == 1) { $func->confirmation($lang['rent']['addstuff_ok'],"index.php?mod=rent&action=show_stuff");
		}
		else
		 $func->error("NO_REFRESH","");


	break; // BREAK CASE 2
		
} // close switch step
?>
