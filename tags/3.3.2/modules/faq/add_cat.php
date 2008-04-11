<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.0
*	Filename: 		add_cat.php
*	Module: 		FAQ
*	Main editor: 		Micheal@one-network.org
*	Last change: 		29.03.2003 20:17
*	Description: 		Adds FAQ Cats
*	Remarks:
*
**************************************************************************/


switch($_GET["step"]) {
	
	case 2:
		
	//  ERRORS
	$get_cat_names = $db->query("SELECT name FROM {$config["tables"]["faq_cat"]}");
	
		while($row=$db->fetch_array($get_cat_names)) {
			
			$name = $row["name"];
		
				if($name == $_POST["cat_caption"]) {
				
					$faq_error['cat_caption'] = $lang['faq']['cat_exists'];
					$_GET["step"] = 1;
				}
		}
			
			
		if($_POST["cat_caption"] == "") {
			
			$faq_error['cat_caption']	= $lang['faq']['no_cat_name'];
			
			eval($error);
			
			$_GET["step"] = 1;
			
		}
			
	break;
		
}
	
	
switch($_GET["step"]) {
			
	default:
			
	unset($_SESSION['add_blocker_faqcat']);
			
	$dsp->NewContent($lang['faq']['add_cat_caption'],$lang['faq']['add_cat_subcaption']);
	$dsp->SetForm("index.php?mod=faq&object=cat&action=add_cat&step=2");
	$dsp->AddTextFieldRow("cat_caption",$lang['faq']['new_cat'],$_POST['cat_caption'],$faq_error['cat_caption']);
	$dsp->AddFormSubmitRow("add");
	$dsp->AddContent();
			
	break;
			
	case 2:
			
	$courent_date = date("U");
			
		if($_SESSION["add_blocker_faqcat"] != 1) {
				
			$add_it = $db->query("INSERT INTO {$config["tables"]["faq_cat"]} SET
										name = '{$_POST["cat_caption"]}',
										poster = '{$_SESSION["auth"]["userid"]}',
										date = '$courent_date',
										catid = '$catid'
										");
		
			if($add_it == 1) { $func->confirmation($lang['faq']['add_cat_ok'],"");
							
				$_SESSION['add_blocker_faqcat'] = 1;	
									
			} 
		}
		
			else {
			
				$func->error("NO_REFRESH","");
			}
		
	
} // close switch

?>
