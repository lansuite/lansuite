<?php

/**
 * @param int $id
 * @return bool
 */
function UpdatePoll($id)
{
    global $db, $database;

    $pollReset = $_POST['poll_reset'] ?? false;
    if ($pollReset || !$_GET['pollid']) {
        $res = $db->qry('SELECT polloptionid FROM %prefix%polloptions WHERE pollid = %int%', $id);
        while ($row = $db->fetch_array($res)) {
            $database->query('DELETE FROM %prefix%pollvotes WHERE polloptionid = ?', [$row['polloptionid']]);
        }
        $db->free_result($res);
        $database->query('DELETE FROM %prefix%polloptions WHERE pollid = ?', [$id]);
        if ($_POST['poll_option']) {
            foreach ($_POST['poll_option'] as $key => $val) {
                if (trim($val) != '') {
                    $database->query('INSERT INTO %prefix%polloptions SET caption = ?, pollid = ?', [$val, $id]);
                }
            }
        }
    }

    return true;
}
