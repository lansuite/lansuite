<?php
// Multiparty or Singelparty
//if ($cfg['signon_multiparty']) include("modules/signon/show_party.php");
//else

if ($auth['login']) {
  $_GET['user_id'] = $auth['userid'];
  include("modules/usrmgr/language/usrmgr_lang_de.php");
  include("modules/usrmgr/party.php");
} else include("modules/signon/add.php");
?>
