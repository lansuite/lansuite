<?php

$importXml = new \LanSuite\XML();
$installImport = new \LanSuite\Module\Install\Import($importXml);
$install = new \LanSuite\Module\Install\Install($installImport);

$dsp->NewContent(t('Webserverkonfiguration und Systemvorraussetzungen überprüfen'), t('Hier kannst du testen, ob Lansuite auf deinem System evtl. Probleme haben wird und bekommst entsprechende Lösungsvorschläge angezeigt.'));

$install->envcheck();

$dsp->AddBackButton("index.php?mod=install");
