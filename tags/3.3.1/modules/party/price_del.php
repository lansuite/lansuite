<?php

foreach ($_POST[action] as $key => $val) {
	$db->query("DELETE FROM {$config["tables"]["party_prices"]} WHERE price_id = ". (int)$key);
}
$func->confirmation('Erfolgreich gelöscht', 'index.php?mod=party');

?>
