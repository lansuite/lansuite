<?php

/**
 * GetColor is used as a mastersearch callback function
 *
 * @return string
 */
function GetColor()
{
    global $line;

    if ($line['my']) {
        return "<font color='red'>-".number_format($line['movement'], 2, ',', '.') . " EUR</font>";
    }
    if (!$line['my']) {
        return "<font color='green'>+".number_format($line['movement'], 2, ',', '.') . " EUR</font>";
    }
}
