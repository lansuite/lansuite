<?php

/**
 * @return int
 */
function MsgInIntMode()
{
    global $cfg;

    if (!$cfg['sys_internet'] || $cfg['msgsys_alwayson']) {
        return 1;
    }

    return 0;
}
