<?php

$dir = ".";

LetsOpenTheDir($dir);
    
foreach ($files as $file) {
    if (preg_match(".php", $file)) {
        $data = GetTheLinesAndChars($file);
        $php_lines += $data[0];
        $php_chars += $data[1];
    }
    if (preg_match(".htm", $file)) {
        $data = GetTheLinesAndChars($file);
        $html_lines += $data[0];
        $html_chars += $data[1];
    }
}

$filecontents = file("modules/about/credits.php");
$fo = fopen("modules/about/credits.php", "w");
foreach ($filecontents as $filecontent) {
    $filecontent = preg_replace("<!--PHP-LINES-START-->(.*)<!--PHP-LINES-STOP-->", "<!--PHP-LINES-START-->$php_lines<!--PHP-LINES-STOP-->", $filecontent);
    $filecontent = preg_replace("<!--PHP-CHARS-START-->(.*)<!--PHP-CHARS-STOP-->", "<!--PHP-CHARS-START-->$php_chars<!--PHP-CHARS-STOP-->", $filecontent);
    $filecontent = preg_replace("<!--HTML-LINES-START-->(.*)<!--HTML-LINES-STOP-->", "<!--HTML-LINES-START-->$html_lines<!--HTML-LINES-STOP-->", $filecontent);
    $filecontent = preg_replace("<!--HTML-CHARS-START-->(.*)<!--HTML-CHARS-STOP-->", "<!--HTML-CHARS-START-->$html_chars<!--HTML-CHARS-STOP-->", $filecontent);
    fwrite($fo, $filecontent);
}
fclose($fo);
