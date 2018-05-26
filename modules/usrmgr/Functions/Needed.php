<?php

/**
 * @param string $key
 * @return bool
 */
function Needed($key)
{
    global $cfg;

    if ($cfg['signon_show_'. $key] == 2) {
        return true;
    } else {
        return false;
    }
}
