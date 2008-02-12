<?php
$dsp->NewContent($lang["install"]["env_caption"], $lang["install"]["env_subcaption"]);

$install->envcheck();

$dsp->AddBackButton("install.php?mod=install", "install/env_check"); 
$dsp->AddContent();
?>
