<?php
$selectrequire = array();
$selectrequire['0'] = t('Alle');
$selectrequire['2'] = t('Admins und Superadmins');
$selectrequire['3'] = t('Superadmins');

$mf = new \LanSuite\MasterForm();

$mf->AddField(t('Name des Veranstaltungsorts'), 'location_name');
$mf->AddField(t('Adresse'), 'address');
$mf->AddField(t('Postleitzahl'), 'postcode');


$mf->AddDropDownFromTable(t('Infoseite zur Lokation'), 'infoID', 'infoID', 'caption', 'info');

$mf->SendForm('index.php?mod=party&action=location_edit&location_id='. $request->query->getInt('location_id'), 'party_location', 'location_id', $request->query->getInt('location_id'));
$dsp->AddBackButton('index.php?mod=party&action=location');
