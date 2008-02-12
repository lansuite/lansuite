<?
ob_start();
include "modules/g6ftp/data/nousername.php";
$content = ob_get_contents();
ob_end_clean();

$dsp->NewContent("{$info["caption"]}", "Sicherheits Meldung / Fehler");

$dsp->AddSingleRow($content);

$dsp->AddContent();
?>