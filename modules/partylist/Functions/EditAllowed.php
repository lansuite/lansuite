<?php

/**
 * @return bool
 */
function EditAllowed()
{
    global $line, $auth;

    if ($line['userid'] == $auth['userid'] or $auth['type'] >= \LS_AUTH_TYPE_ADMIN) {
        return true;
    } else {
        return false;
    }
}
