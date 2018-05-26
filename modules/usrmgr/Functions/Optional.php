<?php

/**
 * @param string $key
 * @return int
 */
function Optional($key)
{
    global $cfg;

    if ($cfg["signon_show_".$key] <= 1) {
        return 1;
    } else {
        return 0;
    }
}
