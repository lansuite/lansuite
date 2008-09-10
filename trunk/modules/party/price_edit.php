<?php

$selectrequire = array();
$selectrequire['0'] = t('Alle');
$selectrequire['2'] = t('Admins und Superadmins');
$selectrequire['3'] = t('Superadmins');

if (!$_GET['party_id']) $_GET['party_id'] = $party->party_id;

include_once('inc/classes/class_masterform.php');
$mf = new masterform();

$mf->AdditionalKey = 'party_id = '. (int)$_GET['party_id'];

$dsp->AddDoubleRow('Party', $party->data['name']);

$mf->AddField(t('Text für Eintrittspreis'), 'price_text');
$mf->AddField(t('Preis'), 'price');
#$mf->AddField(t('Beschreibung eines Depots'), 'depot_desc');
#$mf->AddField(t('Depotpreis (Leer lassen, wenn es kein Depot gibt.)'), 'depot_price');

$selections = array();
$res = $db->qry("SELECT * FROM %prefix%party_usergroups");
while ($row = $db->fetch_array($res)) {
  $selections[$row['group_id']] = $row['group_name'];
}
$mf->AddField(t('Gruppenname'), 'group_id', IS_SELECTION, $selections, 1);
$mf->AddField(t('Sichtbar für'), 'requirement', IS_SELECTION, $selectrequire, 1);
$mf->AddField(t('Gültig bis'), 'enddate');

$mf->SendForm('index.php?mod=party&action=price_edit', 'party_prices', 'price_id', $_GET['price_id']);
$dsp->AddBackButton('index.php?mod=party&action=price');
$dsp->AddContent();

?>