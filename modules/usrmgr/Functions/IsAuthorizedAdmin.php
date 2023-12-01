<?php

/**
 * @return bool
 */
function IsAuthorizedAdmin()
{
    global $auth, $user_data, $link;

    $link = '';
    if ($auth['type'] >= \LS_AUTH_TYPE_ADMIN and $auth['type'] >= $user_data['type']) {
        return true;
    } else {
        return false;
    }
}
