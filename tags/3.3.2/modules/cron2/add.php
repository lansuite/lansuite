<?php
$dsp->NewContent(t('Cronjob hinzufügen'), '');

include_once('inc/classes/class_masterform.php');
$mf = new masterform();

$mf->AddField(t('Name'), 'name');
$mf->AddField(t('SQL-Statement'), 'function');
$mf->AddField(t('Aktiv'), 'active');
$mf->AddField(t('Ausführen täglich, um'), 'runat');

$mf->SendForm('index.php?mod=cron2&action=add', 'cron', 'jobid', $_GET['jobid'])
?>