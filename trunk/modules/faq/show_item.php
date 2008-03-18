<?php

$get_data = $db->query_first("SELECT caption,text FROM {$config["tables"]["faq_item"]} WHERE itemid = '${_GET["itemid"]}'");
	
$templ["faq"]["show"]["caption"] 	= $func->text2html($get_data["caption"]);
$templ["faq"]["show"]["text"] 		= $func->text2html($get_data["text"]);

$dsp->NewContent(t('<b>F</b>requently <b>A</b>sked <b>Q</b>uestions'));
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

include('inc/classes/class_mastercomment.php');
new Mastercomment('faq', $_GET['itemid']);
?>