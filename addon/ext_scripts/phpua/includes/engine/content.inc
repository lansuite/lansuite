<?php

    
    startBenchmark(__FILE__, __FUNCTION__);
    
    // Engine Credits
    addCredits($phpUA["LANGUAGE"]["phpUAEngine"], "Kris Splittgerber");
    addCredits("Smarty", "Monte Ohrt, Andrei Zmievski");
    addCredits($phpUA["LANGUAGE"]["SpecialThanks"], "Ben Anderson, Jeffrey \"botman\" Broome, Rob \"Maelstrom\" Abbot, Alfred Reynolds, Pierre Marie Baty, Stefan Hendriks, Adam \"Lumpy\" Kiepper, Peter-Paul Koch, Mike Foster, Monte Ohrt, Andrei Zmievski, Will Day, The PHP Group");
    
    // Here we define a few assignments that may be used in the templates.
    $phpUA["TEMPLATE"]->assign("SITE_HOME_NAME", $phpUA["CONFIG"]["SITE_HOME_NAME"]);
    $phpUA["TEMPLATE"]->assign("SITE_HOME_URL", $phpUA["CONFIG"]["SITE_HOME_URL"]);
    $phpUA["TEMPLATE"]->assign("VERSION", VERSION);
    
    // Header
    $phpUA["TEMPLATE"]->display($phpUA["STYLE"] . "/html_header.tpl");
    $phpUA["TEMPLATE"]->display($phpUA["STYLE"] . "/body_header.tpl");
    @ob_end_flush(); // This is supposed to prevent output buffering.
    
    // Tell displayErrorMessage() that we've output the header.
    $header_displayed = true;
    
    // If no $mode is specified, we default to the Server Index page.
    // Otherwise, for now, we'll assume the $mode exists and error check it later.
    if (empty($_REQUEST["mode"])) $mode = "index";
    else $mode = $_REQUEST["mode"];
    
    // If the mode exists as an include in the /includes/content directory then we do not invoke
    // any plugins.
    $modefound = false;
    if ($content_handle = opendir(ABSOLUTEPATH . "includes/content/")) {
        while (false !== ($file = readdir($content_handle))) {
            if (($file != ".") && ($file != "..") && (substr($file,-4) == ".inc")) {
                if ( $mode == substr($file,0,-4) ) {
                    // Navigation Menu
                    $server_index = "<a href=\"" . INDEXFILE . "?mod=phpua&action=show\">" . $phpUA["LANGUAGE"]["ServerIndex"] . "</a>";
                    $help = "<a href=\"doc/index.html\" target=\"_blank\">" . $phpUA["LANGUAGE"]["Help"] . "</a>";
                    $admin = "<a href=\"" . INDEXFILE . "?mod=phpua&action=settings\">" . $phpUA["LANGUAGE"]["Settings"] . "</a> | <a href=\"" . INDEXFILE . "?mod=phpua&action=plugins\">" . $phpUA["LANGUAGE"]["Plugins"] . "</a> | <a href=\"" . INDEXFILE . "?mod=phpua&action=server\">" . $phpUA["LANGUAGE"]["Servers"] . "</a> | <a href=\"" . INDEXFILE . "?mod=phpua&action=check\">" . $phpUA["LANGUAGE"]["UpdateCheck"] . "</a>";

                    
                    if ($mode == "logout") {
                        $site_nav = " | " . $server_index . " | " . $help . " | ";
                    } else {
                        $site_nav = " | " . $server_index . " | " . $auth . " | " . $help . " | ";
                    }
                    $site_admin_nav = " | " . $server_index . " | " . $admin . " | " . $auth . " | " . $help . " | ";
                    $index_admin_nav = " | " . $admin . " | ";
                    
                    $phpUA["TEMPLATE"]->assign("SITE_NAV", $site_nav);
                    $phpUA["TEMPLATE"]->assign("SITE_ADMIN_NAV", $site_admin_nav);
                    $phpUA["TEMPLATE"]->assign("INDEX_ADMIN_NAV", $index_admin_nav);
                    
                    // Include Content
                    $modefound = true;
                    include ABSOLUTEPATH . "includes/content/" . $file;
                    break;
                }
            }
        }
        closedir($content_handle);
    }
    else {
        displayErrorMessage($phpUA["LANGUAGE"]["ERROR_DIRECTORYERROR"] . "/includes/content");
    }
    
    if (!$modefound) {
        // If we got this far than we need to look for a plug-in mode.
        // In an effort to make phpUA user-friendly, we're going to do a bunch of error checking.
        // Make sure a server id is specified otherwise we don't know what server we want info about.
        if (!isset($_REQUEST["server"]) || empty($phpUA["CONFIG"]["SERVER"][$_REQUEST["server"]])) displayErrorMessage($phpUA["LANGUAGE"]["ERROR_BADSERVERID"]);
        // Evaluate the config line for the appropriate server.
        // Remember that explode() is faster than split() because it doesn't do REGEX evaluation.
        $temp = explode(":", $phpUA["CONFIG"]["SERVER"][$_REQUEST["server"]]);
        $server_game = $temp[0];
        $server_ip   = $temp[1];
        $server_port = $temp[2];
        // Make sure that the plugin needed to obtain info about this server still exists.
        if (!class_exists($phpUA["PLUGINS"][$server_game]["CLASS"])) displayErrorMessage($phpUA["LANGUAGE"]["ERROR_PLUGINNOTFOUND"] . " (plugin_" . strtolower($server_game) . ".class).");
        // Make sure that the mode we're about to call has been defined by a related include file.
        if (!function_exists($phpUA["PLUGINS"][$server_game]["MODES"][$mode]["FUNC"])) displayErrorMessage($phpUA["LANGUAGE"]["ERROR_BADMODE"]);
        
        // If we got this far then everything is valid.
        
        // Here we build a variable for the dynamic Site Navigation template assignment.
        $site_nav = "| <a href=\"" . INDEXFILE . "?mode=index\">" . $phpUA["LANGUAGE"]["ServerIndex"] . "</a> |";
        asort($phpUA["PLUGINS"][$server_game]["MODES"]);
        foreach ($phpUA["PLUGINS"][$server_game]["MODES"] as $key => $value) {
            if ($key != $mode) {
                $site_nav .= " <a href=\"" . INDEXFILE . "?mode=" . $key . "&amp;server=" . $_REQUEST["server"] . "\">" . $value["NAME"] . "</a> |";
            }
        }
        $phpUA["TEMPLATE"]->assign("SITE_NAV", $site_nav);
        
        // Finally the actual content is returned here.
        // The content is responsible for displaying the template(s) it needs.
        $functionname = $phpUA["PLUGINS"][$server_game]["MODES"][$mode]["FUNC"];
        $functionname($_REQUEST["server"], $server_ip, $server_port);
        
        // !NOTICE! The rest of this script may never be executed, depending on the content above.
        //          i.e. it may have a while(true){} loop.
    }
    
    $phpUA["TEMPLATE"]->display($phpUA["STYLE"] . "/body_footer.tpl");
    $phpUA["TEMPLATE"]->display($phpUA["STYLE"] . "/html_footer.tpl");
    
    endBenchmark(__FILE__, __FUNCTION__);
?>