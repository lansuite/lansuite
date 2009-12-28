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
	$get_cat_names = $db->qry("SELECT name FROM %prefix%faq_cat");
	
		while($row=$db->fetch_array($get_cat_names)) {
			
			$name = $row["name"];
		
				if($name == $_POST["cat_caption"]) {
				
					$faq_error['cat_caption'] = t('Dieser Kategoriename existiert bereits');
					$_GET["step"] = 1;
				}
		}
			
			
		if($_POST["cat_caption"] == "") {
			
			$faq_error['cat_caption']	= t('Bitte geben Sie einen Namen für die neue Kategorie ein');
			
			eval($error);
			
			$_GET["step"] = 1;
			
		}
			
	break;
		
}
	
	
switch($_GET["step"]) {
			
	default:
			
	unset($_SESSION['add_blocker_faqcat']);
			
	$dsp->NewContent(t('Kategorie hinzufügen'),t(' Um eine Kategorie hinzuzufügen, füllen Sie bitte das folgende Formular aus. Für das Feld Kategoriename stehen Ihnen 30 Zeichen zur Verfügung.'));
	$dsp->SetForm("index.php?mod=faq&object=cat&action=add_cat&step=2");
	$dsp->AddTextFieldRow("cat_caption",t('Neue Kategorie'),$_POST['cat_caption'],$faq_error['cat_caption']);
	$dsp->AddFormSubmitRow("add");
	$dsp->AddContent();
			
	break;
			
	case 2:
			
	$courent_date = date("U");
			
		if($_SESSION["add_blocker_faqcat"] != 1) {
				
			$add_it = $db->qry("INSERT INTO %prefix%faq_cat SET
										name = %string%,
										poster = %int%,
										date = %string%,
										catid = %int%
										", $_POST["cat_caption"], $_SESSION["auth"]["userid"], $courent_date, $catid);
		
			if($add_it == 1) { $func->confirmation(t('Die Kategorie wurde erfolgreich eingetragen'),"");
							
				$_SESSION['add_blocker_faqcat'] = 1;	
									
			} 
		}
		
			else {
			
				$func->error("NO_REFRESH");
			}
		
	
} // close switch

?>