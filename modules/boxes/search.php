<?php

$templ['box']['rows'] = "";

$module_row = $db->query("SELECT name FROM {$config["tables"]["modules"]} WHERE active = 1");
while($modules = $db->fetch_array($module_row)){
	$module_array[] .= $modules['name'];
}
$db->free_result($module_row);

$templ['mastersearch']['search']['content']['search_keywords'] = $_POST['search_keywords'];
$templ['mastersearch']['search']['content']['module'] .= "<select name=\"search_module\" class=\"form\" style=\"width: 138px\">\n";
if ($_POST['search_module'] == "user") $s = "selected"; else $s = "";
$templ['mastersearch']['search']['content']['module'] .= "<option value=\"user\" $s>".$lang['boxes']['search_user']."</option>\n";

if(in_array("news",$module_array)){
	if ($_POST['search_module'] == "news") $s = "selected"; else $s = "";
	$templ['mastersearch']['search']['content']['module'] .= "<option value=\"news\" $s>".$lang['boxes']['search_news']."</option>\n";
}
if(in_array("tournament",$module_array)){
	if ($_POST['search_module'] == "tournament") $s = "selected"; else $s = "";
	$templ['mastersearch']['search']['content']['module'] .= "<option value=\"tournament\" $s>".$lang['boxes']['search_tournaments']."</option>\n";
}
if(in_array("poll",$module_array)){
	if ($_POST['search_module'] == "poll") $s = "selected"; else $s = "";
	$templ['mastersearch']['search']['content']['module'] .= "<option value=\"poll\" $s>".$lang['boxes']['search_poll']."</option>\n";
}
if(in_array("faq",$module_array)){
	if ($_POST['search_module'] == "faq") $s = "selected"; else $s = "";
	$templ['mastersearch']['search']['content']['module'] .= "<option value=\"faq\" $s>".$lang['boxes']['search_faq']."</option>\n";
}
if(in_array("server",$module_array)){
	if ($_POST['search_module'] == "server") $s = "selected"; else $s = "";
	$templ['mastersearch']['search']['content']['module'] .= "<option value=\"server\" $s>".$lang['boxes']['search_server']."</option>\n";
}
if(in_array("board",$module_array)){
	if ($_POST['search_module'] == "thread") $s = "selected"; else $s = "";
	$templ['mastersearch']['search']['content']['module'] .= "<option value=\"thread\" $s>".$lang['boxes']['search_thread']."</option>\n";
}
if(in_array("troubleticket",$module_array)){
	if ($_POST['search_module'] == "troubleticket") $s = "selected"; else $s = "";
	$templ['mastersearch']['search']['content']['module'] .= "<option value=\"troubleticket\" $s>".$lang['boxes']['search_troubletickets']."</option>\n";
}
if(in_array("rent",$module_array)){
	if ($_POST['search_module'] == "rent") $s = "selected"; else $s = "";
	$templ['mastersearch']['search']['content']['module'] .= "<option value=\"rent\" $s>".$lang['boxes']['search_rent']."</option>\n";
}
$templ['mastersearch']['search']['content']['module'] .= "</select>\n";

$gd->CreateButton('search');
$box->AddTemplate("box_search_content");
$boxes['search'] .= $box->CreateBox("search",$lang['boxes']['search']);
?>