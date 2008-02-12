<?php

    
    startBenchmark(__FILE__, __FUNCTION__);
    
    // !!! SECURITY !!!

    
    // Using the existing (enabled) plug-in array, mark the plug-ins as enabled.
    foreach ($phpUA["PLUGINS"] as $key => $value) {
        if (!empty($phpUA["PLUGINS"][$key]["NAME"])) {
            $phpUA["PLUGINS"][$key]["ENABLED"] = true;
            foreach ($phpUA["PLUGINS"][$key]["MODES"] as $key2 => $value2) {
                $phpUA["PLUGINS"][$key]["MODES"][$key2]["ENABLED"] = true;
            }
        }
    }
    // Include the rest of the available plug-ins.
    loadPlugins(ABSOLUTEPATH . "plugins", "all");
    
    // Make any changes (refresh the page if changes are made)
    if (!empty($_REQUEST["action"])) {
        switch ($_REQUEST["action"]) {
            case $phpUA["LANGUAGE"]["Modify"]:
                $i = 0;
                while (!empty($_REQUEST["plugin_" . $i])) {
                    configRemoveLine($_REQUEST["plugin_" . $i]);
                    if (!empty($_REQUEST["plugin_" . $i . "_enabled"])) {
                        if ( $_REQUEST["plugin_" . $i . "_enabled"]) configAddLine($_REQUEST["plugin_" . $i], "1");
                    }
                    $i++;
                }
                echo "<script language=\"Javascript\" type=\"text/javascript\">document.location.href = '" . INDEXFILE . "?mode=manageplugins';</script>";
                exit;
                break;
        }
    }
    
    // Create template arrays of plug-in information
    $i = -1;
    $plugins_array = array();
    foreach ($phpUA["PLUGINS"] as $key => $value) {
        $i++;
        if (empty($value["ENABLED"])) $value["ENABLED"] = false;
        $plugins_array[$i] = array("id"      =>$i,
                                   "name"    =>$value["NAME"],
                                   "type"    =>"Game",
                                   "enabled" =>$value["ENABLED"],
                                   "filename"=>$value["FILENAME"]);
        if ($value["ENABLED"]) {
            foreach ( $phpUA["PLUGINS"][$key]["MODES"] as $value2 ) {
                $i++;
                if (empty($value2["ENABLED"])) $value2["ENABLED"] = false;
                $plugins_array[$i] = array("id"      =>$i,
                                           "type"    =>"Mode",
                                           "name"    =>$value["NAME"] . ": " . $value2["NAME"],
                                           "enabled" =>$value2["ENABLED"],
                                           "filename"=>$value2["FILENAME"]);
            }
        }
    }
    
    // Output template
    if (empty($phpUA["PLUGINS"]) ) $phpUA["TEMPLATE"]->assign("NO_PLUGINS", true);
    else $phpUA["TEMPLATE"]->assign("NO_PLUGINS", false);
    $phpUA["TEMPLATE"]->assign("PLUGINS", $plugins_array);
    $phpUA["TEMPLATE"]->display($phpUA["STYLE"] . "/admin/manageplugins.tpl");
    
    endBenchmark(__FILE__, __FUNCTION__);
?>