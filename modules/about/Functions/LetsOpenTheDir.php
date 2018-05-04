<?php

/**
 * @param string $dir
 * @return void
 */
function lets_open_the_dir($dir)
{
    global $files;

    $thedir = opendir($dir);
    while (false !== ($content = readdir($thedir))) {
        if ($content != "." and $content != "..") {
            if (is_dir($dir."/".$content)) {
                lets_open_the_dir($dir."/".$content);
            }
            if (file_exists($dir."/".$content)) {
                $files[] = $dir."/".$content;
            }
        }
    }
}