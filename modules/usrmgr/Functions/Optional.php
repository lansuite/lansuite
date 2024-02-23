<?php

/**
 * @param string $key
 * @return int
 */
function Optional($key)
{
    global $cfg;

    $configKey = 'signon_show_' . $key;
    if (array_key_exists($configKey, $cfg) && $cfg[$configKey] <= 1) {
        return 1;
    }

    return 0;
}
