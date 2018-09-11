<?php

/**
 * @return int
 */
function CountAdmins()
{
    global $db;

    $query_admins = $db->qry("SELECT * FROM %prefix%user WHERE clanid = %int% AND clanadmin = 1", $_GET['clanid']);
    return $db->num_rows($query_admins);
}
