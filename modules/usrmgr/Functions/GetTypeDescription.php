<?php

/**
 * @param int $type
 * @return string
 */
function GetTypeDescription($type)
{
    return match ($type) {
        -2 => t('Organisator (gesperrt)'),
        -1 => t('Gast (gesperrt)'),
        1 => t('Gast'),
        2 => t('Organisator'),
        3 => t('Superadmin'),
        default => t('Gast (deaktiviert)'),
    };
}
