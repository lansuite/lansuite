<?php
include_once('inc/classes/class_masterform.php');
$mf = new masterform();

$mf->AddField(t('Forumname'), 'name');
$mf->AddField(t('Untertitel'), 'description', '', LSCODE_ALLOWED, FIELD_OPTIONAL);
$selections = array();
$selections['0'] = t('Jeder lesen/schreiben');
$selections['1'] = t('Jeder lesen/ Eingeloggte schreiben');
$selections['2'] = t('Eingeloggte lesen/schreiben');
$selections['3'] = t('Organisatoren lesen/schreiben');
$mf->AddField(t('Zugriff für'), 'need_type', IS_SELECTION, $selections, FIELD_OPTIONAL);
$mf->AddField(t('Position'), 'pos', '', '', FIELD_OPTIONAL);

$mf->SendForm('index.php?mod=board&action=add', 'board_forums', 'fid', $_GET['fid']);

?>