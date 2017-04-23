<?php

    $dir = ".";

function lets_open_the_dir($dir)
{
    global $files;
        
    // Open the design-dir
    $thedir = opendir($dir);
        
    // Reading all subdirs from design and fill them into an array
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
function get_the_lines_and_chars($file)
{
    $file_content = file($file);
    $data[0] = count($file_content);
    for ($i=0; $i < count($file_content); $i++) {
        $data[1] += strlen($file_content[$i]);
    }
    return $data;
}
    
    lets_open_the_dir($dir);
    
foreach ($files as $file) {
    if (eregi(".php", $file)) {
        $data = get_the_lines_and_chars($file);
        $php_lines += $data[0];
        $php_chars += $data[1];
    }
    if (eregi(".htm", $file)) {
        $data = get_the_lines_and_chars($file);
        $html_lines += $data[0];
        $html_chars += $data[1];
    }
}
    
    $filecontents = file("modules/about/credits.php");
    $fo = fopen("modules/about/credits.php", "w");
foreach ($filecontents as $filecontent) {
    $filecontent = eregi_replace("<!--PHP-LINES-START-->(.*)<!--PHP-LINES-STOP-->", "<!--PHP-LINES-START-->$php_lines<!--PHP-LINES-STOP-->", $filecontent);
    $filecontent = eregi_replace("<!--PHP-CHARS-START-->(.*)<!--PHP-CHARS-STOP-->", "<!--PHP-CHARS-START-->$php_chars<!--PHP-CHARS-STOP-->", $filecontent);
    $filecontent = eregi_replace("<!--HTML-LINES-START-->(.*)<!--HTML-LINES-STOP-->", "<!--HTML-LINES-START-->$html_lines<!--HTML-LINES-STOP-->", $filecontent);
    $filecontent = eregi_replace("<!--HTML-CHARS-START-->(.*)<!--HTML-CHARS-STOP-->", "<!--HTML-CHARS-START-->$html_chars<!--HTML-CHARS-STOP-->", $filecontent);
    fwrite($fo, $filecontent);
}
    fclose($fo);
