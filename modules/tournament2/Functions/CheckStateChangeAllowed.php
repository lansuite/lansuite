<?php

/**
 * @param string $state
 * @return bool|string
 */
function CheckStateChangeAllowed($state)
{
    if ($state == 'process') {
        return t('Dieser Status kann nicht manuell gesetzt werden. Zum setzen, bitte "Generieren" verwenden');
    }
    if ($state == 'closed') {
        return t('Dieser Status kann nicht manuell gesetzt werden. Er wird automatisch gesetzt, sobald das letzte Ergebnis im Turnier eingetragen wurde');
    }
    return false;
}
