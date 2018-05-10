<?php

/**
 * Used as a callback function
 *
 * @return bool
 */
function ShowSignon()
{
    global $cfg, $auth;

    if ($cfg['signon_partyid'] or !$auth['login']) {
        return true;
    }

    return false;
}
