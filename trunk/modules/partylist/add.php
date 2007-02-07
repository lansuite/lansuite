<?php
$LSCurFile = __FILE__;

$dsp->NewContent(t('Party eintragen'), t('Hier können Sie Ihre Party der Liste hinzufügen'));

include_once('inc/classes/class_masterform.php');
$mf = new masterform();

$mf->AddField(t('Partyname'), 'name');
$mf->AddField(t('Partymotto'), 'motto', '', '', FIELD_OPTIONAL);
$mf->AddField(t('Zusätzliche Infos'), 'text', '', LSCODE_ALLOWED, FIELD_OPTIONAL);
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
$dsp->AddContent();
?>
