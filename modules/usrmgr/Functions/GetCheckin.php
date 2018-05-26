<?php

/**
 * @param boolean $checkin
 * @return string
 */
function getCheckin($checkin)
{
    if ($checkin) {
        return "<img src='design/images/icon_yes.png' border='0' alt='Ja' />";
    } else {
        return "<img src='design/images/icon_no.png' border='0' alt='Ja' />";
    }
}
