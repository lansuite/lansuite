<?php

include_once('modules/install/class_install.php');
$install = new Install();

$dsp->NewContent(t('Webserverkonfiguration und Systemvorraussetzungen überprüfen'), t('Hier kannst du testen, ob Lansuite auf deinem System evtl. Probleme haben wird und bekommst entsprechende Lösungsvorschläge angezeigt.'));

$install->envcheck();

$dsp->AddBackButton("index.php?mod=install");
$dsp->AddContent();
