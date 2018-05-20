<?php

/**
 * Returns if the supplied extension is supported, or not.
 *
 * @param string $ext
 * @return bool
 */
function IsSupportedType($ext)
{
    $ext = strtolower($ext);
    if ((($ext == "jpeg" || $ext == "jpg") && (ImageTypes() & IMG_JPG))
        || ($ext == "png" && (ImageTypes() & IMG_PNG))
        || ($ext == "gif" && (ImageTypes() & IMG_GIF))
        || ($ext == "wbmp" && (ImageTypes() & IMG_WBMP))
        || ($ext == "bmp")
        || (IsSupportedVideo($ext))
    ) {
        return true;
    }

    return false;
}
