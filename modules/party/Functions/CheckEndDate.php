<?php

/**
 * @param string $enddate
 */
function CheckEndDate($enddate): bool|string
{
    global $func;

    if ($func->str2time($enddate) > $func->str2time($_POST['startdate'])) {
        return t('Der Endzeitpunkt muss nach dem Startzeitpunkt liegen');
    } else {
        return false;
    }
}
