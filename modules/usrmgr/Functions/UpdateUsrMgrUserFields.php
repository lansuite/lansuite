<?php

/**
 * @param int $id
 * @return bool
 */
function UpdateUsrMgrUserFields($id)
{
    global $db;

    $db->qry("ALTER TABLE %prefix%user ADD %plain% VARCHAR(255) NOT NULL;", $_POST['name']);

    return true;
}
