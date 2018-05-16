<?php

/**
 * @return bool
 */
function EditAllowed()
{
    global $line, $auth;

    if ($line['userid'] == $auth['userid'] or $auth['type'] >= 2) {
        return true;
    } else {
        return false;
    }
}
