<?php

/**
 * @param int $minage
 * @return string
 */
function GetMinimumAgeString($minage)
{
    return ($minage == 0) ? t('Kein Mindestalter') : $minage;
}
