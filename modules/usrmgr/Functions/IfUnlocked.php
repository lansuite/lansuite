<?php

/**
 * @param int $userid
 * @return bool
 */
function IfUnlocked($userid)
{
    global $line;

    if (!$line['locked']) {
        return true;
    } else {
        return false;
    }
}
