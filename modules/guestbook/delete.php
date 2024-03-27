<?php

foreach ($_POST['action'] as $key => $val) {
    $database->query("DELETE FROM %prefix%guestbook WHERE guestbookid = ?", [$key]);
}
$func->confirmation('Erfolgreich gelöscht', 'index.php?mod=guestbook');
