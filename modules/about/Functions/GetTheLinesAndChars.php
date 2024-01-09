<?php

/**
 * @param string $file
 *
 * @return array<int, int> array containing two elements: line-count in element 0, character count in element 1
 */
function GetTheLinesAndChars($file)
{
    $data = [];
    $fileContent = file($file);
    $data[0] = is_countable($fileContent) ? count($fileContent) : 0;
    foreach ($fileContent as $iValue) {
        $data[1] += strlen($iValue);
    }

    return $data;
}
