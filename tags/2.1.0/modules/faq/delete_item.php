<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.0
*	Filename: 		delete_item.php
*	Module: 		FAQ
*	Main editor: 		Micheal@one-network.org
*	Last change: 		01.04.2003 13:58
*	Description: 		Removes FAQ Items
*	Remarks:
*
**************************************************************************/

switch($_GET["step"]) {
	
	case 2:
	
	$get_caption = $db->query_first("SELECT caption FROM {$config["tables"]["faq_item"]} WHERE itemid = '{$_GET["itemid"]}'");
	$caption = $get_caption["caption"];
	
		if($caption != "") {
		
			$func->question(str_replace("%ITEMNAME%",$caption,$lang['faq']['del_item_quest']),"index.php?mod=faq&object=item&action=delete_item&itemid={$_GET['itemid']}&step=3","index.php?mod=faq&object=cat&action=delete_cat");
		}
		
			else {
	
				$func->error($lang['faq']['quest_not_exists'],"");	
			}

	break;
	
	case 3:
	
	$get_caption = $db->query_first("SELECT caption FROM {$config["tables"]["faq_item"]} WHERE itemid = '{$_GET["itemid"]}'");
	$caption = $get_caption["caption"];
	
		if($caption != "") {
		
			$del_item = $db->query("DELETE FROM {$config["tables"]["faq_item"]} WHERE itemid = '{$_GET["itemid"]}'");
		
				if ($del_item == true) {
			
						$func->confirmation($lang['faq']['del_item_ok'],"index.php?mod=faq&object=cat&action=delete_cat");
				}
					
			else {
		
				$func->error("DB_ERROR","");
		 		
			}
	
		} // close if caption
		
			else {	
		
				$func->error($lang['faq']['quest_not_exists'],"");
			}	
	
	break;

} // close switch step
?>
