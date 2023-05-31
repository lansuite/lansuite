<?php

/**
 * @param string $clan_name
 * @return string
 */
function ClanURLLinkUsrMgrSearchInc($clan_name)
{
    global $line, $func;

    if ($clan_name == '') {
        return '';
    } elseif ($func->isModActive('clanmgr')) {
        return '<a href="index.php?mod=clanmgr&action=clanmgr&step=2&clanid='. $line['clanid'] .'">'. $clan_name .'</a>';
    } elseif ($clan_name != '' and $line['clanurl'] != '' and $line['clanurl'] != 'http://') {
        if (!str_starts_with($line['clanurl'], 'http://')) {
            $line['clanurl'] = 'http://'. $line['clanurl'];
        }
        return '<a href="'. $line['clanurl'] .'" target="_blank">'. $clan_name .'</a>';
    } else {
        return $clan_name;
    }
}
