<?php

include_once("modules/usrmgr/language/usrmgr_lang_de.php");
if ($language != "de" and file_exists("modules/usrmgr/usrmgr_lang_$language.php")) include_once("modules/usrmgr/usrmgr_lang_$language.php");

if ($auth['login']) {
  $_GET['user_id'] = $auth['userid'];
  include_once("modules/usrmgr/party.php");

} else {
  include_once ("modules/usrmgr/add.php");
}

?>