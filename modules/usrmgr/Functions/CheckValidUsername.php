<?php
/**
 * Checks (at the moment only) if the selected username is already existing in the database
 *
 * @param string $username The username to be checked
 * @return bool|mixed|string
 */
function CheckValidUsername($username)
{
    global $cfg, $db;
    if ($cfg['signon_username_unique']) {
        if (isset($_GET['userid'])) {
            $row = $db->qry_first('SELECT * FROM %prefix%user WHERE username=%string% AND userid!=%int%', $username, $_GET['userid']);
        } else {
            $row = $db->qry_first('SELECT * FROM %prefix%user WHERE username=%string%', $username);
        }
        if ($row) {
            return t('Der gewählte Username existiert bereits');
        }
    }
    return false;
}
