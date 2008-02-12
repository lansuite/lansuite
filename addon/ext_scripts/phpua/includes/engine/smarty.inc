<?php

    
    startBenchmark(__FILE__, __FUNCTION__);
    
    require_once(ABSOLUTEPATH . "includes/smarty/Smarty.class.php");
    $phpUA["TEMPLATE"] = new Smarty;
    $phpUA["TEMPLATE"]->template_dir = ABSOLUTEPATH . "templates/";
    $phpUA["TEMPLATE"]->compile_dir  = ABSOLUTEPATH . "includes/smarty/templates_c/";
    $phpUA["TEMPLATE"]->config_dir   = ABSOLUTEPATH . "includes/smarty/configs/";
    $phpUA["TEMPLATE"]->cache_dir    = ABSOLUTEPATH . "includes/smarty/cache/";
    
    endBenchmark(__FILE__, __FUNCTION__);
?>