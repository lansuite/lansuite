<?php
// Multiparty or Singelparty
if ($cfg['signon_multiparty']) include("modules/signon/show_party.php");
else include("modules/signon/add.php");
?>
	
