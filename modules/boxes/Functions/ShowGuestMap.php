<?php

/**
 * Used as a callback function
 *
 * @return bool
 */
function ShowGuestMap()
{
    global $cfg;

    if ($cfg['guestlist_guestmap']) {
        return true;
    }

    return false;
}
