<?php

/**
 * @param boolean $role
 * @return string
 */
function ShowRole($role)
{
    global $auth, $line;

    if ($role) {
        $ret = t('Clan-Admin');
    } else {
        $ret = t('Clan-Mitglied');
    }

    if (($_GET['clanid'] == $auth['clanid'] and $auth['clanadmin']) or $auth['type'] > 1) {
        $ret = '<a href="index.php?mod=clanmgr&action=clanmgr&step=50&clanid='. $_GET['clanid'] .'&userid='. $line['userid'] .'">'. $ret .'</a>';
    }

    return $ret;
}
