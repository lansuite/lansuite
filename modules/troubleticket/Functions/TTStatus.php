<?php

/**
 * @param int $status
 * @return string
 */
function TTStatus($status)
{
    return match ($status) {
        1 => t('Neu / Ungeprüft'),
        2 => t('Überprüft / Akzeptiert'),
        3 => t('In Arbeit'),
        4 => t('Abgeschlossen'),
        5 => t('Abgelehnt'),
        default => t('Überprüft am/um'),
    };
}
