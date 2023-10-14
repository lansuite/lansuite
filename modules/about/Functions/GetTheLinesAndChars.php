<?php

/**
 * @param string $file
 * @return array
 */
function GetTheLinesAndChars($file)
{
    $data = [];
    $file_content = file($file);
    $data[0] = is_countable($file_content) ? count($file_content) : 0;
    foreach ($file_content as $iValue) {
        $data[1] += strlen($iValue);
    }

    return $data;
}
