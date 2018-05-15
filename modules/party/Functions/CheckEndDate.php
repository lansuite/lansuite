<?php

/**
 * @param string $enddate
 * @return bool|string
 */
function CheckEndDate($enddate)
{
    global $func;

    if ($func->str2time($enddate) < $func->str2time($_POST['startdate'])) {
        return t('Der Endzeitpunkt muss nach dem Startzeitpunkt liegen');
    } else {
        return false;
    }
}
