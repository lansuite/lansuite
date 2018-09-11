<?php

/**
 * @param boolean $league
 * @return bool|string
 */
function CheckModeForLeague($league)
{
    if ($league and $_POST['mode'] != 'single' and $_POST['mode'] != 'double') {
        return t('Diese Liga ist nur im SE und DE Modus möglich');
    } else {
        return false;
    }
}
