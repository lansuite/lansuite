<?php

/**
 * @param string $price_text
 * @return string
 */
function p_price($price_text)
{
    global $line, $cfg;

    if ($line['price']) {
        $ret = $price_text .' ('. $line['price'] .' '. $cfg['sys_currency'] .')';
    } else {
        $ret = $price_text;
    }

    if ($ret) {
        return '<a href="index.php?mod=usrmgr&action=party&user_id='. $line['userid'] .'">'. $ret .'</a>';
    } else {
        return '';
    }
}
