<?php

$get_data = $db->qry_first("SELECT caption,text FROM %prefix%faq_item WHERE itemid = %int%", $_GET['itemid']);
$framework->AddToPageTitle($get_data["caption"]);

$dsp->NewContent(t('<b>F</b>requently <b>A</b>sked <b>Q</b>uestions'));
$buttons = $dsp->FetchButton("index.php?mod=faq","back");

if ($auth["type"] > 1){
	$buttons .= $dsp->FetchButton("index.php?mod=faq&object=item&action=change_item&step=2&itemid=" . $_GET["itemid"],"edit");
	$buttons .= $dsp->FetchButton("index.php?mod=faq&object=item&action=delete_item&step=2&itemid=" . $_GET["itemid"],"delete");
}

if ($_GET['mcact'] == "show" OR $_GET['mcact'] == "") {
	$dsp->AddFieldsetStart($func->text2html($get_data["caption"]));
	$dsp->AddSingleRow('<br>'. $func->text2html($get_data["text"]) .'<br>');
	$dsp->AddSingleRow($buttons);
	$dsp->AddFieldsetEnd();
}

include('inc/classes/class_mastercomment.php');
new Mastercomment('faq', $_GET['itemid']);
?>
