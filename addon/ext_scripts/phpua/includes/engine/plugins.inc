<?php

    
    startBenchmark(__FILE__, __FUNCTION__);
    
    $phpUA["PLUGINS"] = array();
    loadPlugins(ABSOLUTEPATH . "plugins", "enabled");
    
    function loadPlugins($path, $type)
    {
        startBenchmark(__FILE__, __FUNCTION__);
        global $phpUA;
        if ($handle = @opendir($path)) {
            while (false !== ($file = readdir($handle))) {
                if (substr($file,-4) == ".inc") {
                    switch ($type) {
                        case "all":
                            require_once $path . "/" . $file;
                            break;
                        case "enabled":
                            if (isset($phpUA["CONFIG"][$file])) require_once $path . "/" . $file;
                            break;
                    }
                } elseif ((is_dir($path . "/" . $file)) && ($file != ".") && ($file != "..")) {
                    loadPlugins($path . "/" . $file, $type);
                }
            }
            closedir($handle);
        } else {
            displayErrorMessage($phpUA["LANGUAGE"]["ERROR_DIRECTORYERROR"] . "plugins");
        }
        endBenchmark(__FILE__, __FUNCTION__);
    }
    
    function pluginRegisterGame($game, $name, $class, $filename)
    {
        startBenchmark(__FILE__, __FUNCTION__ . " -> " . $game);
        global $phpUA;
        $phpUA["PLUGINS"][$game]["NAME"] = $name;
        $phpUA["PLUGINS"][$game]["CLASS"] = $class;
        $phpUA["PLUGINS"][$game]["FILENAME"] = $filename;
        if (empty($phpUA["PLUGINS"][$game]["MODES"])) $phpUA["PLUGINS"][$game]["MODES"] = array();
        endBenchmark(__FILE__, __FUNCTION__ . " -> " . $game);
    }
    
    function pluginRegisterMode($game, $id, $name, $func, $public="0", $online="0", $offline="0", $filename)
    {
        startBenchmark(__FILE__, __FUNCTION__ . " -> " . $game . " -> " . $id);
        global $phpUA;
        $phpUA["PLUGINS"][$game]["MODES"][$id]["NAME"] = $name;
        $phpUA["PLUGINS"][$game]["MODES"][$id]["FUNC"] = $func;
        $phpUA["PLUGINS"][$game]["MODES"][$id]["PUBLIC"] = $public;
        $phpUA["PLUGINS"][$game]["MODES"][$id]["ONLINE"] = $online;
        $phpUA["PLUGINS"][$game]["MODES"][$id]["OFFLINE"] = $offline;
        $phpUA["PLUGINS"][$game]["MODES"][$id]["FILENAME"] = $filename;
        endBenchmark(__FILE__, __FUNCTION__ . " -> " . $game . " -> " . $id);
    }
    
    function socket_timeout($handle)
    {
        if (DEBUG > 1) startBenchmark(__FILE__, __FUNCTION__);
        $array = socket_get_status($handle);
        if (DEBUG > 1) endBenchmark(__FILE__, __FUNCTION__);
        if ($array["timed_out"]) return true;
        else return false;
    }
    
    endBenchmark(__FILE__, __FUNCTION__);
?>