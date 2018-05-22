<?php

/**
 * @return string
 */
function ServerStatus()
{
    global $cfg, $line;

    // Wenn Intranetversion, erreichbarkeit testen
    if ($cfg["sys_internet"] == 0 and (!get_cfg_var("safe_mode"))) {
        PingServer($line['ip'], $line['port']);

        if ($line['available'] == 1) {
            return "<div class=\"tbl_green\">Online</div>";
        } elseif ($line['available'] == 2) {
            return "<div class=\"tbl_red\">Port Offline</div>";
        } else {
            return "<div class=\"tbl_red\">IP Offline</div>";
        }
    } else {
        return "-";
    }
}
