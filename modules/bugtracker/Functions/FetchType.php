<?php

/**
 * @param string $type
 * @return string
 */
function FetchType($type)
{
    global $types, $line;

    $ret = $types[$type];
    if ($line['price']) {
        $ret .= '<br /><span style="white-space:nowrap;">'. (int)$line['price_payed'] .'&euro; / '. $line['price'] .'&euro; ['. (round((((int)$line['price_payed'] / (int)$line['price']) * 100), 1)) .'%]</span>';
    }
    return $ret;
}
