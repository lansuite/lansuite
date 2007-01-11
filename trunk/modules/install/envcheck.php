<?php
$dsp->NewContent($lang["install"]["env_caption"], $lang["install"]["env_subcaption"]);

$install->envcheck();

$dsp->AddBackButton("index.php?mod=install");
$dsp->AddContent();
?>
