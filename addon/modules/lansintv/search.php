<?php

$lansintv_admin = $db->query_first("SELECT download_prefix FROM {$config['tables']['lansintv_admin']}");

include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2('news');

$ms2->query['from'] = "{$config['tables']['lansintv']} AS l
  LEFT JOIN {$config["tables"]["user"]} AS u ON l.uploader = u.userid";

$ms2->AddTextSearchField('Pfad', array('l.pfad' => 'like'));

$ms2->AddSelect('u.userid');
$ms2->AddResultField('Pfad', 'l.pfad');
$ms2->AddResultField('Votes', 'l.votes');
$ms2->AddResultField('Uploader', 'u.username', 'UserNameAndIcon');

$ms2->AddIconField('details', $lansintv_admin["download_prefix"] . "/", $lang['ms2']['details']);

$ms2->PrintSearch('index.php?mod=lansintv&action=search', 'l.pfad');
?>