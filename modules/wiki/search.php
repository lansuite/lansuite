<?php
include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2('wiki');

$ms2->query['from'] = "%prefix%wiki AS w
  LEFT JOIN %prefix%wiki_versions AS v ON w.postid = v.postid
  LEFT JOIN %prefix%user AS u ON v.userid = u.userid";
$ms2->query['default_order_by'] = 'v.date DESC';

$ms2->config['EntriesPerPage'] = 30;

$ms2->AddTextSearchField(t('Titel'), array('w.name' => 'like'));
$ms2->AddTextSearchField(t('Text'), array('v.text' => 'fulltext'));

$ms2->AddResultField(t('Titel'), 'w.name');
$ms2->AddResultField(t('Version'), 'MAX(v.versionid) AS versionid');
$ms2->AddResultField(t('Letzer Autor'), 'u.username', 'UserNameAndIcon');
$ms2->AddResultField(t('Letzte Änderung'), 'UNIX_TIMESTAMP(v.date) AS date', 'MS2GetDate');

$ms2->AddIconField('details', 'index.php?mod=wiki&action=show&postid=', t('Details'));
if ($auth['type'] >= 3) {
    $ms2->AddIconField('delete', 'index.php?mod=wiki&action=delete&step=2&postid=', t('Löschen'));
}

$ms2->PrintSearch('index.php?mod=wiki&action=search', 'w.postid');
