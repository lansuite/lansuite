<?php

    

    
    startBenchmark(__FILE__, __FUNCTION__);
    
    // Decide which file to use.
    if (isset($_REQUEST["language"])) $language = $_REQUEST["language"];
    else $language = $_SERVER["HTTP_ACCEPT_LANGUAGE"];
    
    // Ensure that we can use that file, if not, default to config setting.
    if (!file_exists(ABSOLUTEPATH . "languages/lang_" . $language . ".inc")) {
        $language = $phpUA["CONFIG"]["LANG"];
        if (!file_exists(ABSOLUTEPATH . "languages/lang_" . $language . ".inc")) {
            if (file_exists(ABSOLUTEPATH . "languages/lang_en-us.inc")) {
                configRemoveLine("LANG");
                configAddLine("LANG", "en-us");
                $language = $phpUA["CONFIG"]["LANG"];
            } else {
                displayErrorMessage("Unable to find the requested/configured/default language file.");
            }
        }
    }
    
    // Use the $language file for translation.
    require_once ABSOLUTEPATH . "languages/lang_" . $language . ".inc";
    $phpUA["LANGUAGE"] = $_LANG[$language];
    $phpUA["TEMPLATE"]->assign("LANG", $_LANG[$language]);
    
    endBenchmark(__FILE__, __FUNCTION__);
?>