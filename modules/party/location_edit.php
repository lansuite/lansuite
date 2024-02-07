<?php

$mf = new \LanSuite\MasterForm();

$mf->AddField(t('Name des Veranstaltungsorts'), 'location_name');
$mf->AddDropDownFromTable(t('Infoseite zur Lokation'), 'locationinfo_id', 'infoID', 'caption', 'info');
$mf->AddField(t('Adresse'), 'address');
$mf->AddField(t('Postleitzahl'), 'postcode');
$mf->AddField(t('Rauchen erlaubt'), 'smoking');
$mf->AddField(t('e-Zigaretten erlaubt'), 'ecig');
$mf->AddField(t('Duschen verfügbar'), 'showers');
$mf->AddField(t('Parkplätze verfügbar'), 'parking');
$mf->AddField(t('Separater Schlafbereich verfügbar'), 'sleeparea');
$mf->AddField(t('WLAN verfügbar'), 'wifi');
$mf->AddField(t('Internetbandbreite gesamt in mbit/s'), 'inetbandwidth');


$mf->SendForm('index.php?mod=party&action=location_edit&location_id='. $request->query->getInt('location_id'), 'party_location', 'location_id', $request->query->getInt('location_id'));
$dsp->AddBackButton('index.php?mod=party&action=location');
