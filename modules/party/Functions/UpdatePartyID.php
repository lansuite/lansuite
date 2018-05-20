<?php

/**
 * @param int $id
 * @return void
 */
function UpdatePartyID($id)
{
    global $db, $func, $cfg;

    if (!$cfg['signon_partyid']) {
        $db->qry("UPDATE %prefix%config SET cfg_value = %int% WHERE cfg_key = 'signon_partyid'", $id);
    }
    $_SESSION['party_id'] = $id;
    $func->confirmation(t('Die Daten wurden erfolgreich geändert.'), 'index.php?mod=party');
}
