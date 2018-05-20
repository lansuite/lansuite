<?php

/**
 * @param string $sstartdate
 * @return bool|string
 */
function CheckSignonStartDate($sstartdate)
{
    global $func;

    if ($func->str2time($sstartdate) > $func->str2time($_POST['startdate'])) {
        return t('Der Anmeldestart muss vor dem Partystart liegen');
    } else {
        return false;
    }
}
