<?php

/**
 * Checks if a cache file is existing and if it is current
 *
 * @param string $file
 * @param int $time
 * @return bool Indicates if the cache file is recent or too old. True if recent false if old or nonexistent
 */
function TS3CacheIsFresh($file, $time)
{
    if (file_exists($file)) {
        if (filemtime($file) + $time < time()) {
            // File too old
            return false;
        } else {
            // File is current
            return true;
        };
    } else {
        // File is not existing...yet
        return false;
    };
}
