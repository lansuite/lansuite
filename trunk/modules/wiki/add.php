<?php

include_once('inc/classes/class_masterform.php');
$mf = new masterform();

$mf->AddField(t('Titel'), 'name');
if ($_GET['postid'] = $mf->SendForm('index.php?mod=wiki&action=add', 'wiki', 'postid', $_GET['postid'])) {
  $_GET['action'] = 'edit';
  $_GET['mf_step'] = '1';
  include_once('modules/wiki/edit.php');
}

?>