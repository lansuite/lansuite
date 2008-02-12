<?
ob_start();
include "modules/g6ftp/data/index.php";
$content = ob_get_contents();
ob_end_clean();

$dsp->NewContent("{$info["caption"]}", "Hier kannst du Dir einen persönlichen FTP Account anlegen.");

$dsp->AddSingleRow($content);

$dsp->AddContent();
?>