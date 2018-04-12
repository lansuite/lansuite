<?php

function Update($id)
{
    if (!$_POST['board_group']) {
        $_POST['board_group'] = $_POST['group_new'];
    }
    return true;
}

$mf = new masterform();

$mf->AddField(t('Forumname'), 'name');
$mf->AddField(t('Untertitel'), 'description', '', masterform::LSCODE_ALLOWED, masterform::FIELD_OPTIONAL);
$selections = array();
$selections['0'] = t('Jeder lesen/schreiben');
$selections['1'] = t('Jeder lesen/ Eingeloggte schreiben');
$selections['2'] = t('Eingeloggte lesen/schreiben');
$selections['3'] = t('Admins lesen/schreiben');
$selections['4'] = t('Super-Admins lesen/schreiben');
$mf->AddField(t('Nur folgende Benutzertypen'), 'need_type', masterform::IS_SELECTION, $selections, masterform::FIELD_OPTIONAL);

$mf->AddDropDownFromTable(t('Nur folgende Gruppen'), 'need_group', 'group_id', 'group_name', 'party_usergroups', t('Alle Gruppen'));

$mf->AddField(t('Position'), 'pos', '', '', masterform::FIELD_OPTIONAL);

$mf->AddDropDownFromTable(t('Vorhandene Gruppe'), 'board_group', 'board_group', 'board_group', 'board_forums', t('Neue Gruppe anlegen'));
$mf->AddField(t('Neue Gruppe'), 'group_new', '', '', masterform::FIELD_OPTIONAL);

$mf->AdditionalDBPreUpdateFunction = 'Update';
$mf->SendForm('index.php?mod=board&action=add', 'board_forums', 'fid', $_GET['fid']);
