<?php

include_once('modules/install/class_install.php');
$install = new Install();

$dsp->NewContent(t('Webserverkonfiguration und Systemvorraussetzungen überprüfen'), t('Hier können Sie testen, ob Lansuite auf ihrem System evtl. Probleme haben wird und bekommen entsprechende Lösungsvorschläge angezeigt.'));

$install->envcheck();

$dsp->AddBackButton("index.php?mod=install");
$dsp->AddContent();
?>
