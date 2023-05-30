<?php

/**
 * @param string $clan_name
 * @return string
 */
function ClanURLLinkUsrMgrSearch($clan_name)
{
    global $line;

    if ($clan_name != '' and $line['clanurl'] != '' and $line['clanurl'] != 'http://') {
        if (!str_starts_with($line['clanurl'], 'http://')) {
            $line['clanurl'] = 'http://'. $line['clanurl'];
        }
        return '<a href="'. $line['clanurl'] .'" target="_blank">'. $clan_name .'</a>';
    } else {
        return $clan_name;
    }
}
