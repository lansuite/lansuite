<?php

foreach ($_POST[action] as $key => $val) {
	$db->qry("DELETE FROM %prefix%party_prices WHERE price_id = %string%", $key);
}
$func->confirmation('Erfolgreich gel�scht', 'index.php?mod=party');

?>
