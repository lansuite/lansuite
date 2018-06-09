<?php

/**
 * @param boolean $league
 * @return bool|string
 */
function CheckModeForWWCLLeague($league)
{
    if ($league and $_POST['mode'] != 'single' and $_POST['mode'] != 'double' and $_POST['mode'] != 'groups') {
        return t('WWCL-Turniere müssen im SE, DE oder Gruppenspiele Modus ausgetragen werden');
    } else {
        return false;
    }
}
