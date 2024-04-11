<?php

$stepParameter = $_GET['step'] ?? 0;
switch ($stepParameter) {
    default:
        include_once('modules/sponsor/search.inc.php');
        break;

    case 2:
        $sponsor = $database->queryWithOnlyFirstRow('SELECT name FROM %prefix%sponsor WHERE sponsorid = ?', [$_GET['sponsorid']]);
        $func->question(t('Wilsst du den Sponsor <b>%1</b> wirklich löschen?', $sponsor['name']), "index.php?mod=sponsor&amp;action=delete&amp;step=3&amp;sponsorid=". $_GET['sponsorid'], "index.php?mod=sponsor&amp;action=delete");
        break;

    case 3:
        $sponsor = $database->queryWithOnlyFirstRow('SELECT name FROM %prefix%sponsor WHERE sponsorid = ?', [$_GET['sponsorid']]);
        $database->query('DELETE FROM %prefix%sponsor WHERE sponsorid = ?', [$_GET['sponsorid']]);
        $func->confirmation(t('Der Sponsor <b>%1</b> wurde erfolgreich gelöscht', $sponsor["name"]), "index.php?mod=sponsor&amp;action=delete");
        break;
}
