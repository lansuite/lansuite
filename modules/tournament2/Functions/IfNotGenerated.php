<?php

/**
 * @param int $tid
 * @return bool
 */
function IfNotGenerated($tid)
{
    global $line;

    if ($line['status'] == 'open' or $line['status'] == 'locked' or $line['status'] == 'invisible') {
        return true;
    } else {
        return false;
    }
}
