<?php

/**
 * @param int $endtime
 * @return string
 */
function GetPollStatus($endtime)
{
    if ($endtime == 0 or $endtime > time()) {
        return "offen";
    } else {
        return "geschlossen";
    }
}
