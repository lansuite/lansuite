<?php

/**
 * @param resource $socket
 * @param string $response
 * @param int $line
 * @return bool
 */
function ServerParse($socket, $response, $line = __LINE__)
{
    while (substr($server_response, 3, 1) != ' ') {
        if (!($server_response = fgets($socket, 256))) {
            echo("Couldn't get mail server response codes ". HTML_NEWLINE);
        }
    }

    if (!(substr($server_response, 0, 3) == $response)) {
        echo "Ran into problems sending Mail. Response: $server_response " . HTML_NEWLINE;
        return false;
    } else {
        return true;
    }
}
