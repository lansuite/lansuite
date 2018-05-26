<?php

/**
 * @param int $type
 * @return string
 */
function GetTypeDescription($type)
{
    switch ($type) {
        case -2:
            return t('Organisator (gesperrt)');
            break;
        case -1:
            return t('Gast (gesperrt)');
            break;
        default:
            return t('Gast (deaktiviert)');
            break;
        case 1:
            return t('Gast');
            break;
        case 2:
            return t('Organisator');
            break;
        case 3:
            return t('Superadmin');
            break;
    }
}
