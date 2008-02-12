<?php

ob_start();
include_once('ext_scripts/phgstats/index.php');
$phgstats = ob_get_contents();
ob_end_clean();
$dsp->AddSingleRow($phgstats);
$dsp->AddContent();

?>