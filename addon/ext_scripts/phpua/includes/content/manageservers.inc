<?php

    
    startBenchmark(__FILE__, __FUNCTION__);
    
    // !!! SECURITY !!!

    
    if (!empty($_REQUEST["action"])) {
        switch ($_REQUEST["action"]) {
            case $phpUA["LANGUAGE"]["Add"]:
                if (empty($_REQUEST["new_ip"]))   displayErrorMessage($phpUA["LANGUAGE"]["ERROR_BADIP"]);
                if (empty($_REQUEST["new_port"])) displayErrorMessage($phpUA["LANGUAGE"]["ERROR_BADPORT"]);
                if (empty($_REQUEST["new_game"])) displayErrorMessage($phpUA["LANGUAGE"]["ERROR_BADGAME"]);
                configAddServer($_REQUEST["new_ip"], $_REQUEST["new_port"], $_REQUEST["new_game"]);
                break;
            case $phpUA["LANGUAGE"]["Modify"]:
                if ($_REQUEST["server_id"] == "")    displayErrorMessage($phpUA["LANGUAGE"]["ERROR_SERVERID"]);
                if (empty($_REQUEST["server_ip"]))   displayErrorMessage($phpUA["LANGUAGE"]["ERROR_BADIP"]);
                if (empty($_REQUEST["server_port"])) displayErrorMessage($phpUA["LANGUAGE"]["ERROR_BADPORT"]);
                if (empty($_REQUEST["server_game"])) displayErrorMessage($phpUA["LANGUAGE"]["ERROR_BADGAME"]);
                configRemoveServer($_REQUEST["server_id"]);
                configAddServer($_REQUEST["server_ip"], $_REQUEST["server_port"], $_REQUEST["server_game"]);
                break;
            case $phpUA["LANGUAGE"]["Remove"]:
                if ($_REQUEST["server_id"] == "") displayErrorMessage($phpUA["LANGUAGE"]["ERROR_SERVERID"]);
                configRemoveServer($_REQUEST["server_id"]);
                break;
        }
    }
    reloadConfig();
    $server_array = array();
    $games_array = array();
    // Create an array of existing servers
    $i = -1;
    $x = -1;
    foreach ($phpUA["CONFIG"]["SERVER"] as $value) {
        $i++;
        // Decode server line (GAME:IP:PORT)
        $temp = explode(":", $value);
        $server_game = $temp[0];
        $server_ip   = $temp[1];
        $server_port = $temp[2];
        if (isset($phpUA["PLUGINS"][$server_game]["NAME"])) {
            $x++;
            $server_array[$x] = array("id"  =>$i,
                                      "ip"  =>$server_ip,
                                      "port"=>$server_port,
                                      "game"=>$server_game);
        }
    }
    // Create an array of games
    $no_plugins = true;
    $i = -1;
    foreach ($phpUA["PLUGINS"] as $key => $value) {
        if (!empty($phpUA["PLUGINS"][$key]["NAME"])) {
            $no_plugins = false;
            $i++;
            $games_array[$i] = array("id"  =>$key,
                                     "name"=>$phpUA["PLUGINS"][$key]["NAME"]);
        }
    }
    // Output template
    if (empty($phpUA["CONFIG"]["SERVER"])) $phpUA["TEMPLATE"]->assign("NO_SERVERS", true);
    else $phpUA["TEMPLATE"]->assign("NO_SERVERS", false);
    $phpUA["TEMPLATE"]->assign("NO_PLUGINS", $no_plugins);
    $phpUA["TEMPLATE"]->assign("SERVERS", $server_array);
    $phpUA["TEMPLATE"]->assign("GAMES", $games_array);
    $phpUA["TEMPLATE"]->display($phpUA["STYLE"] . "/admin/manageservers.tpl");
    
    endBenchmark(__FILE__, __FUNCTION__);
?>