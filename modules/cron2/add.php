<?php
$dsp->NewContent(t('Cronjob hinzufügen'), '');

$mf = new \LanSuite\MasterForm();

$mf->AddField(t('Name'), 'name');
$mf->AddField(t('Statement'), 'function');
$mf->AddField(t('Aktiv'), 'active', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
$mf->AddField(
    t('Typ'),
    'type',
    \LanSuite\MasterForm::IS_SELECTION,
    array('php'=>'php','sql'=>'sql'),
    \LanSuite\MasterForm::FIELD_OPTIONAL
);
$mf->AddField(t('Ausführen täglich, um'), 'runat');

$mf->SendForm('index.php?mod=cron2&action=add', 'cron', 'jobid', $_GET['jobid']);
