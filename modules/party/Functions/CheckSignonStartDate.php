<?php

/**
 * @param string $sstartdate
 */
function CheckSignonStartDate($sstartdate): bool|string
{
    global $func;

    if ($func->str2time($sstartdate) > $func->str2time($_POST['startdate'])) {
        return t('Der Anmeldestart muss vor dem Partystart liegen');
    } else {
        return false;
    }
}
