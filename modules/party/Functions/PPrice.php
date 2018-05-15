<?php

/**
 * @param string $price_text
 * @return string
 */
function PPrice($price_text)
{
    global $line, $cfg;

    if ($line['price']) {
        return $price_text .'<br /> ('. $line['price'] .' '. $cfg['sys_currency'] .')';
    } else {
        return $price_text;
    }
}
