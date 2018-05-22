<?php

$dsp->NewContent(t('Party eintragen'), t('Hier kannst du deine Party der Liste hinzufügen'));

$mf = new \LanSuite\MasterForm();

$mf->AddField(t('Partyname'), 'name');
$mf->AddField(t('Partymotto'), 'motto', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
$mf->AddField(t('Zusätzliche Infos'), 'text', '', \LanSuite\MasterForm::LSCODE_ALLOWED, \LanSuite\MasterForm::FIELD_OPTIONAL);
$mf->AddGroup(t('Allgemeine Angaben'));

$mf->AddField(t('Party-Start'), 'start');
$mf->AddField(t('Party-Ende'), 'end');
$mf->AddGroup(t('Datum'));

$mf->AddField(t('Webseite'), 'url');
$mf->AddField(t('Url zum Lansuite-Hauptordner') .HTML_NEWLINE. t('Bsp.: http://deineurl.de/unterordner/'), 'ls_url');
$mf->AddGroup(t('Webseite'));

$mf->AddField(t('Ort'), 'city');
$mf->AddField(t('PLZ'), 'plz');
$mf->AddField(t('Straße'), 'street');
$mf->AddField(t('Hausnummer'), 'hnr');
$mf->AddGroup(t('Adresse der Location'));

if (!$_GET['partyid']) {
    $mf->AddFix('userid', $auth['userid']);
}

$mf->SendForm('index.php?mod=partylist&action=add', 'partylist', 'partyid', $_GET['partyid']);
$dsp->AddBackButton('index.php?mod=partylist');
