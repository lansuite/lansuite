<?php

foreach ($_POST[action] as $key => $val) {
	$db->qry("DELETE FROM %prefix%partys WHERE party_id = %string%", $key);
}
$func->confirmation('Erfolgreich gelöscht', 'index.php?mod=party');

?>
