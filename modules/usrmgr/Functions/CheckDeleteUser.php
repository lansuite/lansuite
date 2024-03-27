<?php

/**
 * Checks which MasterDetele doesn't perform.
 *
 * @param int $userid User ID
 * @return bool
 */
function CheckDeleteUser($userid)
{
    global $db, $database, $auth, $func;

    $get_data = $database->queryWithOnlyFirstRow("SELECT username, type FROM %prefix%user WHERE userid = ?", [$userid]);
    $get_party_data = $database->queryWithOnlyFirstRow('
      SELECT
        1 AS found
      FROM %prefix%party_user AS pu
      LEFT JOIN %prefix%partys AS p ON p.party_id = pu.party_id
      WHERE
        pu.user_id = ?
        AND UNIX_TIMESTAMP(p.enddate) > UNIX_TIMESTAMP(NOW())', [$userid]);

    if ($auth['type'] == \LS_AUTH_TYPE_ADMIN and $get_data["type"] >= 2) {
        $func->error(t('Du hast nicht die erforderlichen Rechte, um einen Admin zu löschen'), "index.php?mod=usrmgr");
    } elseif ($get_party_data["found"]) {
        $func->error(t('Dieser Benutzer ist noch zu einer Party angemeldet. Melden sie ihn zuerst ab'), "index.php?mod=usrmgr");
    } elseif ($get_data["type"] < 0) {
        $func->error(t('Dieser Benutzer wurde bereits gelöscht'), "index.php?mod=usrmgr");
    } elseif ($auth["userid"] == $userid) {
        $func->error(t('Du kannst dich nicht selbst löschen'), "index.php?mod=usrmgr");
    } else {
        $database->query("UPDATE %prefix%seat_seats SET status = '1', userid = '0' WHERE userid = ?", [$userid]);
        return true;
    }

    return false;
}
