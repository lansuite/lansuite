<?php

/**
 * @param int $status
 * @return string
 */
function TTStatus($status)
{
    switch ($status) {
        default:
            return t('Überprüft am/um');
            break;
        case 1:
            return t('Neu / Ungeprüft');
            break;
        case 2:
            return t('Überprüft / Akzeptiert');
            break;
        case 3:
            return t('In Arbeit');
            break;
        case 4:
            return t('Abgeschlossen');
            break;
        case 5:
            return t('Abgelehnt');
            break;
    }
}
