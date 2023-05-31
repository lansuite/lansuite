<?php

/**
 * @param string $key
 * @return bool
 */
function Needed($key)
{
    global $cfg;

    $configKey = 'signon_show_' . $key;
    if (array_key_exists($configKey, $cfg) && $cfg[$configKey] == 2) {
        return true;
    } else {
        return false;
    }
}
