<?php

/**
 * Used as callback function in mastersearch
 *
 * @param int $userid
 * @return string
 */
function SeatNameLinkFoodcenter($userid)
{
    global $seat2;

    return $seat2->SeatNameLink($userid);
}
