<?php

/**
 * @param int $id
 * @return void
 */
function UpdateClanMgr($id)
{
    global $auth, $db, $func;

    if (!$_GET['clanid']) {
        $func->log_event(t('Clan %1 erstellt', $_POST['name']), 1, t('clanmgr'));
        if ($db->qry("UPDATE %prefix%user SET clanid = %int%, clanadmin = 1 WHERE userid =%int%", $id, $auth["userid"])) {
            $func->confirmation(t('Der Clan wurde erfolgreich angelegt. Als Ersteller hast du die Rolle Admin in diesem Clan.'), "index.php?mod=clanmgr");
        }
    }
}
