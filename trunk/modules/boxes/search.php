<?php
/**
 * Generate Searchbox
 *
 * @package lansuite_core
 * @author knox
 * @version $Id$
 */
 
($_GET['mod'] == 'usrmgr')? $s = 'selected' : $s = '';
$templ['searchbox']['module'] = "<option value=\"index.php?mod=guestlist&amp;action=guestlist\" $s>".t('Benutzer')."</option>\n";

if (in_array("news", $ActiveModules)){
	($_GET['mod'] == 'news')? $s = 'selected' : $s = '';
	$templ['searchbox']['module'] .= "<option value=\"index.php?mod=news&amp;action=search\" $s>".t('News')."</option>\n";
}
if (in_array("server", $ActiveModules)){
	($_GET['mod'] == 'server')? $s = 'selected' : $s = '';
	$templ['searchbox']['module'] .= "<option value=\"index.php?mod=server\" $s>".t('server')."</option>\n";
}
if (in_array("board", $ActiveModules)){
	($_GET['mod'] == 'board')? $s = 'selected' : $s = '';
	$templ['searchbox']['module'] .= "<option value=\"index.php?mod=board&amp;action=forum\" $s>".t('Thread')."</option>\n";
}
if (in_array("troubleticket", $ActiveModules)){
	($_GET['mod'] == 'troubleticket')? $s = 'selected' : $s = '';
	$templ['searchbox']['module'] .= "<option value=\"index.php?mod=troubleticket\" $s>".t('Troubletickets')."</option>\n";
}

$templ['searchbox']['button'] = '<input type="submit" class="Button" name="suchen" value="Suchen" />';
$box->AddTemplate("box_search_content");
?>
