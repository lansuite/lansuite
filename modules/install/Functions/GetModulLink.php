<?php

/**
 * @param string $modul
 * @return string
 */
function GetModulLink($modul)
{
    return "<a href=\"index.php?mod=install&action=mod_cfg&step=10&module=".$modul."\">".$modul."</a>";
}
