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
$mf->AddField(t('Untertitel'), 'description', '', LSCODE_ALLOWED, FIELD_OPTIONAL);
$selections = array();
$selections['0'] = t('Jeder lesen/schreiben');
$selections['1'] = t('Jeder lesen/ Eingeloggte schreiben');
$selections['2'] = t('Eingeloggte lesen/schreiben');
$selections['3'] = t('Admins lesen/schreiben');
$selections['4'] = t('Super-Admins lesen/schreiben');
$mf->AddField(t('Nur folgende Benutzertypen'), 'need_type', IS_SELECTION, $selections, FIELD_OPTIONAL);

$mf->AddDropDownFromTable(t('Nur folgende Gruppen'), 'need_group', 'group_id', 'group_name', 'party_usergroups', t('Alle Gruppen'));

$mf->AddField(t('Position'), 'pos', '', '', FIELD_OPTIONAL);

$mf->AddDropDownFromTable(t('Vorhandene Gruppe'), 'board_group', 'board_group', 'board_group', 'board_forums', t('Neue Gruppe anlegen'));
$mf->AddField(t('Neue Gruppe'), 'group_new', '', '', FIELD_OPTIONAL);

$mf->AdditionalDBPreUpdateFunction = 'Update';
$mf->SendForm('index.php?mod=board&action=add', 'board_forums', 'fid', $_GET['fid']);
