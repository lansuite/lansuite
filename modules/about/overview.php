<?php
$dsp->NewContent(LANSUITE_VERSION, 'A web based lanparty administration tool');

$dsp->AddFieldsetStart(t('Information'));
$dsp->AddDoubleRow('', '<a href="index.php?mod=about&action=credits">credits</a>');
$dsp->AddDoubleRow('', '<a href="index.php?mod=about&action=design_info">design</a>');
$dsp->AddDoubleRow('', '<a href="index.php?mod=about&action=license">license</a>');
$dsp->AddFieldsetEnd();
