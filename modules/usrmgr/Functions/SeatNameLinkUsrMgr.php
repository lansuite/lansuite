<?php

/**
 * @param int $userid
 * @return string
 */
function SeatNameLinkUsrMgr($userid)
{
    global $seat2;

    return $seat2->SeatNameLink($userid);
}
