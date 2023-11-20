<?php

/**
 * Function to recursively enumerate all files in all subdirectories into global variable $files.
 *
 * @param string $dir directory to enumerate files in
 *
 * @return void Returns files found in current + subdirectories (currently) via global variable $files
 *
 * @global string[] $files global variable to contain all files enumerated
 */
function LetsOpenTheDir($dir)
{
    global $files;

    $thedir = opendir($dir);
    while (false !== ($content = readdir($thedir))) {
        if ($content != '.' and $content != '..') {
            if (is_dir($dir.'/'.$content)) {
                LetsOpenTheDir($dir.'/'.$content);
            }
            if (file_exists($dir.'/'.$content)) {
                $files[] = $dir.'/'.$content;
            }
        }
    }
}
