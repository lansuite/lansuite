<?php

    
    startBenchmark(__FILE__, __FUNCTION__);
    
    // Output HTML (header)
    $phpUA["TEMPLATE"]->display($phpUA["STYLE"] . "/index_header.tpl");
    flush();
    
    // Test each server for a response
    if (empty($phpUA["CONFIG"]["SERVER"])) {
        $phpUA["TEMPLATE"]->display($phpUA["STYLE"] . "/index_noservers.tpl");
    } else {
        $i = -1;
        foreach ($phpUA["CONFIG"]["SERVER"] as $value) { // "For each server listed in the config file..."
            $i++; // Keep track of the server count (becomes server_id)
            startBenchmark(__FILE__, $i);
            // Decode server line (GAME:IP:PORT)
            $temp = explode(":", $value);
            $server_game = $temp[0];
            $server_ip   = $temp[1];
            $server_port = $temp[2];
            // Setup Plugin
            if (( !empty($phpUA["PLUGINS"][$server_game]["CLASS"])) && (class_exists($phpUA["PLUGINS"][$server_game]["CLASS"]) )) { // Plug-in exists and is enabled
                $plugin = new $phpUA["PLUGINS"][$server_game]["CLASS"]($i, $server_ip, $server_port);
                // Setup Template
                $phpUA["TEMPLATE"]->assign("SERVER_ID", $i);
                $phpUA["TEMPLATE"]->assign("SERVER_IP", $server_ip);
                $phpUA["TEMPLATE"]->assign("SERVER_PORT", $server_port);
                // Test The Server
                if ($plugin->isOnline()) {
                    $info = $plugin->getInfo();
                    $phpUA["TEMPLATE"]->assign("SERVER_NAME", $info["name"]);
                    $phpUA["TEMPLATE"]->assign("SERVER_DESCRIPTION", $phpUA["PLUGINS"][$server_game]["NAME"] . ": " . $info["desc"]);
                    $server_modes = "|";
                    asort($phpUA["PLUGINS"][$server_game]["MODES"]);
                    foreach ($phpUA["PLUGINS"][$server_game]["MODES"] as $key => $value) {
                        if (( $key["ONLINE"] && $key["PUBLIC"] ) || ( $key["ONLINE"] && !$key["PUBLIC"] && $_SESSION["authenticated"] )) {
                            $server_modes .= " <a href=\"" . INDEXFILE . "?mode=" . $key . "&amp;server=" . $i . "\">" . $value["NAME"] . "</a> |";
                        }
                    }
                    if ($server_modes == "|") $server_modes = "";
                    $phpUA["TEMPLATE"]->assign("SERVER_MODES", $server_modes);
                    $phpUA["TEMPLATE"]->display($phpUA["STYLE"] . "/index_online.tpl");
                    flush();
                } else {
                    // Server is offline and there is no more information we can expect to
                    // gain, so we will display this template with the assignments we've
                    // already assigned (see Setup Template above).
                    $phpUA["TEMPLATE"]->assign("SERVER_DESCRIPTION", $phpUA["PLUGINS"][$server_game]["NAME"] . ": " . $phpUA["LANGUAGE"]["ServerOffline"]);
                    $server_modes = "|";
                    foreach ($phpUA["PLUGINS"][$server_game]["MODES"] as $key => $value) {
                        if (($key["OFFLINE"] && $key["PUBLIC"]) || ($key["OFFLINE"] && !$key["PUBLIC"] && $_SESSION["authenticated"])) {
                            $site_nav .= " <a href=\"" . INDEXFILE . "?mode=" . $key . "&amp;server=" . $i . "\">" . $key["NAME"] . "</a> |";
                        }
                    }
                    if ($server_modes == "|") $server_modes = "";
                    $phpUA["TEMPLATE"]->assign("SERVER_MODES", $server_modes);
                    $phpUA["TEMPLATE"]->display($phpUA["STYLE"] . "/index_offline.tpl");
                    flush();
                }
            }
            endBenchmark(__FILE__, $i);
        }
    }
    
    // Output HTML (footer)
    $phpUA["TEMPLATE"]->display($phpUA["STYLE"] . "/index_footer.tpl");
    
    endBenchmark(__FILE__, __FUNCTION__);
?>