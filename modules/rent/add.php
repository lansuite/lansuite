<?php

$dsp->NewContent(t('Verleih'), t('Neuen Artikel zum Verleih eintragen'));

$mf = new \LanSuite\MasterForm();

$mf->AddField(t('Bezeichnung'), 'caption');
$mf->AddField(t('Beschreibung'), 'comment', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
$mf->AddField(t('Menge'), 'quantity');
$mf->AddDropDownFromTable(t('Besitzer'), 'ownerid', 'userid', 'username', 'user', t('Keinem zugeordnet'), 'type >= 2');

$stuffIdParameter = $_GET['stuffid'] ?? 0;
$mf->SendForm('index.php?mod=rent&action=add', 'rentstuff', 'stuffid', $stuffIdParameter);

$dsp->AddBackButton('index.php?mod=rent');
