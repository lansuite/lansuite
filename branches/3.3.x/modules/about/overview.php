<?php
$dsp->NewContent($config['lansuite']['version'], 'A web based lanparty administration tool');

$dsp->AddFieldsetStart(t('Information'));
$dsp->AddDoubleRow('', '<a href="?mod=about&action=credits">credits</a>');
$dsp->AddDoubleRow('', '<a href="?mod=about&action=design_info">design</a>');
$dsp->AddDoubleRow('', '<a href="?mod=about&action=license">license</a>');
$dsp->AddFieldsetEnd();
?>