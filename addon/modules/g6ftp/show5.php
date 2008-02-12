<?
ob_start();
include "modules/g6ftp/data/success.php";
$content = ob_get_contents();
ob_end_clean();

$dsp->NewContent("{$info["caption"]}", "Du kannst dich nun auf den hier angegeben FTP Server einloggen:");

$dsp->AddSingleRow($content);

$dsp->AddContent();
?>