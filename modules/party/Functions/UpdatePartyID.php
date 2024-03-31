<?php

/**
 * @param int $id
 * @return void
 */
function UpdatePartyID($id)
{
    global $database, $func, $cfg;

    if (!$cfg['signon_partyid']) {
        $database->query("UPDATE %prefix%config SET cfg_value = ? WHERE cfg_key = 'signon_partyid'", [$id]);
    }
    $_SESSION['party_id'] = $id;
    $func->confirmation(t('Die Daten wurden erfolgreich geändert.'), 'index.php?mod=party');
}
