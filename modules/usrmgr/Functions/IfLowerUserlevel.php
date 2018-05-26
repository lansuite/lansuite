<?php

/**
 * @param int $userid
 * @return bool
 */
function IfLowerUserlevel($userid)
{
    global $line, $auth;

    if ($line['type'] < $auth['type']) {
        return true;
    } else {
        return false;
    }
}
