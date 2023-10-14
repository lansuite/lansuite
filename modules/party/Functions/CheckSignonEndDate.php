<?php

/**
 * @param string $senddate
 */
function CheckSignonEndDate($senddate): bool|string
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
