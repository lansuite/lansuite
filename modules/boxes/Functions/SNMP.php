<?php

/**
 * Used as a callback function
 *
 * @return bool
 */
function snmp()
{
    if (extension_loaded('snmp')) {
        return true;
    }

    return false;
}
