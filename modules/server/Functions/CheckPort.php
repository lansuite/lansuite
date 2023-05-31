<?php

/**
 * @param int $port
 */
function CheckPort($port): bool|string
{
    if ($port < 1 or $port > 65535) {
        return t('Der Port muss zwischen 1 und 65535 liegen');
    }
    return false;
}
