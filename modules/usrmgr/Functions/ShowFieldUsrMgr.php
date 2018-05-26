<?php

/**
 * @param string $key
 * @return int
 */
function ShowFieldUsrMgr($key)
{
    global $cfg;

    if ($cfg["signon_show_".$key] > 0) {
        return 1;
    } else {
        return 0;
    }
}
