<?php
$dsp->NewContent(t('Cronjob hinzufügen'), '');

$mf = new masterform();

$mf->AddField(t('Name'), 'name');
$mf->AddField(t('Statement'), 'function');
$mf->AddField(t('Aktiv'), 'active');
$mf->AddField(t('Type'), 'type');
$mf->AddField(t('Ausführen täglich, um'), 'runat');

$mf->SendForm('index.php?mod=cron2&action=add', 'cron', 'jobid', $_GET['jobid']);
