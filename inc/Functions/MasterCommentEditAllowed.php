<?php

/**
 * @return bool
 */
function MasterCommentEditAllowed()
{
    global $line, $auth;

    if ($line['creatorid'] == $auth['userid'] || $auth['type'] >= 2) {
        return true;
    }

    return false;
}
