<?php

/**
 * @param string $file
 * @return array
 */
function GetTheLinesAndChars($file)
{
    $file_content = file($file);
    $data[0] = count($file_content);
    foreach ($file_content as $iValue) {
        $data[1] += strlen($iValue);
    }

    return $data;
}
