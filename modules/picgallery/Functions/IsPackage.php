<?php

/**
 * @param string $ext
 * @return bool
 */
function IsPackage($ext)
{
    $ext = strtolower($ext);
    if (($ext == "zip")
        || ($ext == "tar")
        || ($ext == "rar")
        || ($ext == "ace")
        || ($ext == "gz")
        || ($ext == "bz")
    ) {
        return true;
    }

    return false;
}
