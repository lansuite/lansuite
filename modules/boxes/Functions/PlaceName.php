<?php

/**
 * Used as a callback function
 *
 * @param int $place
 * @return string
 */
function PlaceName($place)
{
    if ($place == 0) {
        return t('Linke Seite');
    } elseif ($place == 1) {
        return t('Rechte Seite');
    }

    return '';
}
