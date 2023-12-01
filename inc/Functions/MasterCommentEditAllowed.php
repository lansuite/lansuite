<?php

/**
 * @return bool
 */
function MasterCommentEditAllowed()
{
    global $line, $auth;

    if ($line['creatorid'] == $auth['userid'] || $auth['type'] >= \LS_AUTH_TYPE_ADMIN) {
        return true;
    }

    return false;
}
