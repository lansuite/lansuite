<?php

    
    startBenchmark(__FILE__, __FUNCTION__);
    
    // !!! SECURITY !!!

    if (!empty($_REQUEST["action"])) {
        switch ($_REQUEST["action"]) {
            case $phpUA["LANGUAGE"]["Modify"]:
                if (empty($_REQUEST["config_site_home_name"])) $_REQUEST["config_site_home_name"] = "United Admins";
                if (empty($_REQUEST["config_site_home_url"]))  $_REQUEST["config_site_home_url"]  = "http://www.unitedadmins.com/";
                if (empty($_REQUEST["config_timeout"]))        $_REQUEST["config_timeout"]        = "5";
                if (empty($_REQUEST["config_interval"]))       $_REQUEST["config_interval"]       = "5";
                if (empty($_REQUEST["config_timelimit"]))      $_REQUEST["config_timelimit"]      = "300";
                if (empty($_REQUEST["config_style"]))          $_REQUEST["config_style"]          = "default";
                if (empty($_REQUEST["config_lang"]))           $_REQUEST["config_lang"]           = "en-us";
                if ((ini_get("safe_mode")) && ($_REQUEST["config_timelimit"] > ini_get("max_execution_time"))) {
                    $_REQUEST["config_timelimit"] =  ini_get("max_execution_time");
                }
                configRemoveLine("SITE_HOME_NAME");
                configAddLine("SITE_HOME_NAME", stripslashes($_REQUEST["config_site_home_name"]));
                configRemoveLine("SITE_HOME_URL");
                configAddLine("SITE_HOME_URL", stripslashes($_REQUEST["config_site_home_url"]));
                configRemoveLine("TIMEOUT");
                configAddLine("TIMEOUT", $_REQUEST["config_timeout"]);
                configRemoveLine("INTERVAL");
                configAddLine("INTERVAL", $_REQUEST["config_interval"]);
                configRemoveLine("TIMELIMIT");
                configAddLine("TIMELIMIT", $_REQUEST["config_timelimit"]);
                configRemoveLine("STYLE");
                configAddLine("STYLE", stripslashes($_REQUEST["config_style"]));
                configRemoveLine("LANG");
                configAddLine("LANG", $_REQUEST["config_lang"]);
                echo "<script language=\"Javascript\" type=\"text/javascript\">document.location.href = '" . INDEXFILE . "?mode=settings';</script>";
                exit;
                break;
        }
    }
    $phpUA["TEMPLATE"]->assign("CONFIG_SITE_HOME_NAME", $phpUA["CONFIG"]["SITE_HOME_NAME"]);
    $phpUA["TEMPLATE"]->assign("CONFIG_SITE_HOME_URL", $phpUA["CONFIG"]["SITE_HOME_URL"]);
    $phpUA["TEMPLATE"]->assign("CONFIG_TIMEOUT", $phpUA["CONFIG"]["TIMEOUT"]);
    $phpUA["TEMPLATE"]->assign("CONFIG_INTERVAL", $phpUA["CONFIG"]["INTERVAL"]);
    $phpUA["TEMPLATE"]->assign("CONFIG_TIMELIMIT", $phpUA["CONFIG"]["TIMELIMIT"]);
    $phpUA["TEMPLATE"]->assign("CONFIG_STYLE", $phpUA["CONFIG"]["STYLE"]);
    $phpUA["TEMPLATE"]->assign("CONFIG_LANG", $phpUA["CONFIG"]["LANG"]);
    
    unset($styles);
    $styles = array();
    if ($templates_handle = opendir(ABSOLUTEPATH . "templates/")) {
        while (false !== ($file = readdir($templates_handle))) {
            if (($file != ".") && ($file != "..") && ($file != "CVS") && ($file != "wap") && ($file != "rss") && ($file != "javascript") && (is_dir(ABSOLUTEPATH . "templates/" . $file))) {
                $styles[] = array("name"=>$file);
            }
        }
        closedir($templates_handle);
    }
    else {
        displayErrorMessage($phpUA["LANGUAGE"]["ERROR_DIRECTORYERROR"] . "templates");
    }
    $phpUA["TEMPLATE"]->assign("STYLES", $styles);
    
    if ($handle = opendir(ABSOLUTEPATH . "languages/")) {
        while (false !== ($file = readdir($handle))) {
            if (($file != ".") && ($file != "..") && (substr($file,0,5) == "lang_") && (substr($file,-4) == ".inc")) { 
                require_once ABSOLUTEPATH . "languages/" . $file;
            }
        }
        closedir($handle);
    }
    else {
        displayErrorMessage($phpUA["LANGUAGE"]["ERROR_DIRECTORYERROR"] . "languages");
    }
    
    $languages = array();
    foreach ($_LANG as $key => $value) {
        $languages[] = array("code"=>$key,
                             "name"=>$value[0]);
    }
    $phpUA["TEMPLATE"]->assign("LANGUAGES", $languages);
    
    $phpUA["TEMPLATE"]->assign("SAFEMODE", ini_get("safe_mode"));
    $phpUA["TEMPLATE"]->assign("SAFEMODE_TIMELIMIT", ini_get("max_execution_time"));
    
    // Output template
    $phpUA["TEMPLATE"]->display($phpUA["STYLE"] . "/admin/settings.tpl");
    
    endBenchmark(__FILE__, __FUNCTION__);
?>