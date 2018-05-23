<?php

/**
 * @return int
 */
function PartyAvailible()
{
    global $party;

    if ($party->getRegistrationCount() > 0) {
        return 1;
    }

    return 0;
}
