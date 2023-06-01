<?php

/**
 * @param int $last
 */
function getTimeDiff($last): false|string
{
    return date("i:s", time()-$last);
}
