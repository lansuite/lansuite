<?php

if($cfg['sys_barcode_on']){
	$dsp->AddBarcodeForm("<strong>" . $lang['barcode']['barcode'] . "</strong>","","index.php?mod=usrmgr&action=details&userid=");
}
include_once('modules/usrmgr/search.inc.php');
?>