<?php

/**
 * @return bool
 */
function IsAuthorizedAdmin()
{
    global $auth, $user_data, $link;

    $link = '';
    if ($auth['type'] >= 2 and $auth['type'] >= $user_data['type']) {
        return true;
    } else {
        return false;
    }
}
