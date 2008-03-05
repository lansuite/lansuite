<?php
if (file_exists("modules/usrmgr/language/usrmgr_lang_de.php")) include_once("modules/usrmgr/language/usrmgr_lang_de.php");
if ($language != "de" and file_exists("modules/usrmgr/language/usrmgr_lang_{$language}.php")) include_once("modules/usrmgr/language/usrmgr_lang_{$language}.php");
include_once('modules/usrmgr/details.php');
?>
