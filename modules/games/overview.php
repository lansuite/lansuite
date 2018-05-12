<?php

$dsp->NewContent(t('Games-Übersicht'), t('Hier findest du ein paar kleine Webspiele, um sich die Zeit zu vertreiben'));
$dsp->AddSingleRow($smarty->fetch('modules/games/templates/overview.htm'));
