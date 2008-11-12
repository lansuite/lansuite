<?php
/**
 * Generate Searchbox
 *
 * @package lansuite_core
 * @author knox
 * @version $Id$
 */

$smarty->assign('value', $_POST['search_input'][0]);

($_GET['mod'] == 'usrmgr')? $s = 'selected' : $s = '';
$module = "<option value=\"index.php?mod=guestlist&amp;action=guestlist\" $s>".t('Benutzer')."</option>\n";

if (in_array("news", $ActiveModules)){
	($_GET['mod'] == 'news')? $s = 'selected' : $s = '';
	$module .= "<option value=\"index.php?mod=news&amp;action=search\" $s>".t('News')."</option>\n";
}
if (in_array("server", $ActiveModules)){
	($_GET['mod'] == 'server')? $s = 'selected' : $s = '';
	$module .= "<option value=\"index.php?mod=server\" $s>".t('server')."</option>\n";
}
if (in_array("board", $ActiveModules)){
	($_GET['mod'] == 'board')? $s = 'selected' : $s = '';
	$module .= "<option value=\"index.php?mod=board&amp;action=forum\" $s>".t('Thread')."</option>\n";
}
if (in_array("troubleticket", $ActiveModules)){
	($_GET['mod'] == 'troubleticket')? $s = 'selected' : $s = '';
	$module .= "<option value=\"index.php?mod=troubleticket\" $s>".t('Troubletickets')."</option>\n";
}

$smarty->assign('module', $module);

$box->AddTemplate($smarty->fetch('modules/boxes/templates/box_search_content.htm'));
?>