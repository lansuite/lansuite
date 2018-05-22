<?php

/**
 * @param string $dir
 * @return void
 */
function recursiveRemoveDirectory($dir)
{
    $dir = str_replace('\\', '/', $dir);
    $dir = str_replace('/..', '', $dir);

    if (is_dir($dir)) {
        $ResDir = opendir($dir);
        while ($file = readdir($ResDir)) {
            if ($file != '.' and $file != '..') {
                if (is_dir("$dir/$file")) {
                    recursiveRemoveDirectory("$dir/$file");
                } elseif (file_exists("$dir/$file")) {
                    unlink("$dir/$file");
                }
            }
        }
        closedir($ResDir);
    }
    rmdir($dir);
}
