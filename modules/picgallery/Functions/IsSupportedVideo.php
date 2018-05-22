<?php

/**
 * @param string $ext
 * @return bool
 */
function IsSupportedVideo($ext)
{
    if (($ext == "mp4") || ($ext == "mpg") || ($ext == "mpeg") || ($ext == "ogv")) {
        return true;
    }

    return false;
}
