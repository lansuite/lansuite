<?php
/**
 * Checks (at the moment only) if the selected username is already existing in the database
 *
 * @param string $username The username to be checked
 * @return bool|mixed|string
 */
function CheckValidUsername($username)
{
    global $cfg, $db, $database;

    if ($cfg['signon_username_unique']) {
        if (isset($_GET['userid'])) {
            $row = $database->queryWithOnlyFirstRow('SELECT * FROM %prefix%user WHERE username = ? AND userid != ?', [$username, $_GET['userid']]);
        } else {
            $row = $database->queryWithOnlyFirstRow('SELECT * FROM %prefix%user WHERE username = ?', [$username]);
        }
        if ($row) {
            return t('Der gewählte Username existiert bereits');
        }
    }
    return false;
}
