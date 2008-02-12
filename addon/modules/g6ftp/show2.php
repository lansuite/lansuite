<?
ob_start();
include "modules/g6ftp/data/createuser.php";
$content = ob_get_contents();
ob_end_clean();

$dsp->NewContent("{$info["caption"]}", "");

$dsp->AddSingleRow($content);

$dsp->AddContent();
?>