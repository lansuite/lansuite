<?php

/**
 * @param int $id
 * @return bool
 */
function UpdatePoll($id)
{
    global $db;

    if ($_POST['poll_reset'] or !$_GET['pollid']) {
        $res = $db->qry('SELECT polloptionid FROM %prefix%polloptions WHERE pollid = %int%', $id);
        while ($row = $db->fetch_array($res)) {
            $db->qry('DELETE FROM %prefix%pollvotes WHERE polloptionid = %int%', $row['polloptionid']);
        }
        $db->free_result($res);
        $db->qry('DELETE FROM %prefix%polloptions WHERE pollid = %int%', $id);
        if ($_POST['poll_option']) {
            foreach ($_POST['poll_option'] as $key => $val) {
                if (trim($val) != '') {
                    $db->qry('INSERT INTO %prefix%polloptions SET caption = %string%, pollid = %int%', $val, $id);
                }
            }
        }
    }

    return true;
}
