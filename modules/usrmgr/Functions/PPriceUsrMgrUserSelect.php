<?php

/**
 * @param string $price_text
 * @return string
 */
function p_priceUsrMgrUserSelect($price_text)
{
    global $line, $cfg;

    if ($line['price']) {
        return $price_text .' ('. $line['price'] .' '. $cfg['sys_currency'] .')';
    } else {
        return $price_text;
    }
}
