<?php

/**
 * @param int $id
 * @return void
 */
function UpdateClanMgr($id)
{
    global $auth, $database, $func;

    if (!$_GET['clanid']) {
        $func->log_event(t('Clan %1 erstellt', $_POST['name']), 1, t('clanmgr'));
        if ($database->query("UPDATE %prefix%user SET clanid = ?, clanadmin = 1 WHERE userid = ?", [$id, $auth["userid"]])) {
            $func->confirmation(t('Der Clan wurde erfolgreich angelegt. Als Ersteller hast du die Rolle Admin in diesem Clan.'), "index.php?mod=clanmgr");
        }
    }
}
