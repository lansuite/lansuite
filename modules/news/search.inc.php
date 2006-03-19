<?php
include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2();

$ms2->query['from'] = "{$config["tables"]["news"]} n LEFT JOIN {$config["tables"]["user"]} u ON n.poster=u.userid";

$ms2->config['EntriesPerPage'] = 20;

$ms2->AddTextSearchField('Titel', array('n.caption' => 'like'));
$ms2->AddTextSearchField('Text', array('n.text' => 'like'));
$ms2->AddTextSearchField('Autor', array('u.username' => '1337', 'u.name' => 'like', 'u.firstname' => 'like'));

$ms2->AddResultField('Titel', 'n.caption');
$ms2->AddSelect('u.userid');
$ms2->AddResultField('Autor', 'u.username', 'UserNameAndIcon');
$ms2->AddResultField('Datum', 'n.date', 'MS2GetDate');

if ($auth['type'] >= 2) $ms2->AddIconField('edit', 'index.php?mod=news&action=change&step=2&newsid=', 'Edit');
if ($auth['type'] >= 3) $ms2->AddIconField('delete', 'index.php?mod=news&action=delete&step=2&newsid=', 'Delete');

$ms2->PrintSearch('index.php?mod=news&action=change', 'n.newsid');
?>