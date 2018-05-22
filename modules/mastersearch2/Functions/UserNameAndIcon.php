<?php

/**
 * Used as a callback function for MasterSearch2 class.
 *
 * @param string $username
 * @return string
 */
function UserNameAndIcon($username)
{
    global $line, $dsp;

    if ($username == '') {
        return '<i>System</i>';
    } elseif ($line['userid']) {
        return $dsp->FetchUserIcon($line['userid'], $username);
    } else {
        return $username;
    }
}
