<?php

/**
 * @param int $tid
 * @return bool
 */
function Active($tid)
{
    global $line, $party;

    if ($line['party_id'] == $party->party_id) {
        return true;
    } else {
        return false;
    }
}
