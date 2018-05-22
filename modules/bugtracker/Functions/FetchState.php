<?php

/**
 * @param int $state
 * @return mixed
 */
function FetchState($state)
{
    global $bugtracker;

    return $bugtracker->stati[$state];
}
