<?php

/**
 * @param boolean $league
 */
function CheckModeForLeague($league): bool|string
{
    if ($league and $_POST['mode'] != 'single' and $_POST['mode'] != 'double') {
        return t('Diese Liga ist nur im SE und DE Modus möglich');
    } else {
        return false;
    }
}
