<?php
ob_start();

define('PHGDIR', 'ext_scripts/lgsl/');
$use_file = 'index.php?mod=server2'; #basename(__FILE__);
$use_bind = '&';#'?';
require_once(PHGDIR . 'admin.php');

$phg_content = ob_get_contents();
ob_end_clean();
$dsp->AddSingleRow($phg_content);
