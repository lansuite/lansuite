<?php
ob_start();

define('PHGDIR', 'ext_scripts/lgsl/');
$use_file = 'index.php?mod=server2'; #basename(__FILE__);
$use_bind = '&';#'?';
require_once(PHGDIR . 'index.php');

$phg_content = ob_get_contents();
ob_end_clean();

/*$style='<style>
table {
  background: #4E5F71;
}
th {
  background: #EEE6E6;
}
td {
  background: #EEE6E6;
}
</style>
';*/
$dsp->AddSingleRow($style . $phg_content);
