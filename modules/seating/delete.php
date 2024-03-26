<?php

$stepParameter = $_GET['step'] ?? 0;
switch ($stepParameter) {
    default:
        include_once('modules/seating/search.inc.php');
        break;

    case 2:
        $func->question(
            t('Willst du diesen Sitzblock wirklich löschen?'),
            "index.php?mod=seating&action=delete&step=3&blockid={$_GET['blockid']}",
            'index.php?mod=seating&action=delete'
        );
        break;

    case 3:
        $database->query("DELETE FROM %prefix%seat_block WHERE blockid = ?", [$_GET['blockid']]);
        $database->query("DELETE FROM %prefix%seat_sep WHERE blockid = ?", [$_GET['blockid']]);
        $database->query("DELETE FROM %prefix%seat_seats WHERE blockid = ?", [$_GET['blockid']]);

        $func->confirmation(t('Der Sitzblock wurde erfolgreich gelöscht'), 'index.php?mod=seating&action=delete');
        break;
}
