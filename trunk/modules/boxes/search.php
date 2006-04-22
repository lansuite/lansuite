<?php

$templ['box']['rows'] = "";

$module_row = $db->query("SELECT name FROM {$config["tables"]["modules"]} WHERE active = 1");
while($modules = $db->fetch_array($module_row)){
	$module_array[] .= $modules['name'];
}
$db->free_result($module_row);


($_GET['mod'] == 'usrmgr')? $s = 'selected' : $s = '';
$templ['searchbox']['module'] = "<option value=\"index.php?mod=guestlist&action=guestlist\" $s>".$lang['boxes']['search_user']."</option>\n";

/*if (in_array("news", $module_array)){
	($_GET['mod'] == 'news')? $s = 'selected' : $s = '';
	$templ['searchbox']['module'] .= "<option value=\"news\" $s>".$lang['boxes']['search_news']."</option>\n";
}*/
/*if (in_array("tournament2", $module_array)){
	($_GET['mod'] == 'tournament2')? $s = 'selected' : $s = '';
	$templ['searchbox']['module'] .= "<option value=\"tournament\" $s>".$lang['boxes']['search_tournaments']."</option>\n";
}*/
/*if (in_array("poll", $module_array)){
	($_GET['mod'] == 'poll')? $s = 'selected' : $s = '';
	$templ['searchbox']['module'] .= "<option value=\"poll\" $s>".$lang['boxes']['search_poll']."</option>\n";
}*/
/*if (in_array("faq", $module_array)){
	($_GET['mod'] == 'faq')? $s = 'selected' : $s = '';
	$templ['searchbox']['module'] .= "<option value=\"faq\" $s>".$lang['boxes']['search_faq']."</option>\n";
}*/
if (in_array("server", $module_array)){
	($_GET['mod'] == 'server')? $s = 'selected' : $s = '';
	$templ['searchbox']['module'] .= "<option value=\"index.php?mod=server\" $s>".$lang['boxes']['search_server']."</option>\n";
}
if (in_array("board", $module_array)){
	($_GET['mod'] == 'board')? $s = 'selected' : $s = '';
	$templ['searchbox']['module'] .= "<option value=\"index.php?mod=board&action=forum\" $s>".$lang['boxes']['search_thread']."</option>\n";
}
if (in_array("troubleticket", $module_array)){
	($_GET['mod'] == 'troubleticket')? $s = 'selected' : $s = '';
	$templ['searchbox']['module'] .= "<option value=\"index.php?mod=troubleticket\" $s>".$lang['boxes']['search_troubletickets']."</option>\n";
}
/*if (in_array("rent", $module_array)){
	($_GET['mod'] == 'rent')? $s = 'selected' : $s = '';
	$templ['searchbox']['module'] .= "<option value=\"rent\" $s>".$lang['boxes']['search_rent']."</option>\n";
}*/

$gd->CreateButton('search');
$box->AddTemplate("box_search_content");
$boxes['search'] .= $box->CreateBox("search",$lang['boxes']['search']);
?>