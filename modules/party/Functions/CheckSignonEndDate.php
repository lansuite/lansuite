<?php

/**
 * @param string $senddate
 * @return bool|string
 */
function CheckSignonEndDate($senddate)
{
    global $func;
    if ($func->str2time($senddate) < $func->str2time($_POST['sstartdate'])) {
        return t('Der Anmeldeschluss muss nach dem Anmeldestart liegen');
    }
    if ($func->str2time($senddate) > $func->str2time($_POST['startdate'])) {
        return t('Der Anmeldeschluss muss vor dem Partystart liegen');
    } else {
        return false;
    }
}
