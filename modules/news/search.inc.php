<?php
include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2('news');

$ms2->query['from'] = "%prefix%news n LEFT JOIN %prefix%user u ON n.poster=u.userid";
$ms2->query['default_order_by'] = 'DATE DESC';

$ms2->config['EntriesPerPage'] = 20;

$ms2->AddTextSearchField(t('Titel'), array('n.caption' => 'like'));
$ms2->AddTextSearchField(t('Text'), array('n.text' => 'fulltext'));
$ms2->AddTextSearchField(t('Autor'), array('u.username' => '1337', 'u.name' => 'like', 'u.firstname' => 'like'));

$ms2->AddResultField(t('Titel'), 'n.caption');
$ms2->AddSelect('u.userid');
$ms2->AddResultField(t('Autor'), 'u.username', 'UserNameAndIcon');
$ms2->AddResultField(t('Datum'), 'UNIX_TIMESTAMP(n.date) AS date', 'MS2GetDate');

$ms2->AddIconField('details', 'index.php?mod=news&action=comment&newsid=', t('Details'));
if ($auth['type'] >= 2) {
    $ms2->AddIconField('edit', 'index.php?mod=news&action=change&step=2&newsid=', t('Editieren'));
}
if ($auth['type'] >= 3) {
    $ms2->AddIconField('delete', 'index.php?mod=news&action=delete&step=2&newsid=', t('Löschen'));
}

$ms2->PrintSearch('index.php?mod=news&action=search', 'n.newsid');
