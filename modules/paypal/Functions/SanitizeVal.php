<?php

/**
 * @param string $string
 * @return float
 */
function SanitizeVal($string)
{
    $string = str_replace(',', '.', $string);
    return doubleval($string);
}
