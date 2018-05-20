<?php

/**
 * @param int $id
 * @return string
 */
function GetActiveState($id)
{
    global $cfg;

    if ($cfg['signon_partyid'] == $id) {
        return 'Aktive Party';
    } else {
        return '<a href="index.php?mod=party&action=show&step=10&party_id='. $id .'">Aktivieren</a>';
    }
}
