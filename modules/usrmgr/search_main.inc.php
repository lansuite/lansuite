<?php
include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2('usrmgr');

$ms2->query['from'] = "{$config['tables']['user']} AS u LEFT JOIN {$config['tables']['party_user']} AS p ON u.userid = p.user_id";

$ms2->config['EntriesPerPage'] = 20;

$ms2->AddTextSearchField($lang['usrmgr']['userid'], array('u.userid' => 'exact'));
$ms2->AddTextSearchField($lang['usrmgr']['add_username'], array('u.username' => '1337'));
$ms2->AddTextSearchField($lang['usrmgr']['name'], array('u.name' => 'like', 'u.firstname' => 'like'));

$ms2->AddResultField($lang['usrmgr']['add_username'], 'u.username');
$ms2->AddResultField($lang['usrmgr']['add_firstname'], 'u.firstname');
$ms2->AddResultField($lang['usrmgr']['add_lastname'], 'u.name');
?>