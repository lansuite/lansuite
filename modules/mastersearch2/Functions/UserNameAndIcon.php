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

    $userId = $line['userid'] ?? 0;
    if ($username == '') {
        return '<i>System</i>';

    } elseif ($userId) {
        return $dsp->FetchUserIcon($userId, $username);

    } else {
        return $username;
    }
}
