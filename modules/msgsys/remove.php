<?php

if ($_GET['queryid']) {
    $stepParameter = $_GET['step'] ?? 0;
    switch ($stepParameter ) {
        default:
            $rowcheck = $db->qry("
              SELECT id
              FROM %prefix%buddys
              WHERE userid = %int%
              AND buddyid = %int%", $auth['userid'], $_GET['queryid']);

            // User in buddylist ?
            if ($db->num_rows() != '0') {
                $row = $database->queryWithOnlyFirstRow("
                  SELECT username, name, firstname
                  FROM %prefix%user
                  WHERE userid = ?", [$_GET['queryid']]);

                if ($cfg['sys_internet'] == 0) {
                    $func->question(t('Willst du den Benutzer <b>%1 (%2 %3)</b> wirklich aus deiner Buddy-Liste entfernen?', $row['name'], $row['firstname'], $row['username']), 'index.php?mod=msgsys&action=removebuddy&queryid=' . $_GET['queryid'] . '&step=2', "index.php");
                } else {
                    $func->question(t('Willst du den Benutzer <b>%1</b> wirklich aus deiner Buddy-Liste entfernen?', $row['username']), 'index.php?mod=msgsys&action=removebuddy&queryid=' . $_GET['queryid'] . '&step=2', "index.php");
                }
            } else {
                $func->error(t('Dieser Benutzer befindet sich nicht in deiner Buddy-Liste'));
            }
            break;

        // Case remove
        case 2:
            $row1 = $database->queryWithOnlyFirstRow("
               SELECT username, name, firstname
               FROM %prefix%user
               WHERE userid = ?", [$_GET['queryid']]);

            $row2 = $database->query("
               DELETE FROM %prefix%buddys
               WHERE buddyid = ?
               AND userid = ?", [$_GET['queryid'], $auth['userid']]);

            // Confirmation
            if ($row2 == true) {
                if ($cfg['sys_internet'] == 1) {
                    $func->confirmation(t('Der Benutzer <b>%1</b> wurde aus deiner Buddy-Liste entfernt. Die &Auml;nderung wird beim n&auml;chsten Seitenaufruf wirksam.', $row1['username']), "");
                } else {
                    $func->confirmation(t('Der Benutzer <b>%1 (%2 %3)</b> wurde aus deiner Buddy-Liste entfernt. Die &Auml;nderung wird beim n&auml;chsten Seitenaufruf wirksam.', $row1['name'], $row1['firstname'], $row1['username']), "");
                }
            }
            break;
    }
} else {
    $func->error(t('Du hast keinen Benutzer ausgew&auml;hlt'));
}
