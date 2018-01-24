<?php
$dsp->NewContent(t('Verleih'), t('Neuen Artikel zum Verleih eintragen'));

$mf = new masterform();

$mf->AddField(t('Bezeichnung'), 'caption');
$mf->AddField(t('Beschreibung'), 'comment', '', '', FIELD_OPTIONAL);
$mf->AddField(t('Menge'), 'quantity');
$mf->AddDropDownFromTable(t('Besitzer'), 'ownerid', 'userid', 'username', 'user', t('Keinem zugeordnet'), 'type >= 2');

$mf->SendForm('index.php?mod=rent&action=add', 'rentstuff', 'stuffid', $_GET['stuffid']);

$dsp->AddBackButton('index.php?mod=rent');
$dsp->AddContent();
