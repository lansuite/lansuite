<?php

$mf = new \LanSuite\MasterForm();

$mf->AddField(t('Forumname'), 'name');
$mf->AddField(t('Untertitel'), 'description', '', \LanSuite\MasterForm::LSCODE_ALLOWED, \LanSuite\MasterForm::FIELD_OPTIONAL);
$selections = array();
$selections['0'] = t('Jeder lesen/schreiben');
$selections['1'] = t('Jeder lesen/ Eingeloggte schreiben');
$selections['2'] = t('Eingeloggte lesen/schreiben');
$selections['3'] = t('Admins lesen/schreiben');
$selections['4'] = t('Super-Admins lesen/schreiben');
$mf->AddField(t('Nur folgende Benutzertypen'), 'need_type', \LanSuite\MasterForm::IS_SELECTION, $selections, \LanSuite\MasterForm::FIELD_OPTIONAL);

$mf->AddDropDownFromTable(t('Nur folgende Gruppen'), 'need_group', 'group_id', 'group_name', 'party_usergroups', t('Alle Gruppen'));

$mf->AddField(t('Position'), 'pos', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);

$mf->AddDropDownFromTable(t('Vorhandene Gruppe'), 'board_group', 'board_group', 'board_group', 'board_forums', t('Neue Gruppe anlegen'));
$mf->AddField(t('Neue Gruppe'), 'group_new', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);

$mf->AdditionalDBPreUpdateFunction = 'Update';
$mf->SendForm('index.php?mod=board&action=add', 'board_forums', 'fid', $_GET['fid']);
