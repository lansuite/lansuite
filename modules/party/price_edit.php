<?php

$selectrequire = array();
$selectrequire['0'] = t('Alle');
$selectrequire['2'] = t('Admins und Superadmins');
$selectrequire['3'] = t('Superadmins');

if (!$_GET['party_id']) {
    $_GET['party_id'] = $party->party_id;
}

$mf = new masterform();

$mf->AdditionalKey = 'party_id = '. (int)$_GET['party_id'];

$dsp->AddDoubleRow('Party', $party->data['name']);

$mf->AddField(t('Text für Eintrittspreis'), 'price_text');
$mf->AddField(t('Preis'), 'price');

$mf->AddDropDownFromTable(t('Gruppenname'), 'group_id', 'group_id', 'group_name', 'party_usergroups');
$mf->AddField(t('Sichtbar für'), 'requirement', IS_SELECTION, $selectrequire, 1);
$mf->AddField(t('Gültig bis'), 'enddate');

$mf->SendForm('index.php?mod=party&action=price_edit&party_id='. $_GET['party_id'], 'party_prices', 'price_id', $_GET['price_id']);
$dsp->AddBackButton('index.php?mod=party&action=price&party_id='. $_GET['party_id']);
$dsp->AddContent();
