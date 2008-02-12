<?php

    
    startBenchmark(__FILE__, __FUNCTION__);
    
    // Use the file CONFIGFILE (constant) to remember some variables that
    // we have previously defined and load them back into memory.
    
    // Data Storage Format:
    //   # comment (and thus ignored)
    //   variable1=value1
    //   variable2=value2
    // etc.
    
    function reloadConfig()
    {
        startBenchmark(__FILE__, __FUNCTION__);
        unset($GLOBALS["phpUA"]["CONFIG"]);
        global $phpUA;
        
        $phpUA["CONFIG"]["SERVER"] = array();
        $phpUA["CONFIG"]["ADMIN"]  = array();
        
        // Explaination of each line is over here ->>>>>>>>>>>>>>>>>>>>>>> See? :)
        clearstatcache();                                               // Prevent remembering the filesize() when we reloadConfig
        $fp = @fopen(ABSOLUTEPATH . CONFIGFILE, "r");                   // Open the file
        $contents = fread($fp, filesize(ABSOLUTEPATH . CONFIGFILE));    // Put the file into $contents
        $lines = explode("\n", $contents);                              // Fill array with each line:
        for ($i=0; $i<count($lines); $i++) {                            // Loop: "For each line in the file..."
            $lines[$i] = trim($lines[$i]);                              //   Make sure we only get something valuable
            if (!empty($lines[$i]) && 
                 substr($lines[$i],0,2) != "<?" &&
                 substr($lines[$i],0,1) != "#") {
                $pair = explode("=", $lines[$i]);                       //        Fill array with either side of the '='
                if ($pair[0] == "SERVER") {                           //     Condition: "If it's defining a server..."
                    $phpUA["CONFIG"]["SERVER"][] = trim($pair[1]);      //       Define the variable
                }                                                       //     End Condition
                elseif ($pair[0] == "ADMIN") {                        //     Condition: "Or, if it's defining an admin..."
                    $phpUA["CONFIG"]["ADMIN"][] = trim($pair[1]);       //       Define the variable
                }                                                       //     End Condition
                else {                                                  //     Condition: "Otherwise..."
                    $phpUA["CONFIG"][$pair[0]] = trim($pair[1]);        //       Define the variable
                }                                                       //     End Condition
            }                                                           //   End Condition
        }                                                               // End Loop
        fclose($fp);                                                    // Close the file
        
        if (isset($_REQUEST["style"])) $phpUA["STYLE"] = $_REQUEST["style"];
        elseif (empty($phpUA["CONFIG"]["STYLE"])) $phpUA["CONFIG"]["STYLE"] = "default";
        else $phpUA["STYLE"] = $phpUA["CONFIG"]["STYLE"];
        
        endBenchmark(__FILE__, __FUNCTION__);
    }
    
    // Basic Operations for simple Key => Value pairs
    
    function configAddLine($key, $value)
    {
        startBenchmark(__FILE__, __FUNCTION__);
        global $phpUA;
        if ( (strstr($key, "#")) || (strstr($value, "#")) ||
             (strstr($key, "=")) || (strstr($value, "=")) 
           ) displayErrorMessage($phpUA["LANGUAGE"]["ERROR_BADCHARS"]);
        if (!$handle = @fopen(ABSOLUTEPATH . CONFIGFILE, "a")) displayErrorMessage($phpUA["LANGUAGE"]["ERROR_FILERROR"] . ABSOLUTEPATH . CONFIGFILE);
        fwrite($handle, $key . "=" . $value . "\n");
        fclose($handle);
        reloadConfig();
        endBenchmark(__FILE__, __FUNCTION__);
    }
    
    function configRemoveLine($key)
    {
        startBenchmark(__FILE__, __FUNCTION__);
        global $phpUA;
        if ((strstr($key, "#")) || (strstr($key, "="))) displayErrorMessage($phpUA["LANGUAGE"]["ERROR_BADCHARS"]);
        // read config into memory and modify it
        if (!$handle = @fopen(ABSOLUTEPATH . CONFIGFILE, "r")) displayErrorMessage($phpUA["LANGUAGE"]["ERROR_FILERROR"] . ABSOLUTEPATH . CONFIGFILE);
        $i = -1;
        $x = -1;
        while (!feof($handle)) {
           $i++;
           $lines[$i] = fgets($handle,1024);
           $pair = explode("=", $lines[$i]);
           if ( $pair[0] == $key ) {
                     $lines[$i] = "D\n";
           }
        }
        fclose($handle);
        // write a new config file from the one in memory
        if (!$handle = @fopen(ABSOLUTEPATH . CONFIGFILE, "w")) displayErrorMessage($phpUA["LANGUAGE"]["ERROR_FILERROR"] . ABSOLUTEPATH . CONFIGFILE);
        for ($i=0; $i<count($lines); $i++) {
            if ($lines[$i] != "D\n") fwrite($handle, $lines[$i]);
        }
        fclose($handle);
        reloadConfig();
        endBenchmark(__FILE__, __FUNCTION__);
    }
    
    // Custom Operations for Built-In Arrays
    
    function configAddServer($server_ip, $server_port, $server_game)
    {
        startBenchmark(__FILE__, __FUNCTION__);
        global $phpUA;
        if ((strstr($server_ip, "#")) || (strstr($server_port, "#")) || (strstr($server_game, "#")) ||
            (strstr($server_ip, "=")) || (strstr($server_port, "=")) || (strstr($server_game, "=")) ||
            (strstr($server_ip, ":")) || (strstr($server_port, ":")) || (strstr($server_game, ":")) 
           ) displayErrorMessage($phpUA["LANGUAGE"]["ERROR_FILERROR"] . ABSOLUTEPATH . CONFIGFILE);
        if (!$handle = @fopen(ABSOLUTEPATH . CONFIGFILE, "a")) displayErrorMessage($phpUA["LANGUAGE"]["ERROR_FILERROR"] . ABSOLUTEPATH . CONFIGFILE);
        fwrite($handle, "SERVER=" . $server_game . ":" . $server_ip . ":" . $server_port . "\n");
        fclose($handle);
        reloadConfig();
        endBenchmark(__FILE__, __FUNCTION__);
    }
    
    function configRemoveServer($server_id)
    {
        startBenchmark(__FILE__, __FUNCTION__);
        global $phpUA;
        if ((strstr($server_id, "#")) || (strstr($server_id, "=")) || (strstr($server_id, ":")) 
           ) displayErrorMessage($phpUA["LANGUAGE"]["ERROR_BADCHARS"]);
        // read config into memory and modify it
        if (!$handle = @fopen(ABSOLUTEPATH . CONFIGFILE, "r")) displayErrorMessage($phpUA["LANGUAGE"]["ERROR_FILERROR"] . ABSOLUTEPATH . CONFIGFILE);
        $i = -1;
        $x = -1;
        while (!feof($handle)) {
           $i++;
           $lines[$i] = fgets($handle,1024);
           $pair = explode("=", $lines[$i]);
           if ($pair[0] == "SERVER") {
                  $x++;
                  if ($x == $server_id) {
                         $lines[$i] = "D\n";
                  }
           }
        }
        fclose($handle);
        // write a new config file from the one in memory
        if (!$handle = @fopen(ABSOLUTEPATH . CONFIGFILE, "w")) displayErrorMessage($phpUA["LANGUAGE"]["ERROR_FILERROR"] . ABSOLUTEPATH . CONFIGFILE);
        for ($i=0; $i<count($lines); $i++) {
            if ($lines[$i] != "D\n") fwrite($handle, $lines[$i]);
        }
        fclose($handle);
        reloadConfig();
        endBenchmark(__FILE__, __FUNCTION__);
    }
    
    function configAddAdmin($admin_name, $admin_pswd)
    {
        startBenchmark(__FILE__, __FUNCTION__);
        global $phpUA;
        if ((strstr($admin_name, "#")) || (strstr($admin_pswd, "#")) ||
            (strstr($admin_name, "=")) || (strstr($admin_pswd, "=")) ||
            (strstr($admin_name, ":")) || (strstr($admin_pswd, ":"))
           ) displayErrorMessage($phpUA["LANGUAGE"]["ERROR_BADCHARS"]);
        if (!$handle = @fopen(ABSOLUTEPATH . CONFIGFILE, "a")) displayErrorMessage($phpUA["LANGUAGE"]["ERROR_FILERROR"] . ABSOLUTEPATH . CONFIGFILE);
        fwrite($handle, "ADMIN=" . $admin_name . ":" . $admin_pswd . "\n");
        fclose($handle);
        reloadConfig();
        endBenchmark(__FILE__, __FUNCTION__);
    }
    
    function configRemoveAdmin($admin_id)
    {
        startBenchmark(__FILE__, __FUNCTION__);
        global $phpUA;
        if ((strstr($admin_id, "#")) || (strstr($admin_id, "=")) || (strstr($admin_id, ":")) 
           ) displayErrorMessage($phpUA["LANGUAGE"]["ERROR_BADCHARS"]);
        // read config into memory and modify it
        if (!$handle = @fopen(ABSOLUTEPATH . CONFIGFILE, "r")) displayErrorMessage($phpUA["LANGUAGE"]["ERROR_FILERROR"] . ABSOLUTEPATH . CONFIGFILE);
        $i = -1;
        $x = -1;
        while (!feof($handle)) {
            $i++;
            $lines[$i] = fgets($handle,1024);
            $pair = explode("=", $lines[$i]);
            if ($pair[0] == "ADMIN") {
                $x++;
                if ($x == $admin_id) {
                    $lines[$i] = "D\n";
                }
            }
        }
        fclose($handle);
        // write a new config file from the one in memory
        if (!$handle = @fopen(ABSOLUTEPATH . CONFIGFILE, "w")) displayErrorMessage($phpUA["LANGUAGE"]["ERROR_FILERROR"] . ABSOLUTEPATH . CONFIGFILE);
        for ($i=0; $i<count($lines); $i++) {
            if ( $lines[$i] != "D\n" ) fwrite($handle, $lines[$i]);
        }
        fclose($handle);
        reloadConfig();
        endBenchmark(__FILE__, __FUNCTION__);
    }
    
    reloadConfig();
    endBenchmark(__FILE__, __FUNCTION__);
?>