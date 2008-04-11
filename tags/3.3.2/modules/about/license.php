<?php
$dsp->NewContent($config['lansuite']['version'], 'A web based lanparty administration tool');
$dsp->AddModTpl("about", "license");
$dsp->AddBackButton("index.php?mod=about", "about/license");
$dsp->AddContent();
?>
