<?php

/**
 * @param int $userid
 * @return bool
 */
function IfLocked($userid)
{
    global $line;

    if ($line['locked']) {
        return true;
    } else {
        return false;
    }
}
