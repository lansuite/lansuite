<?php

/**
 * @param string $type
 * @return string
 */
function ServerType($type)
{
    switch ($type) {
        default:
            return "???";
            break;
        case "gameserver":
            return "Game";
            break;
        case "ftp":
            return "FTP";
            break;
        case "irc":
            return "IRC";
            break;
        case "web":
            return "Web";
            break;
        case "proxy":
            return "Proxy";
            break;
        case "misc":
            return "Misc";
            break;
    }
}
