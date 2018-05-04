<?php

/**
 * @param string $dir
 * @return void
 */
function LetsOpenTheDir($dir)
{
    global $files;

    $thedir = opendir($dir);
    while (false !== ($content = readdir($thedir))) {
        if ($content != "." and $content != "..") {
            if (is_dir($dir."/".$content)) {
                LetsOpenTheDir($dir."/".$content);
            }
            if (file_exists($dir."/".$content)) {
                $files[] = $dir."/".$content;
            }
        }
    }
}
