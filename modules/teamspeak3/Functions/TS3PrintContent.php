<?php

/**
 * Outputs either the cached content if it is quite current or
 * fetches a new page via the TS3 framework and stores it in the cache file.
 *
 * @return void
 */
function TS3PrintContent()
{
    // File where the output is buffered
    $TS3_CACHE_FILE = 'ext_scripts/teamspeak3/ts3cache.html';
    // How long should we use that file?
    $TS3_CACHE_TIME = 180;
    // Check the cache....
    if (TS3CacheIsFresh($TS3_CACHE_FILE, $TS3_CACHE_TIME)) {
        readfile($TS3_CACHE_FILE);
    } else {
        echo TS3GenerateCacheFile($TS3_CACHE_FILE);
    }
}
