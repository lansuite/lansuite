<?php

/**
 * @param int $port
 * @return bool|string
 */
function CheckPort($port)
{
    if ($port < 1 or $port > 65535) {
        return t('Der Port muss zwischen 1 und 65535 liegen');
    }
    return false;
}
