<?php

/**
 * @param string $name
 * @return string
 */
function PGetActive($name)
{
    global $line, $cfg;

    if ($cfg['signon_partyid'] == $line['party_id']) {
        return "<b>".$name." (aktiv)</b>";
    } else {
        return $name;
    }
}
