<?php

    //script @ kuppe

    $phpUA["ENGINE"]["TIME"] = time();
    if (version_compare(phpversion(), "4.1.0", "<")) die("phpUA requires PHP version 4.1.0 or greater. You are running version "  . phpversion() . ".");
    
    // PHP Settings
    ini_set("register_globals", 0);
    ini_set("arg_separator.output", "&amp;");
    ini_set("zlib.output_compression", "Off");
    
    // Constants
    define("ABSOLUTEPATH", dirname(__FILE__) . "/");
    define("INDEXFILE", basename(__FILE__));
    define("CONFIGFILE", "phpua.cfg.php");
    define("LOGFILE", "phpua.log.php");
    define("VERSION", "0.1 Alpha");
    define("DEBUG", 0);
    if (DEBUG > 0) error_reporting(E_ALL);
    
    // Engine
    require_once ABSOLUTEPATH . "includes/engine/benchmark.inc";
    startBenchmark(__FILE__, __FUNCTION__);
    
    require_once ABSOLUTEPATH . "includes/engine/smarty.inc";
    require_once ABSOLUTEPATH . "includes/engine/credits.inc";
    require_once ABSOLUTEPATH . "includes/engine/errorhandler.inc";
    require_once ABSOLUTEPATH . "includes/engine/config.inc";
    require_once ABSOLUTEPATH . "includes/engine/languages.inc";
    require_once ABSOLUTEPATH . "includes/engine/log.inc";
    require_once ABSOLUTEPATH . "includes/engine/auth.inc";
    require_once ABSOLUTEPATH . "includes/engine/plugins.inc";
    require_once ABSOLUTEPATH . "includes/engine/content.inc";
    
    endBenchmark(__FILE__, __FUNCTION__);
    printBenchmark();
?>