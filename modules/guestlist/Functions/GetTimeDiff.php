<?php

/**
 * @param int $last
 * @return false|string
 */
function getTimeDiff($last)
{
    return date("i:s", time()-$last);
}
