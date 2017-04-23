<?php
/**
 * Generate Searchbox
 *
 * @package lansuite_core
 * @author knox
 * @version $Id: search.php 1739 2008-11-12 22:59:07Z jochenjung $
 */

$smarty->assign('value', $_POST['search_input'][0]);

($_GET['mod'] == 'usrmgr')? $s = 'selected' : $s = '';
$module = "<option value=\"guestlist|guestlist\" $s>".t('Benutzer')."</option>\n";

if ($func->isModActive('news')) {
    ($_GET['mod'] == 'news')? $s = 'selected' : $s = '';
    $module .= "<option value=\"news|search\" $s>".t('News')."</option>\n";
}
if ($func->isModActive('server')) {
    ($_GET['mod'] == 'server')? $s = 'selected' : $s = '';
    $module .= "<option value=\"server\" $s>".t('server')."</option>\n";
}
if ($func->isModActive('board')) {
    ($_GET['mod'] == 'board')? $s = 'selected' : $s = '';
    $module .= "<option value=\"board|forum\" $s>".t('Thread')."</option>\n";
}
if ($func->isModActive('troubleticket')) {
    ($_GET['mod'] == 'troubleticket')? $s = 'selected' : $s = '';
    $module .= "<option value=\"troubleticket\" $s>".t('Troubletickets')."</option>\n";
}

$smarty->assign('module', $module);

$box->AddTemplate($smarty->fetch('modules/boxes/templates/box_search_content.htm'));
