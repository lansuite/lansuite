<?php
$dsp->NewContent(t('Cronjob hinzufügen'), '');

$mf = new \LanSuite\MasterForm();

$mf->AddField(t('Name'), 'name');
$selections = ['sql' => 'SQL-Befehl','php'=>'PHP-Datei'];
$mf->AddField(t('Typ'), 'type', \LanSuite\MasterForm::IS_SELECTION, $selections);
$mf->AddField(t('Statement(SQL)/Datei(PHP)'), 'function');
$mf->AddField(t('Aktiv'), 'active');
$mf->AddField(t('Ausführen täglich, um'), 'runat');

$mf->SendForm('index.php?mod=cron2&action=add', 'cron', 'jobid', $_GET['jobid']);
