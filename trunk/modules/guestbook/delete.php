<?php

foreach ($_POST['action'] as $key => $val) {
	$db->qry("DELETE FROM %prefix%guestbook WHERE guestbookid = %string%", $key);
}
$func->confirmation('Erfolgreich gelöscht', 'index.php?mod=guestbook');
?>