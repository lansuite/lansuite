<?php

/**
 * @param boolean $league
 */
function CheckModeForWWCLLeague($league): bool|string
{
    if ($league and $_POST['mode'] != 'single' and $_POST['mode'] != 'double' and $_POST['mode'] != 'groups') {
        return t('WWCL-Turniere müssen im SE, DE oder Gruppenspiele Modus ausgetragen werden');
    } else {
        return false;
    }
}
