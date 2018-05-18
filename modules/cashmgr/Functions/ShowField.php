<?php

/**
 * @param string $key
 * @return boolean
 */
function ShowField($key)
{
    global $cfg;

    if ($cfg["signon_show_" . $key] > 0) {
        return true;
    }

    return false;
}
