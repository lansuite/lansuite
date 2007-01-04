<?php

$templ['box']['rows'] = '';

($_GET['mod'] == 'usrmgr')? $s = 'selected' : $s = '';
$templ['searchbox']['module'] = "<option value=\"index.php?mod=guestlist&action=guestlist\" $s>".t('Benuter')."</option>\n";

if (in_array("news", $ActiveModules)){
	($_GET['mod'] == 'news')? $s = 'selected' : $s = '';
	$templ['searchbox']['module'] .= "<option value=\"index.php?mod=news&action=search\" $s>".t('News')."</option>\n";
}
if (in_array("server", $ActiveModules)){
	($_GET['mod'] == 'server')? $s = 'selected' : $s = '';
	$templ['searchbox']['module'] .= "<option value=\"index.php?mod=server\" $s>".t('server')."</option>\n";
}
if (in_array("board", $ActiveModules)){
	($_GET['mod'] == 'board')? $s = 'selected' : $s = '';
	$templ['searchbox']['module'] .= "<option value=\"index.php?mod=board&action=forum\" $s>".t('Thread')."</option>\n";
}
if (in_array("troubleticket", $ActiveModules)){
	($_GET['mod'] == 'troubleticket')? $s = 'selected' : $s = '';
	$templ['searchbox']['module'] .= "<option value=\"index.php?mod=troubleticket\" $s>".t('Troubletickets')."</option>\n";
}

$gd->CreateButton('search');
$box->AddTemplate("box_search_content");
$boxes['search'] .= $box->CreateBox("search",t('Suche'));
?>