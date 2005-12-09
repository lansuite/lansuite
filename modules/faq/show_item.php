<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.0
*	Filename: 		show.php
*	Module: 		FAQ
*	Main editor: 		Micheal@one-network.org
*	Last change: 		01.04.2003 14:00
*	Description: 		Shows a single Item
*	Remarks:		add. function to comment this item
*
**************************************************************************/


$get_data = $db->query_first("SELECT caption,text FROM {$config["tables"]["faq_item"]} WHERE itemid = '${_GET["itemid"]}'");
	
	$templ["faq"]["show"]["caption"] 	= $func->text2html($get_data["caption"]);
	$templ["faq"]["show"]["text"] 		= $func->text2html($get_data["text"]);

	$dsp->NewContent($lang['faq']['show_longcaption']);
	$buttons = $dsp->FetchButton("index.php?mod=faq","back");
	
	if($_SESSION["auth"]["type"] > 1){

		$buttons .= $dsp->FetchButton("index.php?mod=faq&object=item&action=change_item&step=2&itemid=" . $_GET["itemid"],"edit");
		$buttons .= $dsp->FetchButton("index.php?mod=faq&object=item&action=delete_item&step=2&itemid=" . $_GET["itemid"],"delete");
	
	}
	
	if($_GET['mcact'] == "show" OR $_GET['mcact'] == "") {
		
		$dsp->AddSingleRow($dsp->FetchModTpl("faq","faq_show_single"));
		$dsp->AddSingleRow($buttons);
		$dsp->AddContent();

	}

include("modules/mastercomment/class_mastercomment.php");
$comment = new Mastercomment($vars,"index.php?mod=faq&action=comment&itemid=" . $_GET["itemid"],"faq",$_GET["itemid"],$func->text2html($get_data["caption"]));
$comment->action();
?>
