<?php

/**
 * @param int $tid
 * @return bool
 */
function IfFinished($tid)
{
    global $line;

    if ($line['status'] == 'closed') {
        return true;
    } else {
        return false;
    }
}
