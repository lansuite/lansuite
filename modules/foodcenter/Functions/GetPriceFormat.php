<?php

/**
 * Used as callback function in mastersearch
 *
 * @param float $price
 * @return string
 */
function GetPriceFormat($price)
{
    return number_format($price, 2, ",", ".") . " EUR";
}
