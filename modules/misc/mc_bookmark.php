<?php

switch($_GET['step']){
  case 10:
    include_once('inc/classes/class_masterdelete.php');
    $md = new masterdelete();
    $md->MultiDelete('comments_bookmark', 'bid');
  break;
}

include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2();

$ms2->query['from'] = "{$config["tables"]["comments_bookmark"]} AS b";
$ms2->query['where'] = 'b.userid = '. (int)$auth['userid'];
$ms2->query['default_order_by'] = 'b.relatedto_item';
$ms2->config['EntriesPerPage'] = 20;

$ms2->AddResultField(t('Modul'), 'b.relatedto_item');
$ms2->AddResultField(t('Beitrags ID'), 'b.relatedto_id');
$ms2->AddResultField(t('Internet-Mail'), 'b.email');
$ms2->AddResultField(t('System-Mail'), 'b.sysemail');

if ($auth['type'] >= 3) $ms2->AddMultiSelectAction(t('Löschen'), 'index.php?mod=misc&action=mc_bookmark&step=10', 1);

$ms2->PrintSearch('index.php?mod=misc&action=mc_bookmark', 'b.bid');
?>
