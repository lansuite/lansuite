<?php
$dsp->NewContent($config['lansuite']['version'], 'A web based lanparty administration tool');
$dsp->AddSmartyTpl('license', 'about');
$dsp->AddBackButton("index.php?mod=about", "about/license");
$dsp->AddContent();
