<?php

$install = new \LanSuite\Module\Install\Install();

$dsp->NewContent(t('Webserverkonfiguration und Systemvorraussetzungen überprüfen'), t('Hier kannst du testen, ob Lansuite auf deinem System evtl. Probleme haben wird und bekommst entsprechende Lösungsvorschläge angezeigt.'));
$install->envcheck($config);

$dsp->AddBackButton("index.php?mod=install");
