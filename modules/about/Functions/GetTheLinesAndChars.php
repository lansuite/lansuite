<?php

/**
 * @param string $file
 * @return array
 */
function GetTheLinesAndChars($file)
{
    $file_content = file($file);
    $data[0] = count($file_content);
    for ($i=0; $i < count($file_content); $i++) {
        $data[1] += strlen($file_content[$i]);
    }

    return $data;
}
