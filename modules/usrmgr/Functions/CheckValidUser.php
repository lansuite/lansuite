<?php
/**
 * Checks (at the moment only) if the selected username is already existing in the database
 *
 * @param string $username The username to be checked
 * @return bool|mixed|string
 */
function CheckValidUser($username){
    global $db;
    $row = $db->qry_first('SELECT * FROM %prefix%users WHERE username=%string%', $username);
    if ($row){
        return t('Der gewählte Username existiert bereits');
    }
    return false;
}