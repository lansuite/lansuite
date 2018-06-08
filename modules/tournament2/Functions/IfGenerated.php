<?php

/**
 * @param int $tid
 * @return bool
 */
function IfGenerated($tid)
{
    global $line;

    if ($line['status'] == 'process' or $line['status'] == 'closed') {
        return true;
    } else {
        return false;
    }
}
