<?php

/**
 * Used as a callback function
 *
 * @return bool
 */
function sys_internet()
{
    global $cfg;

    if ($cfg['sys_internet']) {
        return true;
    }

    return false;
}
