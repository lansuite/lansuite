<?php

foreach ($_POST[action] as $key => $val) {
	$db->query("DELETE FROM {$config["tables"]["partys"]} WHERE party_id = ". (int)$key);
}
$func->confirmation('Erfolgreich gel�scht', 'index.php?mod=signon&action=party');

?>
