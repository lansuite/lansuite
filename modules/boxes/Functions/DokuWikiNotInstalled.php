<?php

/**
 * Used as a callback function
 *
 * @return bool
 */
function DokuWikiNotInstalled()
{
    if (!file_exists('ext_scripts/dokuwiki/conf/local.php')) {
        return true;
    }

    return false;
}
