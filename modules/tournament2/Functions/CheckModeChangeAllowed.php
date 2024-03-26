<?php

/**
 * @param string $mode
 */
function CheckModeChangeAllowed($mode): bool|string
{
    global $mf, $database;

    $t = $database->queryWithOnlyFirstRow('SELECT mode, status FROM %prefix%tournament_tournaments WHERE tournamentid = ?', [$_GET['tournamentid']]);
    if ($mf->isChange and $t['status'] != 'open' and $t['mode'] != $mode) {
        if ($t['mode'] == 'single' or $t['mode'] == 'double') {
            if ($mode != 'single' and $mode != 'double') {
                return t('Bei bereits generierten Turnieren darf der Modus nur noch zwischen Single-Elimintation und Double-Elimination geändert werden');
            }
        } else {
            return t('Bei bereits generierten Turnieren ist das ändern des Modus nur noch bei Single-Elimintation und Double-Elimination erlaubt');
        }
    }
    return false;
}
