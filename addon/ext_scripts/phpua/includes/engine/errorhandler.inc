<?php

    startBenchmark(__FILE__, __FUNCTION__);
    
    // Turn error handling over to us (from the PHP engine) and check for installation/config files.
    if (DEBUG == 0) {
        set_error_handler("ErrorHandler");
        error_reporting(E_WARNING | E_ERROR | E_PARSE | E_USER_ERROR);
        if (file_exists(ABSOLUTEPATH . "install.php")) displayErrorMessage("Please read the phpUA Documentation for the installation proceedure.");
        if (!file_exists(ABSOLUTEPATH . CONFIGFILE)) displayErrorMessage("Could not find the file: " . CONFIGFILE . "<br>Ensure that 'install.php' has been uploaded, and run 'install.php' again.");
    }
    
    function displayErrorMessage($message)
    {
        startBenchmark(__FILE__, __FUNCTION__);
        global $header_displayed, $phpUA, $_LANG;
        // Define Fail-safe Variables
        if (empty($phpUA["CONFIG"]["SITE_HOME_NAME"]) && empty($phpUA["CONFIG"]["SITE_HOME_URL"])) {
            $phpUA["TEMPLATE"]->assign("SITE_HOME_NAME", "United Admins");
            $phpUA["TEMPLATE"]->assign("SITE_HOME_URL", "http://www.unitedadmins.com/");
        } else {
            $phpUA["TEMPLATE"]->assign("SITE_HOME_NAME", $phpUA["CONFIG"]["SITE_HOME_NAME"]);
            $phpUA["TEMPLATE"]->assign("SITE_HOME_URL", $phpUA["CONFIG"]["SITE_HOME_URL"]);
        }
        if (empty($phpUA["STYLE"])) $phpUA["STYLE"] = "default";
        if (empty($phpUA["CONFIG"]["LANG"])) $phpUA["CONFIG"]["LANG"] = "en-us";
        require_once ABSOLUTEPATH . "languages/lang_" . $phpUA["CONFIG"]["LANG"] . ".inc";
        $phpUA["LANGUAGE"] = $_LANG[$phpUA["CONFIG"]["LANG"]];
        // Navigation
        $server_index = "<a href=\"" . INDEXFILE . "?mode=index\">" . $phpUA["LANGUAGE"]["ServerIndex"] . "</a>";
        $forum = "<a href=\"http://forum.phpua.com/\">" . $phpUA["LANGUAGE"]["Forum"] . "</a>";
        $help = "<a href=\"doc/index.html\" target=\"_blank\">" . $phpUA["LANGUAGE"]["Help"] . "</a>";
        $site_nav = " | " . $server_index . " | " . $forum . " | " . $help . " | ";
        // Template Output
        $phpUA["TEMPLATE"]->assign("LANG", $_LANG[$phpUA["CONFIG"]["LANG"]]);
        $phpUA["TEMPLATE"]->assign("SITE_NAV", $site_nav);
        $phpUA["TEMPLATE"]->assign("ERROR_MSG", $message);
        $phpUA["TEMPLATE"]->assign("VERSION", VERSION);
        if (!$header_displayed) {
            $phpUA["TEMPLATE"]->display($phpUA["STYLE"] . "/html_header.tpl");
            $phpUA["TEMPLATE"]->display($phpUA["STYLE"] . "/body_header.tpl");
        }
        $phpUA["TEMPLATE"]->display($phpUA["STYLE"] . "/error.tpl");
        $phpUA["TEMPLATE"]->display($phpUA["STYLE"] . "/body_footer.tpl");
        $phpUA["TEMPLATE"]->display($phpUA["STYLE"] . "/html_footer.tpl");
        printBenchmark();
        flush();
        exit;
    }
    
    function ErrorHandler($errno, $errstr, $errfile, $errline)
    {
        startBenchmark(__FILE__, __FUNCTION__);
        if (!error_reporting()) return; // error was was prepended by an @
        switch ($errno) {
            case E_ERROR:
                $typestr = "Error";
                break;
            case E_WARNING:
                $typestr = "Warning";
                break;
            case E_NOTICE:
                if (DEBUG == 0) return;
                $typestr = "Notice";
                break;
            default:
                $typestr = "Unknown (" . $errno . ")";
            break;
        }
        $message  = "<b>PHP " . $typestr . "</b><br><br>";
        $message .= "An unpredicted behavior has occurred in the phpUA Engine.<br><br>";
        $message .= "<b>Message:</b> " . $errstr . "<br>";
        $message .= "<b>File:</b> " . $errfile . "<br>";
        $message .= "<b>Line:</b> " . $errline;
        endBenchmark(__FILE__, __FUNCTION__);
        displayErrorMessage($message);
    }
    
    endBenchmark(__FILE__, __FUNCTION__);
?>