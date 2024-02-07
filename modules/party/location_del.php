<?php

foreach ($_POST['action'] as $key => $val) {
    $database->query("DELETE FROM %prefix%party_location WHERE location_id = ?", $key);
}
$func->confirmation('Erfolgreich gelöscht', 'index.php?mod=party');
