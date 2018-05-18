<?php

/**
 * @param int $userid
 * @return string
 */
function SeatNameLink($userid)
{
    global $seat2;

    return $seat2->SeatNameLink($userid);
}
