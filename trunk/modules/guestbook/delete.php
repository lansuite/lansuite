<?php

foreach ($_POST['action'] as $key => $val) {
	$db->query("DELETE FROM {$config["tables"]["guestbook"]} WHERE guestbookid = '$key'");
}
$func->confirmation('Erfolgreich gelöscht', 'index.php?mod=guestbook');
?>