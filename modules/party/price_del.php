<?php

foreach ($_POST['action'] as $key => $val) {
    $database->query("DELETE FROM %prefix%party_prices WHERE price_id = ?", [$key]);
}
$func->confirmation('Erfolgreich gelöscht', 'index.php?mod=party');
