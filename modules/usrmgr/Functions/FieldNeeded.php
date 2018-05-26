<?php

/**
 * @param string $key
 * @return int
 */
function FieldNeeded($key)
{
    global $cfg;

    if ($cfg["signon_show_".$key] == 2) {
        return 1;
    } else {
        return 0;
    }
}
