<?php

    
    startBenchmark(__FILE__, __FUNCTION__);
    
    // Initialise Session Management
    session_start();
    
    if (empty($_REQUEST["mode"])) $_REQUEST["mode"] = NULL;

    // Login authentication (only needed when we actually want to login)
    if ($_REQUEST["mode"] == "authenticate") {
        // Some basic error checking to make sure a username and password has been specified.
        if (empty($_REQUEST["username"])) displayErrorMessage($phpUA["LANGUAGE"]["ERROR_BADUSERNAME"]);
        if (empty($_REQUEST["password"])) displayErrorMessage($phpUA["LANGUAGE"]["ERROR_BADPASSWORD"]);

        // Go through each ADMIN= line from CONFIGFILE and validate.
        foreach ($phpUA["CONFIG"]["ADMIN"] as $value) {
            // Decode the username and password values from the config line
            $temp = explode(":", $value);
            $username = $temp[0];
            $password = $temp[1];
            if ($username == $_REQUEST["username"]) {
                if ($password == md5($_REQUEST["password"])) {
                    // Username and password match with their corresponding record on file.
                    // Flag the current session as authenticated.
                    $_SESSION["authenticated"] = true;
                    $_SESSION["username"] = $username;
                    $_REQUEST["mode"] = "index";
                    // We have what we came for, no use going any further.
                    // Leave to foreach loop.
                    break;
                }
                else {
                    // Wrong password for a valid username, no use going any further.
                    // Leave the foreach loop.
                    break;
                }
            }
        }
        // If we're not authenticated by now, log the attempt and display an error message.
        if (!$_SESSION["authenticated"]) {
            writeLog("Failed Login Attempt for Username: " . $_REQUEST["username"]);
            displayErrorMessage($phpUA["LANGUAGE"]["ERROR_ACCESSDENIED"]);
        }
    }
    elseif ($_REQUEST["mode"] == "logout") {
        session_unset();
        $_REQUEST["mode"] = "index";
    }

    // Confirm that we are NOT authenticated by default! This also makes sure that the
    // index 'authenticated' is defined before we start evaluating it.
    if (empty($_SESSION["authenticated"])) {
        $_SESSION["authenticated"] = false;
        $_SESSION["username"] = $phpUA["LANGUAGE"]["Guest"];
    }

    // This prevents locking as this script may take a while before it finishes execution.
    session_write_close();

    // These template assignments get set regardless of $mode
    $phpUA["TEMPLATE"]->assign("AUTHENTICATED", $_SESSION["authenticated"]);
    $phpUA["TEMPLATE"]->assign("USERNAME", $_SESSION["username"]);

    endBenchmark(__FILE__, __FUNCTION__);
?>