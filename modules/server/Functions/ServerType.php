<?php

/**
 * @param string $type
 * @return string
 */
function ServerType($type)
{
    return match ($type) {
        "gameserver" => "Game",
        "ftp" => "FTP",
        "irc" => "IRC",
        "voice" => "Voice",
        "web" => "Web",
        "proxy" => "Proxy",
        "misc" => "Misc",
        default => "???",
    };
}
