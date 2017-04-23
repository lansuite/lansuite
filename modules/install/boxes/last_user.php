<?php
/**
 * Generate Generate Box to show last User
 *
 * @package lansuite_core
 * @author knox
 * @version $Id: last_user.php 1535 2008-07-27 22:36:01Z bytekilla $
 */
 
$box->DotRow(t('Zuletzt angemeldet').':');

$qry = $db->qry('SELECT userid, username FROM %prefix%user WHERE type > 0 ORDER BY userid DESC LIMIT 0,5');
while ($row = $db->fetch_array($qry)) {
    if (strlen($row["username"]) > 12) {
        $row["username"] = substr($row["username"], 0, 10) . "...";
    }
    $box->EngangedRow($dsp->FetchUserIcon($row["userid"], $row["username"]));
}
$db->free_result($qry);
