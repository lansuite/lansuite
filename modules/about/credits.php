<?php
$dsp->NewContent(LANSUITE_VERSION, 'A web based lanparty administration tool');

$dsp->AddSingleRow('<b>Contact</b>', 'align="center"');
$dsp->AddSingleRow('Internet/Development: <a href="https://github.com/lansuite/lansuite" title="lansuite at Github">https://github.com/lansuite/lansuite</a>', 'align="center"');

$dsp->AddSingleRow('<b>Thanks to all project contributos</b>', 'align="center"');
$dsp->AddSingleRow('Please head over to <a href="https://github.com/lansuite/lansuite/blob/master/CONTRIBUTORS.md" title="All lansuite contributors">project contributors</a> to see the full list of project contributors.', 'align="center"');

$dsp->AddBackButton("index.php?mod=about", "about/credits");
