<?php

/**
 * @param int $userid
 * @return bool
 */
function IfLowerOrEqualUserlevel($userid)
{
    global $line, $auth;

    if ($line['type'] <= $auth['type']) {
        return true;
    } else {
        return false;
    }
}
